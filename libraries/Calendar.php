<?php

class Calendar
{
    public function getCalendar($date = null)
    {
        $calendar['todays_date'] = date('j M Y', time());
        $calendar['month'] = isset($date) ? date('M', strtotime($date)) : date('M');
        $calendar['year'] = isset($date) ? date('Y', strtotime($date)) : date('Y');
        $calendar['month_year'] = isset($date) ? date('M Y', strtotime($date)) : date('M Y');
        $calendar['timestamp'] = strtotime($calendar['month_year']);
        $calendar['last_month'] = date('M Y', strtotime('-1 month', $calendar['timestamp']));
        $calendar['next_month'] = date('M Y', strtotime('+1 month', $calendar['timestamp']));
        $calendar['day_count'] = date('t', $calendar['timestamp']);
        $calendar['lm_day_count'] = date('t', strtotime($calendar['last_month']));
        $calendar['dow'] = date('w', $calendar['timestamp']);
        $calendar['first_day'] = date('D', strtotime(date('1 M Y')));

        if (isset($date)) {
            $calendar['today'] = date('l', strtotime($date));
        } else {
            $calendar['today'] = date('l', time());
        }

        if (isset($date)) {
            $calendar['date'] =  date('j M Y', strtotime($date));
        } else {
            $calendar['date'] = date('j M Y', time());
        }

        if (isset($date)) {
            $calendar['day'] =  date('j', strtotime($date));
        } else {
            $calendar['day'] = date('j', time());
        }

        return $calendar;
    }
}