<?php

namespace App\Console\Commands;

use App\Repositories\CompanyLocationLicenseRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\CompanySubscriptionPlanRepository;
use App\Repositories\CompanySubscriptionRepository;
use App\Repositories\CouponsRepository;
use App\Repositories\MasterReferralsRepository;
use App\Repositories\MasterSubscriptionRepository;
use App\Repositories\MasterUserRepository;
use App\Repositories\PaymentRepository;
use Cartalyst\Stripe\Exception\CardErrorException;
use Cartalyst\Stripe\Exception\NotFoundException;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use Illuminate\Console\Command;
use DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use App\Lib\sendMail;

class SubscriptionRenew extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:renew
                        {customDate? : (optional) The date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Renew subscription';

    protected $company;
    protected $masterSubscription;
    protected $master_data;
    protected $payment;
    protected $companyLicense;
    protected $company_subscription;
    protected $company_subscription_plan;
    protected $coupons;
    protected $masterReferrals;

    /**
     * Create a new command instance.
     *
     * @param CompanyRepository $company
     * @param MasterSubscriptionRepository $masterSubscription
     * @param MasterUserRepository $master_data
     * @param PaymentRepository $payment
     * @param CompanyLocationLicenseRepository $companyLicense
     * @param CompanySubscriptionRepository $company_subscription
     * @param CompanySubscriptionPlanRepository $company_subscription_plan
     * @param CouponsRepository $coupons
     * @param MasterReferralsRepository $masterReferrals
     */
    public function __construct(CompanyRepository $company,
                                MasterSubscriptionRepository $masterSubscription,
                                MasterUserRepository $master_data,
                                PaymentRepository $payment,
                                CompanyLocationLicenseRepository $companyLicense,
                                CompanySubscriptionRepository $company_subscription,
                                CompanySubscriptionPlanRepository $company_subscription_plan,
                                CouponsRepository $coupons,
                                MasterReferralsRepository $masterReferrals)
    {
        parent::__construct();
        $this->company = $company;
        $this->masterSubscription = $masterSubscription;
        $this->master_data = $master_data;
        $this->payment = $payment;
        $this->companyLicense = $companyLicense;
        $this->company_subscription = $company_subscription;
        $this->company_subscription_plan = $company_subscription_plan;
        $this->coupons = $coupons;
        $this->masterReferrals = $masterReferrals;
        $this->stripe = Stripe::make(Config::get('simplifya.STRIPE_KEY'));
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Log::debug("==== Subscription scheduler");
        $currentDate = date('Y-m-d');
        if ($customDate = $this->argument('customDate')) {
            $currentDate  = $customDate;
        }
        \Log::debug("Syncing date $currentDate");
        $companies = $this->company->getAllActiveAndAbleToChargeCompanies();
        \Log::debug("all companies : " . $companies->count());
        $this->updateSubscriptionPlanDueDates($companies, $currentDate);
        $this->chargeSubscription($companies, $currentDate);
    }

    /**
     * Check current plan is expiring on due date
     * if so create new monthly plan and de-activate the current plan
     * @param Collection $companies
     * @param $currentDate
     */
    private function updateSubscriptionPlanDueDates(Collection $companies, $currentDate) {
        foreach($companies as $company) {
            if($company->entity_type == Config::get('simplifya.MarijuanaBusiness')) {
                //todo add new monthly plan
                $monthlyPlan = $this->masterSubscription->getMonthlySubscriptionByEntity($company->entity_type, 1);
                \Log::debug("==== company id " . $company->id);
                $subscriptionPlans = $this->company_subscription_plan
                    ->getAllSubscriptionToBeCharged($company->id, $currentDate);
                if (count($subscriptionPlans->toArray()) == 1) {
                    \Log::debug("==== only one subscription plan exists");
                    $subscriptionPlan = $subscriptionPlans->first();
                    $isEndDateExpired = $this->company_subscription_plan->isEndDateExpired($subscriptionPlan->due_date, $subscriptionPlan->end_date);
                    if ($isEndDateExpired) {
                        \Log::debug("=== end date expired");
                        //de-activate current
                        $this->company_subscription_plan->disableSubscriptionsSubscriptionPlan($subscriptionPlan->id);
                        \Log::debug("=== plan " . print_r($monthlyPlan->toArray(), true));
                        $plansParam = $this->company_subscription_plan->getSubscriptionPlanParams($monthlyPlan->id, $company->id, $subscriptionPlan->due_date, true);
                        \Log::debug("=== plan param" . print_r($plansParam, true));
                        \Log::debug("=== create new sub plan");

                        if (isset($subscriptionPlan->coupon_referral_id)) {
                            $plansParam['coupon_referral_id'] = $subscriptionPlan->coupon_referral_id;
                        }

                        $this->company_subscription_plan->create($plansParam);
                        \Log::debug("=== create new sub plan done!");
                    }
                }else {
                    \Log::debug("=== having more than one subscription plan in the table");
                    foreach ($subscriptionPlans as $subscriptionPlan) {
                        $isEndDateExpired = $this->company_subscription_plan->isEndDateExpired($subscriptionPlan->due_date, $subscriptionPlan->end_date);
                        if ($isEndDateExpired) {
                            \Log::debug("=== end date expired");
                            $this->company_subscription_plan->disableSubscriptionsSubscriptionPlan($subscriptionPlan->id);
                        }
                    }
                }
            }
        }
    }

    private function chargeSubscription($companies, $currentDate) {
        foreach($companies as $company) {
            if($company->entity_type == Config::get('simplifya.MarijuanaBusiness')) {
                $currentSubscriptionPlan = $this->company_subscription_plan->getCurrentActiveSubscriptionPlanByDate($company->id, $currentDate);
                $licenseCount = (int) $this->companyLicense->getActiveLicenseCountByCompanyId($company->id);
                if (isset($currentSubscriptionPlan)) {
                    if ($currentDate === $currentSubscriptionPlan->due_date) {
                        \Log::debug("current subscription plan " . print_r($currentSubscriptionPlan->toArray(), true));
                        $fee = $this->calculateChargeAmount($company->id, $currentSubscriptionPlan->id);
                        $discount = 0;
                        $commission = 0;
                        //todo check discounts
                        if ($currentSubscriptionPlan->coupon_id != 0) {
                            $coupon = $this->coupons->find($currentSubscriptionPlan->coupon_id);
                            if ($coupon->type == 'referral'){
                                $couponDetails = $this->coupons->getDiscount($coupon->code, 0);
                            }else {
                                $couponDetails = $this->coupons->getDiscount($coupon->code, $currentSubscriptionPlan->master_subscription_id);
                            }
                            $chargeCount = $this->company_subscription_plan->getChargeCountFromSubscriptionPlan($currentSubscriptionPlan->id);
                            $currentCoupon = $this->coupons->getDiscountByOrder($couponDetails, $fee, ($chargeCount->total_charge_count + 1), $licenseCount);

                            if ($coupon->type == 'referral') {
                                $currentCoupon = $this->coupons->getDiscountByOrder($couponDetails, $fee, 1, $licenseCount);
                                \Log::debug("==== current coupon type is referral");
                                \Log::debug("==== current referral coupon details " . print_r($currentCoupon, true));
                                /*
                                 * check company commission_end_date
                                 * if commission_end_date > $currentDate
                                 * then referral_commission field in company_subscription table should update !
                                 */
//                                if (isset($company->commission_end_date) && $company->commission_end_date != '0000-00-00') {
//                                    $isCommissionPeriodExpired = $this->company_subscription_plan->isEndDateExpired($company->commission_end_date, $currentDate);
//                                    if (!$isCommissionPeriodExpired) {
//                                        \Log::debug("current master subscription id " . $currentSubscriptionPlan->master_subscription_id);
//                                        $commission = $this->masterReferrals->getReferralCommission($coupon->id, $currentSubscriptionPlan->master_subscription_id, $fee);
//                                        \Log::debug("Commission calculated amount is : " . $commission);
//                                    }
//                                }
                            }
                            $newFee = $fee - $currentCoupon['discount'];
                            \Log::debug("new discounted fee " . $newFee);
                            \Log::debug("current discount " . $currentCoupon['discount']);
                            $discount = $currentCoupon['discount'];
                            if ($newFee > 0) {
                                $fee = $fee - $currentCoupon['discount'];
                            }else {
                                $fee = 0;
                            }
                        }
                        if ($currentSubscriptionPlan->coupon_referral_id != 0) {
                            \Log::debug("************************************ coupon_referral_id not empty!");
                            $coupon = $this->coupons->find($currentSubscriptionPlan->coupon_referral_id);
                            if ($coupon->type == 'referral') {
                                \Log::debug("************************************ coupon type is referral !");
                                /*
                                 * check company commission_end_date
                                 * if commission_end_date > $currentDate
                                 * then referral_commission field in company_subscription table should update !
                                 */
                                if (isset($company->commission_end_date) && $company->commission_end_date != '0000-00-00') {
                                    \Log::debug("************************************ company date not empty!");
                                    $isCommissionPeriodExpired = $this->company_subscription_plan->isEndDateExpired($currentDate, $company->commission_end_date);
                                    if ($isCommissionPeriodExpired) {
                                        \Log::debug("Commission period expire true");
                                    }else {
                                        \Log::debug("Commission period expire false");
                                    }
                                    if (!$isCommissionPeriodExpired) {
                                        \Log::debug("subs fee: " . $fee);
//                                        $commission = $this->masterReferrals->getReferralCommission($coupon->id, $currentSubscriptionPlan->validity_period_id, $fee);
                                        $commission = $this->masterReferrals->getReferralCommission($coupon->id, $currentSubscriptionPlan->master_subscription_id, $fee);
                                        \Log::debug("Commission calculated amount is : " . $commission);
                                    }else {
                                        \Log::debug("************************************ company comission period expired!");
                                    }
                                }
                            }
                        }

                        $users = $this->company->getAdminUser($company->id, 2);
                        $currency = 'USD';
                        $this->stripePayments($users, $company->stripe_id, $currency, $fee, $company->id, $currentSubscriptionPlan->id, $currentSubscriptionPlan->due_date, 1,$company->foc, $discount, $commission);
                    }
                }
            }
        }
    }

    /**
     * Calculate subscription charge amount for particular company
     * based on current subscription plan
     * @param $companyId
     * @param $currentSubscriptionPlanId
     * @return int
     * @internal param $currentPlanId
     */
    private function calculateChargeAmount($companyId, $currentSubscriptionPlanId) {
        // fetch all licenses including inactive once
        $licenseCount = (int) $this->companyLicense->getActiveLicenseCountByCompanyId($companyId);
        // fetch how many months got charged out of plan validity period
        $chargeSummary = $this->company_subscription_plan->getChargeCountFromSubscriptionPlan($currentSubscriptionPlanId);

        \Log::debug("=== license count = " . $licenseCount);
        \Log::debug("=== charge summary  " . print_r($chargeSummary->toArray(), true));

        $perLicenseFee = (float) $chargeSummary->subscription_fee;
        $fee = $licenseCount * $perLicenseFee;
        \Log::debug("=== subscription fee to be charged = " . $fee);

        return $fee;
    }

    /**
     * Update next due date on current subscription plan
     * @param $currentSubscriptionPlanId
     * @param $date
     * @param $numberOfMonths number of months needs to added for next due date
     * @return mixed
     */
    private function updateCurrentSubscriptionPlanDueDate($currentSubscriptionPlanId, $date, $numberOfMonths) {
        $currentSubscriptionPlan = $this->company_subscription_plan->find($currentSubscriptionPlanId);

        $nextDueDate = $this->company_subscription_plan->getNextDate($date, $numberOfMonths, false, $currentSubscriptionPlan->start_date);
        return $this->company_subscription_plan->update(['due_date' => $nextDueDate], $currentSubscriptionPlanId);
    }

    private function customerCharges($customer, $currency, $amount)
    {
        try {
            $charge = $this->stripe->charges()->create([
                'customer' => $customer,
                'currency' => $currency,
                'amount'   => $amount,
            ]);

            return array('success' => true, 'charge' => $charge);
        } catch(NotFoundException $e) {
            // Get the status code
            $code = $e->getCode();

            // Get the error message returned by Stripe
            $message = $e->getMessage();

            // Get the error type returned by Stripe
            $type = $e->getErrorType();

            return array('success'=>false, 'code' => $code, 'message' => $message, 'type' => $type);
        } catch (CardErrorException $e) {
            // Get the status code
            $code = $e->getCode();

            // Get the error message returned by Stripe
            $message = $e->getMessage();

            // Get the error type returned by Stripe
            $type = $e->getErrorType();

            return array('success'=>false, 'code' => $code, 'message' => $message, 'type' => $type);
        } catch (\Exception $e) {
            // Get the status code
            $code = $e->getCode();

            // Get the error message returned by Stripe
            $message = $e->getMessage();

            // Get the error type returned by Stripe
            $type = 'unknown';

            return array('success'=>false, 'code' => $code, 'message' => $message, 'type' => $type);
        }

    }

    public function paymentHandler($customer_charge, $subscription_fee, $currency, $company_id, $tx_type, $company_subscription_plan_id=false, $discount = 0.0, $commission=0)
    {
        $master_subscription = $this->company->getSubscriptionType($company_id);
        $payments = array(
            'req_date_time' => Carbon::now(),
            'object'        => $customer_charge['charge']['object'],
            'req_currency'  => $currency,
            'req_amount'    => $subscription_fee,
            'res_date_time' => Carbon::createFromTimestamp($customer_charge['charge']['created']),
            'res_id'        => $customer_charge['charge']['id'],
            'res_currency'  => strtoupper($customer_charge['charge']['currency']),
            'res_amount'    => $customer_charge['charge']['amount']/100,
            'company_id'    => $company_id,
            'tx_type'       => $tx_type,
            'tx_status' => 1,
            'created_by'    => '0'
        );
        $response_payment = $this->payment->create($payments);

        if($response_payment) {
            $company_subscription = array(
                'company_id'            => $company_id,
                'master_subscription_id'=>$master_subscription[0]->id,
                'payment_id'            =>$response_payment->id,
                'created_by'            => 0,
                'amount'                => $subscription_fee,
                'company_subscription_plan_id' => $company_subscription_plan_id,
                'discount'              => $discount,
                'referral_commission'   => $commission,
            );
            $response_subscription = $this->company_subscription->create($company_subscription);
            if($response_subscription) {
                $message = Config::get('messages.PAYMENT_SUCCESSFUL');
                return response()->json(array('success' => 'true', 'message'=> $message));
            }
        }
    }

    private function stripePayments($users, $stripe_id, $currency, $amount, $company_id, $currentSubscriptionPlanId, $date, $numberOfMonths, $foc, $discount = false, $commission=false)
    {
        if ($foc == 0) {
            if ($amount > 0) {
                \Log::debug("==== amount is greater than zero ! : " . $amount);
                \Log::debug("==== discount : " . $discount);
                \Log::debug("==== commission : " . $commission);
                $customer_charge = $this->customerCharges($stripe_id, $currency, $amount);
                if ($customer_charge['success']) {
                    $tx_type = 'subscription';
                    $this->paymentHandler($customer_charge, $amount, $currency, $company_id, $tx_type, $currentSubscriptionPlanId, $discount,$commission);
                    $this->company->updateCompanyByStatusAndId(2, $company_id);
                    $this->updateCurrentSubscriptionPlanDueDate($currentSubscriptionPlanId, $date, $numberOfMonths/*no of months needs to added for next due date*/);
                } else {
                    \Log::debug("==== Charge failed! *****************");
                    if (isset($customer_charge['message'])) {
                        \Log::debug("==== message : " . $customer_charge['message']);
                    }
                    $simplifya_name = Config::get('messages.COMPANY_NAME');
                    $layout = 'emails.account_expire';
                    $this->company->updateCompanyByStatusAndId(Config::get('simplifya.EXPIRE'), $company_id);
                    $this->company_subscription_plan->disableAllActiveSubscriptions($company_id);

                    $subject = 'Payment Method Declined';
                    foreach($users as $user) {
                        $email_data = array('from' => Config::get('simplifya.SIMPLIFIYA_EMAIL'),
                            'system' => $simplifya_name,
                            'company' => $simplifya_name
                        );
                        $this->sendWelcomeMail($user->email, $user->name,$layout,$subject,$email_data);
                    }
                }
            }else {
                // skip payment gateway and update subscription related tables
                \Log::debug("==== skip payment process when discount reach full amount!");
                $this->saveSubscriptionPlansWhenPaymentSkipped($company_id, $currentSubscriptionPlanId, $date, $numberOfMonths, $discount);
            }

        } else {
            \Log::debug("==== skip payment process when foc is 1");
            $this->saveSubscriptionPlansWhenPaymentSkipped($company_id, $currentSubscriptionPlanId, $date, $numberOfMonths);
        }

    }

    /**
     * Update company_subscriptions and company_subscription_plans tables
     * when payment gateway got skipped.
     *
     * @param $company_id
     * @param $currentSubscriptionPlanId
     * @param $date
     * @param $numberOfMonths
     * @param int $discount
     */
    private function saveSubscriptionPlansWhenPaymentSkipped($company_id, $currentSubscriptionPlanId, $date, $numberOfMonths, $discount = 0) {

        $master_subscription = $this->company->getSubscriptionType($company_id);
        $company_subscription = array(
            'company_id'            => $company_id,
            'master_subscription_id'=> $master_subscription[0]->id,
            'payment_id'            => 0.00, // not on foc
            'created_by'            => 0,
            'amount'                => 0,
            'company_subscription_plan_id' => $currentSubscriptionPlanId,
            'discount'              => $discount
        );
        $this->company_subscription->create($company_subscription);
        // update subscription plan
        $this->company->updateCompanyByStatusAndId(2, $company_id);
        $this->updateCurrentSubscriptionPlanDueDate($currentSubscriptionPlanId, $date, $numberOfMonths/*no of months needs to added for next due date*/);
    }

    private function sendWelcomeMail($email, $name, $layout, $subject,$data)
    {
        $send_mail = new sendMail();
        $send_mail->mailSender($layout, $email, $name, $subject, $data);
    }
}
