<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\SomeEvent' => [
            'App\Listeners\EventListener',
        ],
        'App\Events\AdminMailRequest' => [
            'App\Listeners\AdminMailSender',
        ],
        'App\Events\CcGeMailRequest' => [
            'App\Listeners\CcGeMailSender',
        ],
        'App\Events\MjbMailRequest' => [
            'App\Listeners\MjbMailSender',
        ],
        'App\Events\AssignUserNotifRequest' => [
            'App\Listeners\AssignUserNotifSender',
        ],
        'App\Events\AddCommentNotifRequest' => [
            'App\Listeners\AddCommentNotifSender',
        ],
        'App\Events\AddAppointmentNotifRequest' => [
            'App\Listeners\AddAppointmentNotifSender',
        ],
        'App\Events\MjbSignUpSupport' => [
            'App\Listeners\MjbSignUpSupportListener',
        ],
        'App\Events\MjbInHouseAuditSupport' => [
            'App\Listeners\MjbInHouseAuditSupportListener',
        ],
        'App\Events\SendReferralToken' => [
            'App\Listeners\ReferralTokenEmailListener',
        ],
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}
