<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 5/26/2016
 * Time: 10:31 AM
 */

namespace App\Repositories;

use Bosnadev\Repositories\Eloquent\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;


class CompanySubscriptionPlanRepository extends Repository

{
    public function model()
    {
        return 'App\Models\CompanySubscriptionPlan';
    }

    public function getCompanySubscriptionPlans($company_id)
    {
        return $this->model
                    ->where('company_id', $company_id)
                    ->get();
    }


    public function getCurrentActivePlanByCompany($company_id) {

        $date = date('Y-m-d');
        return $this->model
            ->join('master_subscriptions', 'master_subscriptions.id', '=', 'company_subscription_plans.master_subscription_id')
            ->where('company_id', $company_id)
            ->where('active', 1)
            ->whereDate('end_date', '>=', $date)
            ->whereDate('start_date', '<=', $date)
            ->select(
                array(

                    DB::raw('CONCAT(master_subscriptions.name, " $", master_subscriptions.amount) as plan_name'),
                    DB::raw('CONCAT(master_subscriptions.id, "") as plan_id'),
                    DB::raw('DATE_FORMAT(company_subscription_plans.start_date, "%m/%d/%Y") as plan_start_date') ,
                    DB::raw('DATE_FORMAT(company_subscription_plans.end_date, "%m/%d/%Y") as plan_end_date') ,
                    DB::raw('DATE_FORMAT(company_subscription_plans.due_date, "%m/%d/%Y") as plan_next_due_date') ,
                    'company_subscription_plans.due_date as due_date',
                    DB::raw('CONCAT(master_subscriptions.validity_period_id, "") as validity_period_id'),
                    DB::raw('CONCAT(company_subscription_plans.id, "") as current_subscription_plan_id'),
                    DB::raw('CONCAT(company_subscription_plans.coupon_referral_id, "") as coupon_referral_id'),
                )
            )
            ->first();
    }

    /**
     * Return next subscription plan which will activated as the next subscription plan
     * @param $company_id
     * @param $dateString
     * @param $currentPlanId
     * @return
     */
    public function getNextSubscriptionPlan($company_id, $dateString, $currentPlanId) {
        return $this->model->join('master_subscriptions', 'master_subscriptions.id', '=', 'company_subscription_plans.master_subscription_id')
            ->where('company_id', $company_id)
            ->where('company_subscription_plans.active', 1)
            ->where('company_subscription_plans.id', '!=', $currentPlanId)
            ->whereDate('start_date', '>=', $dateString)
            ->select(
                array(
                    DB::raw('CONCAT(master_subscriptions.name, " $", master_subscriptions.amount) as plan_name'),
                    DB::raw('CONCAT(master_subscriptions.id, "") as plan_id'),
                    DB::raw('DATE_FORMAT(company_subscription_plans.start_date, "%m/%d/%Y") as plan_start_date') ,
                    DB::raw('DATE_FORMAT(company_subscription_plans.end_date, "%m/%d/%Y") as plan_end_date') ,
                    DB::raw('CONCAT(company_subscription_plans.id, "") as subscription_plan_id'),
                )
            )
            ->first();
    }

    /**
     * Get next subscriptions that are available to charge
     * @param $company_id
     * @param bool $date
     * @return mixed
     */
    public function getAllSubscriptionToBeCharged($company_id, $date=false) {
        // if date not specified set to current date
        if (!$date) {
            $date = date('Y-m-d');
        }
//        return $this->model()->join('master_subscriptions', 'master_subscriptions.id', '=', 'company_subscription_plans.master_subscription_id')
//            ->where('company_id', $company_id)
//            ->where('active', 1)
//            ->whereDate('end_date', '>', date('Y-m-d'))
//            ->whereDate('due_date', '=', date('Y-m-d'))
//            ->select('company_subscription_plans.*', 'master_subscriptions.amount')
//            ->get();

        return $this->model->join('master_subscriptions', 'master_subscriptions.id', '=', 'company_subscription_plans.master_subscription_id')
            ->where('company_id', $company_id)
            ->where('active', 1)
//            ->whereDate('end_date', '<', date($date))
            ->whereDate('due_date', '=', date($date))
            ->select('company_subscription_plans.*', 'master_subscriptions.amount',  'master_subscriptions.validity_period_id')
            ->get();
    }

    /**
     * Disable all active plan in particular company
     * @param $company_id
     * @return mixed
     */
    public function disableAllActiveSubscriptions($company_id) {

        return $this->model
            ->where('company_id', $company_id)
            ->update(array('active' => 0));
    }

