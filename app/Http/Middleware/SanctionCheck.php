<?php

namespace App\Http\Middleware;

use Cache;
use Closure;
use NetworkHelper;
use App\Models\Country;
use GeoIp2\WebService\Client;

class SanctionCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $visitor_cc = '';
        $sanctioned = [];

        // Get country code
        if ($request->session()->has('Visitor_Country_Code')) {
            $visitor_cc = $request->session()->get('Visitor_Country_Code');
        } else {
            $client = new Client(config('services.maxmind.uid'), config('services.maxmind.key'));
            try {
                $record = $client->city(NetworkHelper::getIP());

                if (isset($record->country->isoCode)) {
                    $visitor_cc = $record->country->isoCode;
                    $request->session()->put('Visitor_Country_Code', $visitor_cc); // Store for next request
                }

                if (isset($record->location->timeZone)) {
                    $request->session()->put('Visitor_Timezone', $record->location->timeZone . '');
                }
            } catch (\Exception $e) {
                // Error, ignore MaxMind lookup in this case
            }
        }

        // Get santioned countries
        if (Cache::has('Countries.Records.Sanctioned')) {
            $sanctioned = Cache::get('Countries.Records.Sanctioned');
        } else {
            $results = Country::where('sanctioned', 1)->get();
            $sanctioned = [];

            foreach ($results as $result) {
                $sanctioned[] = $result->code;
            }

            Cache::put('Countries.Records.Sanctioned', $sanctioned, 5);
        }

        if (in_array($visitor_cc, $sanctioned)) {
            echo 'Sorry, we\'re unable to support your request right now.';
            die();
        }

        return $next($request);
    }
}
