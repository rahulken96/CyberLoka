<?php

namespace App\Helpers;

class StringHelper
{
    public static function formattedPhone($phone): string
    {
        $phone = trim($phone);
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        if (substr($phone, 0, 3) === '+62') {
            $phone = '62' . substr($phone, 3);
        }
        $phone = preg_replace('/[^0-9]/', '', $phone);

        return $phone; //62xxxxx
    }

    public static function normalizePhone($phone): string
    {
        $phone = trim($phone);
        if (strpos($phone, '62') === 0) {
            $phone = '0' . substr($phone, 2);
        }
        if (strpos($phone, '+62') === 0) {
            $phone = '0' . substr($phone, 2);
        }
        return $phone; //08xxxxx
    }
}
