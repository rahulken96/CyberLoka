<?php

namespace App\Helpers;

use Carbon\Carbon;

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

    public static function dateTranslatedFormat(string $date, string $format = 'Y-m-d H:i:s'): ?string
    {
        if (empty($date)) return null;
        Carbon::setLocale('id');
        return Carbon::parse($date)->translatedFormat($format);
    }

    public static function numberFormat($string, bool $withRp = false): ?string
    {
        if (is_string($string)) {
            // Bersihkan simbol Rp, titik setelah Rp, dan spasi
            $string = str_ireplace(['Rp.', 'Rp', 'rp.', 'rp', ' '], '', $string);
            
            // Deteksi format pemisah ribuan dan desimal
            if (strpos($string, '.') !== false && strpos($string, ',') !== false) {
                // Format Indonesia: 1.500.000,50 -> hapus titik ribuan, ubah koma desimal ke titik desimal
                $string = str_replace('.', '', $string);
                $string = str_replace(',', '.', $string);
            } elseif (strpos($string, '.') !== false) {
                // Hanya ada titik. Cek apakah ini desimal (misal 150.50) atau ribuan (misal 150.000)
                $parts = explode('.', $string);
                $lastPart = end($parts);
                if (strlen($lastPart) === 3) {
                    // Jika 3 digit di belakang titik, berarti ribuan -> hapus semua titik
                    $string = str_replace('.', '', $string);
                }
            } elseif (strpos($string, ',') !== false) {
                // Hanya ada koma. Cek apakah ini ribuan (misal 1,500,000) atau desimal (misal 150,50)
                $parts = explode(',', $string);
                $lastPart = end($parts);
                if (strlen($lastPart) === 3) {
                    // Ribuan -> hapus koma
                    $string = str_replace(',', '', $string);
                } else {
                    // Desimal -> ubah koma ke titik desimal
                    $string = str_replace(',', '.', $string);
                }
            }
        }

        if (empty($string) || !is_numeric($string)) {
            return $withRp ? 'Rp 0' : '0';
        }

        $result = number_format((float)$string, 0, ',', '.');
        if ($withRp) {
            return 'Rp ' . $result;
        }
        return $result;
    }
}
