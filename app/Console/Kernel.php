<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Symfony\Component\Finder\Shell\Command;
use App\Console\Commands\InviteMailer;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\InviteMailer::class,
        Commands\LicenseRemainder::class,
        Commands\SubscriptionExpire::class,
        Commands\AddSubQuestionClassification::class,
        Commands\NotifyPendingPaymentOnSignUp::class,
        Commands\NotifyNoAuditAfterPayment::class,
        Commands\RosterJobs::class,
        Commands\UpdateDeletedSubQuestions::class,
        Commands\UpdateSubQuestionsStatus::class,
        Commands\SubscriptionRenew::class,
        Commands\AddSubscriptionPlanForExistingMjb::class,
        Commands\Inspire::class,
        Commands\DeleteTemproraryMjbCompanies::class,
        Commands\AuditMailer::class,
        Commands\QuestionsPublisher::class,
        Commands\QuestionsPublisher::class,
        Commands\AuditMailer::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        $schedule->command('inspire')
                ->everyMinute()->withoutOverlapping();
        $schedule->command('invite')
                 ->everyMinute()->withoutOverlapping();
        $schedule->command('reminder')
                ->daily()->at('06:45')->withoutOverlapping();
        $schedule->command('notify_admin_pending_sign_up_payments')
                ->hourly()->withoutOverlapping();
        $schedule->command('notify_no_audit_after_payment')
                ->hourly()->withoutOverlapping();
        $schedule->command('subscription:renew')
            ->daily()->withoutOverlapping();
        $schedule->command('roster:job')
            ->daily()->at('00:02')->withoutOverlapping();
        $schedule->command('tempMjb:remove')
            ->weekly()->at('23:30')->withoutOverlapping(); //runs on weekly at 23:30
        $schedule->command('audit')
            ->everyMinute()->withoutOverlapping();
        $schedule->command('question_publisher')
            ->daily()->at('00:01')->withoutOverlapping();
    }
}
