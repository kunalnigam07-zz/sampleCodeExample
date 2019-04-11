<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use App\Models\Currency;
use Illuminate\Console\Command;

class UpdateCurrencies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currencies:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all currency exchange rates.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Currency $currency)
    {
        parent::__construct();

        $this->currency = $currency;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Get all currency rates EXCEPT for GBP
        $currencies = $this->currency->where('id', '>', 1)->get();
        $currencies_array = [];
        foreach ($currencies as $currency) {
            $currencies_array[] = $currency->code;
        }
        $currencies_string = implode(',', $currencies_array);

        $client = new Client;
        $response = $client->get('https://apilayer.net/api/live?access_key=' . config('services.currencylayer.key') . '&currencies=' . $currencies_string . '&source=GBP&format=1');
        $resp = json_decode((string)$response->getBody());

        if (is_object($resp->quotes)) {
            foreach ($currencies_array as $c) {
                $rate = $resp->quotes->{'GBP' . $c};
                $this->currency->where('code', $c)->update(['rate' => $rate]);
            }
        }
    }
}
