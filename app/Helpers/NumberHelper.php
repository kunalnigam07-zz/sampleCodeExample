<?php

namespace App\Helpers;

class NumberHelper
{
	public static function friendlyNumber($n, $decimal = 0)
	{
        $number = $n;

		if ($n > 1000000000000) {
            $number = round(($n / 1000000000000), $decimal) . 'T';
        } elseif ($n > 1000000000) {
            $number = round(($n / 1000000000), $decimal) . 'B';
        } elseif ($n > 1000000) {
            $number = round(($n / 1000000), $decimal) . 'M';
        } elseif ($n > 1000) {
            $number = round(($n / 1000), $decimal) . 'K';
        }
       
        return $number;
	}

    public static function format($n, $decimal = 0)
    {
        return number_format($n, $decimal, '.', ',');
    }

    public static function money($n)
    {
        return self::format($n, 2);
    }

    public static function moneySimple($n)
    {
        if (($n * 100) % 100 == 0) {
            return self::format($n, 0);
        } else {
            return self::money($n);
        }
    }

    public static function leastSquareFit($values) 
    {
        $x_sum = array_sum(array_keys($values));
        $y_sum = array_sum($values);
        $meanX = $x_sum / count($values);
        $meanY = $y_sum / count($values);

        $mBase = $mDivisor = 0.0;
        foreach ($values as $i => $value) {
            $mBase += ($i - $meanX) * ($value - $meanY);
            $mDivisor += ($i - $meanX) * ($i - $meanX);
        }

        $slope = $mBase / $mDivisor;

        return $slope;
    }
}
