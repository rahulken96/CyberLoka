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

    /**
     * Format atau sanitasi angka dari input FE.
     *
     * @param mixed $string   Input angka (bisa "Rp 155.000", "155,000", atau integer biasa)
     * @param bool  $withRp   Tambahkan prefix "Rp " (hanya berlaku jika $display = true)
     * @param bool  $display  true  → string berformat untuk tampilan ("155.000" / "Rp 155.000")
     *                        false → angka murni int|float untuk disimpan ke DB (155000)
     */
    public static function numberFormat($string, bool $withRp = false, bool $display = true): int|float|string|null
    {
        // Jika sudah bertipe numerik murni, skip proses cleaning
        if (!is_int($string) && !is_float($string)) {
            if (is_null($string) || $string === '') {
                if (!$display) return null;
                return $withRp ? 'Rp 0' : '0';
            }

            $string = (string) $string;

            // Bersihkan simbol Rp dan spasi
            $string = str_ireplace(['Rp.', 'Rp', 'rp.', 'rp', ' '], '', $string);
            $string = trim($string);

            if (strpos($string, '.') !== false && strpos($string, ',') !== false) {
                // Format Indonesia dengan desimal: 1.500.000,50
                $string = str_replace('.', '', $string);
                $string = str_replace(',', '.', $string);
            } elseif (strpos($string, '.') !== false) {
                $parts    = explode('.', $string);
                $lastPart = end($parts);
                if (strlen($lastPart) === 3) {
                    // Ribuan: 155.000 -> 155000
                    $string = str_replace('.', '', $string);
                }
                // else: desimal biasa 155.50 -> biarkan
            } elseif (strpos($string, ',') !== false) {
                $parts    = explode(',', $string);
                $lastPart = end($parts);
                if (strlen($lastPart) === 3) {
                    // Ribuan: 155,000 -> 155000
                    $string = str_replace(',', '', $string);
                } else {
                    // Desimal: 155,50 -> 155.50
                    $string = str_replace(',', '.', $string);
                }
            }

            if (!is_numeric($string)) {
                if (!$display) return null;
                return $withRp ? 'Rp 0' : '0';
            }

            $string = str_contains($string, '.') ? (float) $string : (int) $string;
        }

        // Kembalikan angka murni untuk penyimpanan ke DB
        if (!$display) return $string;

        // Kembalikan string berformat untuk tampilan
        $result = number_format((float) $string, 0, ',', '.');
        return $withRp ? 'Rp ' . $result : $result;
    }
}
