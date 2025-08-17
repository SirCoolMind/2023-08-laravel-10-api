<?php

namespace App\Console\Commands;

use Brevo\Client\Model\SendSmtpEmail;
use Hofmannsven\Brevo\Facades\Brevo;
use Illuminate\Console\Command;
use Mail;

class EmailTestUsingBrevo extends Command
{
    protected $signature = 'test:send-email-brevo {type?}';
    protected $description = 'Command description';

    public function handle()
    {
        $type = $this->argument('type') ?? '1';


        switch ($type) {
            case '3':
                return $this->userTesting();
            case '2':
                return $this->manualTesting();
            case '1':
            default:
                return $this->defaultTesting();
        }

    }

    public function defaultTesting()
    {
        Mail::raw('This is a test email sent via Brevo API transport.', function ($message) {
            $message->to('hafizcoolman@gmail.com')
                ->subject('Brevo Test Mail');
        });

        $this->info('Test email sent via Brevo API!');
        return 0;
    }

    public function userTesting()
    {
        $user = \App\Models\User::first();

        if (! $user) {
            $this->error('No users found in the database.');
            return 1;
        }

        Mail::raw('This is a test email sent via Brevo API transport.', function ($message) use ($user) {
            $message->to($user->email, $user->name ?? null)
                    ->subject('Brevo Test Mail');
        });

        $this->info("Test email sent to {$user->email} via Brevo API!");
        return 0;
    }

    public function manualTesting()
    {
        $email = new SendSmtpEmail([
            'to' => [
                ['email' => 'hafizcoolman@gmail.com', 'name' => 'SirCoolMind']
            ],
            'sender' => [
                'email' => config('mail.from.address', ''),
                'name'  => config('mail.from.name')
            ],
            'subject'     => 'Hello from Laravel Brevo!',
            'htmlContent' => '<p>This is a <strong>test email</strong> sent via Brevo API 🎉</p>',
        ]);

        try {
            $result = Brevo::transactionalEmailsApi()->sendTransacEmail($email);
            $this->info('Email sent successfully ✅');
            $this->line(print_r($result, true));
            return 0; // success
        } catch (\Exception $e) {
            $this->error('Brevo send email failed: ' . $e->getMessage());
            return 1; // failure
        }
    }
}
