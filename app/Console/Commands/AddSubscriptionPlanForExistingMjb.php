<?php

namespace App\Console\Commands;

use App\Repositories\CompanyLocationLicenseRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\CompanySubscriptionPlanRepository;
use App\Repositories\CompanySubscriptionRepository;
use App\Repositories\MasterSubscriptionRepository;
use App\Repositories\MasterUserRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\QuestionClassificationRepository;
use App\Repositories\QuestionRepository;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

/**
 * Class UpdateDeletedSubQuestions
 *
 * Scenario:
 *  - Parent Question `A` has two child questions `A1` and `A2`
 *  - Delete the parent question `A`.
 *  - When we delete the parent question it should delete all child questions as well.
 *  - But the system does not delete child questions.
 *
 * This artisan command task will fetch all child questions and updated them
 * as deleted which have not deleted when parent got delete.
 *
 * @package App\Console\Commands
 */
class AddSubscriptionPlanForExistingMjb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add subscription plan for existing mjbs';


    protected $company;
    protected $masterSubscription;
    protected $master_data;
    protected $payment;
    protected $companyLicense;
    protected $company_subscription;
    protected $company_subscription_plan;

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
     * @internal param QuestionRepository $question
     * @internal param QuestionClassificationRepository $questionClassification
     */
    public function __construct(CompanyRepository $company,
                                MasterSubscriptionRepository $masterSubscription,
                                MasterUserRepository $master_data,
                                PaymentRepository $payment,
                                CompanyLocationLicenseRepository $companyLicense,
                                CompanySubscriptionRepository $company_subscription,
                                CompanySubscriptionPlanRepository $company_subscription_plan)
    {
        parent::__construct();
        $this->company = $company;
        $this->masterSubscription = $masterSubscription;
        $this->master_data = $master_data;
        $this->payment = $payment;
        $this->companyLicense = $companyLicense;
        $this->company_subscription = $company_subscription;
        $this->company_subscription_plan = $company_subscription_plan;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        // Start DB transaction
        DB::beginTransaction();
        try {
            $companies = $this->company->findWhere(array('entity_type' => Config::get('simplifya.MarijuanaBusiness'), 'status' => 2));
            foreach($companies as $company) {
                $monthlyPlan = $this->masterSubscription->getMonthlySubscriptionByEntity($company->entity_type, 1);
                $subsriptionPlans = $this->company_subscription_plan->findWhere(array('company_id' => $company->id));
                \Log::info("==== company id: " . $company->id . ' - ' . $subsriptionPlans->count());
                if (!$subsriptionPlans->count()) {
                    \Log::info("==== no plan added for company_id " . $company->id);
                    $plansParam = $this->company_subscription_plan->getSubscriptionPlanParams($monthlyPlan->id, $company->id, date('Y-m-d'));
                    \Log::info("==== plan param " . print_r($plansParam, true));
                    $this->company_subscription_plan->create($plansParam);
                }
            }
            DB::commit();
        }catch (Exception $e) {
            $this->info("Exception got fired " . $e->getMessage());
            DB::rollback();
        }
    }
}
