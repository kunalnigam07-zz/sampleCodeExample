<?php

namespace App\Services\API;

use Hash;
use Event;
use Validator;
use RouteHelper;
use NetworkHelper;
use App\Models\User;
use App\Models\Country;
use App\Models\Booking;
use App\Events\BookingMade;
use App\Events\MemberJoined;
use GeoIp2\WebService\Client;
use App\Services\Web\ClassService;
use App\Services\Web\SubscribeService;

class MemberService extends APIService
{
    public function __construct(User $model, SubscribeService $subscribe, Country $country, Booking $booking, ClassService $classservice)
    {
        $this->model = $model;
        $this->subscribe = $subscribe;
        $this->country = $country;
        $this->booking = $booking;
        $this->classservice = $classservice;
    }

    public function joinBook($request)
    {
        $response = ['status' => 'success', 'url' => ''];

        $fields = [
            'first-name' => 'required',
            'last-name' => 'required',
            'email' => 'required|email|unique:users,email',
            'age' => 'required',
            'class-id' => 'required'
        ];

        $validator = Validator::make($request->all(), $fields);

        if ($validator->fails()) {
            $response = ['status' => 'error', 'errors' => []];
            $messages = $validator->errors();

            foreach ($fields as $k => $v) {
                if ($messages->has($k)) {
                    $response['errors'][] = ['field' => $k, 'message' => str_replace('-', ' ', $messages->first($k))];
                }
            }

            return response()->json($response, 422);
        } else {
            $settings = $this->getAllSettings();
            $token = str_random(50);

            $member = new User;
            $member->name = $request->get('first-name');
            $member->surname = $request->get('last-name');
            $member->email = $request->get('email');
            $member->dob = '1950-01-01';
            $member->password = Hash::make(str_random(40));
            $member->ip = NetworkHelper::getIP();
            $member->user_type = 3;
            $member->activation_token = $token;

            $client = new Client(config('services.maxmind.uid'), config('services.maxmind.key'));
            try {
                $record = $client->city(NetworkHelper::getIP());

                if (isset($record->location->timeZone)) {
                    $member->timezone = $record->location->timeZone;
                }

                if (isset($record->country->isoCode)) {
                    $country = $this->country->where('code', $record->country->isoCode)->first();

                    if (count($country) == 1) {
                        $member->country_id = $country->id;
                        $member->mobile_country_id = $country->id;
                    }
                }
            } catch (\Exception $e) {
                // Error, ignore MaxMind lookup in this case
            }

            $member->status = 1;
            $member->save();

            $this->subscribe->subscribe($member);

            $response['url'] = RouteHelper::getRoute('web.join.activate', [$token], true);

            //Event::fire(new MemberJoined($member, $settings));

            // -----------------------------------------------------------------------------------------------------------------------------------------------
            // Now book!
            // -----------------------------------------------------------------------------------------------------------------------------------------------

            $this->booking->create([
                'class_id' => $request->get('class-id'),
                'user_id' => $member->id,
                'rating' => 0,
                'comments' => '',
                'order_id' => 0,
                'discount_code_id' => 0,
                'joined_at' => null,
                'refunded_at' => null,
                'notes' => 'Booked via Landing Page.',
                'status' => 1
            ]);

            $details = $this->classservice->getClass($request->get('class-id'));

            // Send email
            Event::fire(new BookingMade($details, $member));

            return response()->json($response);
        }
    }
}
