<?php namespace App\Lib;

/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 6/3/2016
 * Time: 12:38 PM
 */
class ProrateDayCalculation
{
    public function dayCalculation($date)
    {
        $date = new \DateTime($date);
        $days = $date->format('d');
        $month = $date->format('m');
        $year = $date->format('Y');

        $timestamp = \strtotime($date->format('Y-m-d'));
        $daysRemaining = (int)date('t', $timestamp) - (int)date('j', $timestamp);
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $data = array('days_remaining' => intval($daysRemaining+ 1), 'days_in_month' => $days_in_month);
        return $data;
    }

    /**
     * Returns number of days remaining for particular date
     * @param bool $dateString
     * @return int
     * @internal param bool $date
     */
    public function getDaysRemaining($dateString=false) {
        $date1 = new \DateTime();  //current date or any date
        $date2 = new \DateTime($dateString);   //Future date
        $diff = $date2->diff($date1);  //find difference
        $days = $diff->days;   //rounding days
        return $days + 1;
    }
}