<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Türkçe ay isimleri (tam hali)
     */
    private static $turkishMonths = [
        'Jan' => 'Ocak',
        'Feb' => 'Şubat',
        'Mar' => 'Mart',
        'Apr' => 'Nisan',
        'May' => 'Mayıs',
        'Jun' => 'Haziran',
        'Jul' => 'Temmuz',
        'Aug' => 'Ağustos',
        'Sep' => 'Eylül',
        'Oct' => 'Ekim',
        'Nov' => 'Kasım',
        'Dec' => 'Aralık',
    ];

    /**
     * Türkçe gün isimleri
     */
    private static $turkishDays = [
        'Monday' => 'Pazartesi',
        'Tuesday' => 'Salı',
        'Wednesday' => 'Çarşamba',
        'Thursday' => 'Perşembe',
        'Friday' => 'Cuma',
        'Saturday' => 'Cumartesi',
        'Sunday' => 'Pazar',
    ];

    /**
     * Tarihi Türkçe format ile döndürür
     */
    public static function formatTurkish($date, $format = 'd M')
    {
        if (!$date instanceof Carbon) {
            $date = Carbon::parse($date);
        }

        $formatted = $date->format($format);

        // Ay isimlerini çevir
        foreach (self::$turkishMonths as $english => $turkish) {
            $formatted = str_replace($english, $turkish, $formatted);
        }

        return $formatted;
    }

    /**
     * Gün ismini Türkçe olarak döndürür
     */
    public static function getTurkishDayName($date)
    {
        if (!$date instanceof Carbon) {
            $date = Carbon::parse($date);
        }

        $dayName = $date->format('l');
        return self::$turkishDays[$dayName] ?? $dayName;
    }

    /**
     * Tarih ve gün ismini Türkçe olarak döndürür
     */
    public static function formatTurkishWithDay($date, $format = 'd M')
    {
        $formattedDate = self::formatTurkish($date, $format);
        $dayName = self::getTurkishDayName($date);

        return $formattedDate . ' ' . $dayName;
    }
}
