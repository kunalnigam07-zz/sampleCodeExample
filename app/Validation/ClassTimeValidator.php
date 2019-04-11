<?php

namespace App\Validation;

use Auth;
use Illuminate\Validation\Validator as IlluminateValidator;

class ClassTimeValidator
{
    const WAIT_TIME_MINUTES = 5;

    public static function create()
    {
        return function($attribute, $value, $parameters, IlluminateValidator $validator) {
            $data = $validator->getData();

            $now = (new \DateTime())->setTimezone(new \DateTimeZone('UTC'));

            $classDateTime = self::createClassDateTime($data, str_replace(' ', '', $value));
            if ($classDateTime === null) {
                return true;
            }
            $classDateTime->setTimezone(new \DateTimeZone('UTC'));

            return $classDateTime->getTimestamp() > ($now->getTimestamp() + self::WAIT_TIME_MINUTES * 60);
        };
    }

    public static function message(): string
    {
        return sprintf(
            'Start time needs to be at least %d mins from now',
            ClassTimeValidator::WAIT_TIME_MINUTES
        );
    }

    protected static function createClassDateTime(array $data, string $value): ?\DateTime
    {
        $date = null;

        switch ($data['date_option'] ?? null) {
            case 's':
            case null:
                $date = "{$data['date_year']}-{$data['date_month']}-{$data['date_day']}";
                break;
            case 'r':
                $date = "{$data['start_year']}-{$data['start_month']}-{$data['start_day']}";
                break;
        }

        if ($date === null) {
            return null;
        }

        $res = \DateTime::createFromFormat(
            'Y-m-d H:i',
            "$date $value",
            new \DateTimeZone(Auth::user()->timezone)
        );

        if ($res instanceof \DateTime) {
            return $res;
        }

        return null;
    }
}
