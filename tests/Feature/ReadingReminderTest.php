<?php

namespace Tests\Feature;

use App\Mail\ReadingReminder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ReadingReminderTest extends TestCase
{
    use RefreshDatabase;

    public function test_reading_reminder_command_sends_emails(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'lecteur@agaueditions.com',
            'newsletter' => true,
        ]);

        $this->artisan('reading:send-reminders')->assertExitCode(0);

        Mail::assertSent(ReadingReminder::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }
}
