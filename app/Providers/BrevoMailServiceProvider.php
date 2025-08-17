<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Mail\MailManager;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\RawMessage;
use Brevo\Client\Configuration;
use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Model\SendSmtpEmail;

class BrevoMailServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->make(MailManager::class)->extend('brevo', function ($config) {
            return new class($config['key'] ?? env('mailers.brevo.key')) implements TransportInterface {
                protected string $apiKey;

                public function __construct(string $apiKey)
                {
                    $this->apiKey = $apiKey;
                }

                public function send(RawMessage $message, ?\Symfony\Component\Mailer\Envelope $envelope = null): ?SentMessage
                {
                    /** @var \Symfony\Component\Mime\Email $symfonyMessage */
                    $symfonyMessage = $message;

                    $from = $symfonyMessage->getFrom()[0];
                    $to   = $symfonyMessage->getTo()[0];

                    $email = new SendSmtpEmail();
                    $email->setSender([
                        'name'  => $from->getName() ?: $from->getAddress(),
                        'email' => $from->getAddress(),
                    ]);

                    $email->setTo([[
                        'email' => $to->getAddress(),
                        'name'  => $to->getName() ?: $to->getAddress(),
                    ]]);

                    $email->setSubject($symfonyMessage->getSubject());

                    $html = $symfonyMessage->getHtmlBody();
                    $text = $symfonyMessage->getTextBody();

                    // fallback if htmlBody is missing
                    if (empty($html)) {
                        $html = '<pre>' . e($text ?: ' ') . '</pre>';
                    }

                    $email->setHtmlContent($html);

                    // optional text version
                    if ($text) {
                        $email->setTextContent($text);
                    }

                    $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $this->apiKey);
                    $api = new TransactionalEmailsApi(null, $config);

                    $api->sendTransacEmail($email);

                    return new SentMessage($message, $envelope);
                }

                public function __toString(): string
                {
                    return 'brevo';
                }
            };
        });
    }
}