    /**
     * Returns current active plan by date
     * @param $company_id
     * @param bool $date
     * @return mixed
     */
    public function getCurrentActiveSubscriptionPlanByDate($company_id, $date=false) {
        // if date not specified set to current date
        if (!$date) {
            $date = date('Y-m-d');
        }

        return $this->model->join('master_subscriptions', 'master_subscriptions.id', '=', 'company_subscription_plans.master_subscription_id')
            ->where('active', 1)
            ->where('company_id', $company_id)
            ->whereDate('end_date', '>=', $date)
            ->whereDate('start_date', '<=', $date)
            ->select('company_subscription_plans.*', 'master_subscriptions.amount', 'master_subscriptions.validity_period_id')
            ->first();
    }

    /**
     * Returns subscription plan data that are ready to be saved on
     * `company_subscription_plans` table.
     *
     * @param $master_subscription_id
     * @param $company_id
     * @return array
     */
    public function getSubscriptionPlanParams($master_subscription_id, $company_id,$startDate=false, $setDueDate=false,$coupon_id=false,$is_referral=1) {

        if (!$startDate) {
            $startDate  = date("Y/m/d");
        }

        $dueDate    = $this->getNextDate($startDate,1);

        // If start date specified make due date same as start date
        if ($setDueDate) {
            $dueDate    = $startDate;
        }

        $subscription_plan  = $this->getSubscriptionFee($master_subscription_id);

        $subscription_fee   = $subscription_plan->amount;
        $subscription_month = $subscription_plan->validity_period_id;
        $endDate            = $this->getNextDate($startDate,$subscription_month, true);

        if($is_referral == 0)
        {
            $coupon_referral_id = 0;
        }
        else
        {
            $coupon_referral_id = $coupon_id;
        }
        $data = array('company_id' => $company_id, 'master_subscription_id' => $master_subscription_id,
            'subscription_fee'=> $subscription_fee, 'start_date'=> $startDate, 'end_date'=> $endDate,'due_date'=> $dueDate, 'active' => 1,'coupon_id'=>$coupon_id,'coupon_referral_id'=>$coupon_referral_id);

        return $data;
    }

    /**
     * get next date passing to number of month
     *
     * @param $toDate
     * @param $month
     * @param bool $withPreviousDay
     * @param bool $baseDate
     * @return bool|string
     * @internal param $date
     */
    public function getNextDate($toDate, $month, $withPreviousDay=false, $baseDate=false)
    {
        $time = strtotime($toDate);
//        $nextDate = date("Y-m-d", strtotime("+{$month} month", $time));
//        $nextMonth = $this->calculate_next_month($time, $month);
        $carbonDate = Carbon::createFromTimestamp($time);
        $nextDate  = $carbonDate->addMonthNoOverflow($month)->toDateString();

        if ($baseDate) {
            \Log::debug("===== baseDate has SET; needs altering");
            $baseDateTime = strtotime($baseDate);
            $baseTimeCarbon = Carbon::createFromTimestamp($baseDateTime);
            $nextDateCarbon = Carbon::createFromTimestamp(strtotime($nextDate));
            \Log::debug("===== baseDate " . $baseTimeCarbon->toDateString());
            \Log::debug("===== baseTimeCarbon->day " . $baseTimeCarbon->day);
            \Log::debug("===== nextDateCarbon->day " . $nextDateCarbon->day);
            \Log::debug("===== nextDate " . $nextDate);
            \Log::debug("===== next month Date " . date('Y-m-t'));
            if ( ($baseTimeCarbon->day > $nextDateCarbon->day) && ($nextDate != date('Y-m-t')) ) {
                $nextDay = date('t');
                \Log::debug("==== next day on date(t) " . $nextDay);
                $dt = Carbon::createFromTimestamp(strtotime($nextDate));

                if (checkdate($dt->month, $baseTimeCarbon->day, $dt->year)) {
                    \Log::debug("==== date check validated!");
                    $ndt = Carbon::create($dt->year, $dt->month, $baseTimeCarbon->day);
                    $nextDate = $ndt->toDateString();
                }

                \Log::debug("===== new date according to baseDate " . $nextDate);
            }
            \Log::debug("===== new date according to baseDate " . $nextDate);
        }

        if ($withPreviousDay)  {
            $previousDayDatetime = strtotime($nextDate);
            $previousDate = date("Y-m-d", strtotime("-1 day", $previousDayDatetime));;
            return $previousDate;
        }
        return $nextDate;
    }

