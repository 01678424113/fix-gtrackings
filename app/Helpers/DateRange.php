<?php

namespace App\Helpers;

class DateRange {

    public static function get($start, $end) {
        $start_time = strtotime(date_format(date_create_from_format('ymd', $start), 'Y-m-d'));
        $end_time = strtotime(date_format(date_create_from_format('ymd', $end), 'Y-m-d'));
        $dates = [$end];
        $number = ($end_time - $start_time) / (60 * 60 * 24);
        if ($number >= 0) {
            for ($i = 1; $i <= $number; $i++) {
                array_push($dates, date("ymd", strtotime('-' . $i . ' days', $end_time)));
            }
        }
        return $dates;
    }

}
