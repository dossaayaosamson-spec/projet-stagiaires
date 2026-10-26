<?php

namespace App\Console\Commands;

use App\Mail\SubscriptionReminder;
use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendSubscriptionReminders extends Command
{
    protected $signature = 'subscriptions:send-reminders';

    protected $description = 'Send email reminders to users whose subscription expires in 3 days';

    public function handle()
    {
        $targetDate = now()->addDays(3)->format('Y-m-d');

        $subscriptions = Subscription::where('statut', 'actif')
            ->whereDate('date_fin', $targetDate)
            ->with('user')
            ->get();

        $this->info('Found '.$subscriptions->count()." subscriptions expiring on $targetDate.");

        foreach ($subscriptions as $subscription) {
            if ($subscription->user) {
                Mail::to($subscription->user->email)->send(new SubscriptionReminder($subscription->user, $subscription));
                $this->line('Reminder sent to: '.$subscription->user->email);
            }
        }

        $this->info('Done.');
    }
}