    /**
     * Returns master subscription details by id
     * @param $id
     * @return mixed
     */
    public function getSubscriptionFee($id)
    {
        return DB::table('master_subscriptions')
            ->where('id', $id)
            ->where('status', 1)
            ->first();
    }

    /**
     * Returns subscription plan with master details for particular id
     * @param $id
     * @return mixed
     */
    public function findSubscriptionPlanWithMasterSubscription($id) {
        return $this->model->join('master_subscriptions', 'master_subscriptions.id', '=', 'company_subscription_plans.master_subscription_id')
            ->where('active', 1)
            ->where('company_subscription_plans.id', $id)
            ->select(
                array(
                    'company_subscription_plans.*',
                    'master_subscriptions.name',
                    'master_subscriptions.amount',
                    'master_subscriptions.validity_period_id'
                )
            )->first();
    }

    public function getChargeCountFromSubscriptionPlan($id) {
        return $this->model->leftJoin('company_subscriptions', 'company_subscriptions.company_subscription_plan_id', '=', 'company_subscription_plans.id')
            ->join('master_subscriptions', 'master_subscriptions.id', '=', 'company_subscription_plans.master_subscription_id')
            ->where('company_subscription_plans.id', $id)
            ->select (
                array(
                    DB::raw('COUNT(company_subscriptions.id) as total_charge_count'),
                    'master_subscriptions.validity_period_id',
                    'company_subscription_plans.subscription_fee'
                )
            )
//            ->groupBy('company_subscriptions.id')
            ->first();


//        return $this->model->leftJoin('company_subscriptions', 'company_subscriptions.company_subscription_plan_id', '=', 'company_subscription_plans.id')->join('master_subscriptions', 'master_subscriptions.id', '=', 'company_subscription_plans.master_subscription_id')->where('company_subscription_plans.id', 14)->select(array(DB::raw('COUNT(company_subscriptions.id) as total_charge_count'), 'master_subscriptions.validity_period_id', 'company_subscription_plans.subscription_fee'))->groupBy('company_subscriptions.id')->first();
    }

    /**
     * Compare future date with current date
     * dueDate (Future date)
     * endDate (Current date)
     * @param $dueDate
     * @param $endDate
     * @return bool
     */
    public function isEndDateExpired($dueDate, $endDate) {

        \Log::debug("=== dueDate : " . $dueDate);
        \Log::debug("=== endDate : " . $endDate);

        $date1 = new \DateTime($endDate);
        $date2 = new \DateTime($dueDate);
        return ($date1 < $date2);
    }

    /**
     * Disable subscription plan
     * @param $id
     * @return mixed
     * @internal param $company_id
     */
    public function disableSubscriptionsSubscriptionPlan($id) {

        return $this->model
            ->where('id', $id)
            ->update(array('active' => 0));
    }

    /**
     * Function to calculate the same day one month in the future.
     *
     * This is necessary because some months don't have 29, 30, or 31 days. If the
     * next month does not have as many days as this month, the anniversary will be
     * moved up to the last day of the next month.
     *
     * @param bool $start_date (optional)
     *   UNIX timestamp of the date from which you'd like to start. If not given,
     *   will default to current time.
     * @param int $numberOfMonths
     * @return int $timestamp
     *   UNIX timestamp of the same day or last day of next month.
     */
    function calculate_next_month($start_date = FALSE, $numberOfMonths=1)
    {
        if ($start_date) {
            $now = $start_date; // Use supplied start date.
        } else {
            $now = time(); // Use current time.
        }

        // Get the current month (as integer).
        $current_month = date('n', $now);

        // If the we're in Dec (12), set current month to Jan (1), add 1 to year.
        if ($current_month == 12) {
            $next_month = $numberOfMonths;
            $plus_one_month = mktime(0, 0, 0, $numberOfMonths, date('d', $now), date('Y', $now) + 1);
        } // Otherwise, add a month to the next month and calculate the date.
        else {
            $next_month = $current_month + $numberOfMonths;
            $plus_one_month = mktime(0, 0, 0, date('m', $now) + $numberOfMonths, date('d', $now), date('Y', $now));
        }

        $i = 1;
        // Go back a day at a time until we get the last day next month.
        while (date('n', $plus_one_month) != $next_month) {
            $plus_one_month = mktime(0, 0, 0, date('m', $now) + 1, date('d', $now) - $i, date('Y', $now));
            $i++;
        }

        return $plus_one_month;
    }

    public function disableSubscriptionsPlan($id) {

        return $this->model
            ->where('company_id', $id)
            ->update(array('active' => 0));
    }
}