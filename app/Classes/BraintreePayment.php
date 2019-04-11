<?php

namespace App\Classes;

use Carbon;
use Braintree;
use DateHelper;
use AuthHelper;
use ClassHelper;
use App\Models\User;
use App\Models\Order;
use App\Models\Currency;
use App\Models\ClassEvent;
use App\Models\BulkPackage;

class BraintreePayment
{
    public function __construct()
    {
        $env = config('services.braintree.environment');

        Braintree\Configuration::environment($env);
        Braintree\Configuration::merchantId(config('services.braintree.' . $env . '.merchant_id'));
        Braintree\Configuration::publicKey(config('services.braintree.' . $env . '.public_key'));
        Braintree\Configuration::privateKey(config('services.braintree.' . $env . '.private_key'));

        $this->my_id = AuthHelper::id();
    }

    public function generateOrderNumber()
    {
        $order_number = '';

        do {
            $order_number = date('ymd') . '-' . mt_rand(100000, 999999) . '-' . mt_rand(100000, 999999);
            $cnt = Order::where('order_number', $order_number)->count();
        } while ($cnt > 0);

        return $order_number;
    }

    public function generateToken()
    {
        if (strlen(AuthHelper::user()->braintree_id) > 0) {
            return Braintree\ClientToken::generate([
                'customerId' => AuthHelper::user()->braintree_id
            ]);
        } else {
            return Braintree\ClientToken::generate();
        }
    }

    public function sale($request)
    {
        $order_id = 0;
        $price = 0;
        $order_brief = '';
        $nonce = $request->get('payment_method_nonce');
        $currency = $request->get('currency');
        $class_id = $request->get('class_id');
        $bulk_id = $request->get('cart_option');
        $order_number = $this->generateOrderNumber();
        $store_details = $request->has('store_details');

        // Get class and instructor
        $class = ClassEvent::where('id', $class_id)->where('status', 1)->where('published', 1)->firstOrFail();
        $instructor = User::instructors()->where('id', $class->user_id)->where('status', 1)->firstOrFail();
        
        // Get all currencies and perform transformation on price
        $currencies = [];
        $data = Currency::where('status', 1)->orderBy('ordering', 'ASC')->get();
        foreach ($data as $v) {
            $currencies[$v->code] = $v;
        }

        // Get single class price OR bulk price
        if ($bulk_id > 0) {
            $bulk_package = BulkPackage::where('id', $bulk_id)
                ->where('user_id', $class->user_id)
                ->where('price', '>', 0)
                ->where('classes_number', '>', 0)
                ->firstOrFail();

            $price = $bulk_package->price;
            $expiry = Carbon::now()->addDays($bulk_package->expiry_days);
            $order_brief = 'Bulk Classes (' . $bulk_package->classes_number . ') - ' . $instructor->name . ' ' . $instructor->surname . ' - Expires: ' . DateHelper::showDate($expiry, 'd F Y');
        } else {
            $price = $class->price;
            $order_brief = $class->title . ' - ' . $instructor->name . ' ' . $instructor->surname . ' - ' . DateHelper::showDate($class->class_at, 'd F Y');
        }

        // Transform price (if applicable)
        $costing = ClassHelper::costCurrency($price, $currency, $currencies);

        $result = Braintree\Transaction::sale([
            'amount' => $costing[2],
            'paymentMethodNonce' => $nonce,
            'orderId' => $order_number,
            'merchantAccountId' => $currencies[$currency]->merchant_id,
            'options' => [
                'submitForSettlement' => true,
                'storeInVaultOnSuccess' => $store_details,
            ]
        ]);
        
        if ($result->success) {
            $member = AuthHelper::user();

            if ($store_details) {
                $member->braintree_id = $result->transaction->customer['id'];
            } else {
                // If don't store and have previous ID, remove this from vault
                if (strlen($member->braintree_id) > 0) {
                    // Braintree action to remove vault client...
                    Braintree\Customer::delete($member->braintree_id);
                }

                $member->braintree_id = '';
            }
            
            $member->save();

            $order = Order::create([
                'order_number' => $order_number, 
                'full_name' => $member->name . ' ' . $member->surname, 
                'mobile' => $member->mobile, 
                'country' => $member->country->title, 
                'email' => $member->email, 
                'title' => $order_brief,
                'price' => $price, 
                'foreign_price' => $currency != 'GBP' ? $costing[2] : 0,
                'foreign_currency' => $currency != 'GBP' ? $costing[1] : '',
                'class_id' => $bulk_id > 0 ? 0 : $class->id,
                'bulk_package_id' => $bulk_id > 0 ? $bulk_id : 0,
                'bulk_qty' => $bulk_id > 0 ? $bulk_package->classes_number : 0, 
                'bulk_type' => $bulk_id > 0 ? $bulk_package->type : 0, 
                'bulk_instructor_id' => $bulk_id > 0 ? $bulk_package->user_id : 0, 
                'bulk_expires_at' => $bulk_id > 0 ? $expiry : null, 
                'user_id' => $member->id, 
                'status_id' => 2, 
                'gateway_response' => print_r($result, true), 
                'status' => 1
            ]);

            $order_id = $order->id;
        }
        
        return $order_id;
    }
}
