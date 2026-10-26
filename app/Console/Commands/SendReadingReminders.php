<?php

namespace App\Console\Commands;

use App\Mail\ReadingReminder;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReadingReminders extends Command
{
    protected $signature = 'reading:send-reminders';

    protected $description = 'Envoie un e-mail de rappel de lecture aux utilisateurs enregistrés';

    public function handle(): int
    {
        $users = User::whereNotNull('email')
            ->where('newsletter', true)
            ->get();

        if ($users->isEmpty()) {
            $users = User::whereNotNull('email')->take(50)->get();
        }

        $this->info('Envoi des rappels de lecture à '.$users->count().' utilisateur(s)...');

        $sentCount = 0;
        foreach ($users as $user) {
            try {
                Mail::to($user->email)->send(new ReadingReminder($user));
                $sentCount++;
            } catch (\Exception $e) {
                Log::error('Erreur envoi rappel de lecture à '.$user->email.': '.$e->getMessage());
            }
        }

        $this->info("Rappels de lecture envoyés avec succès ($sentCount/".$users->count().').');

        return Command::SUCCESS;
    }
}
