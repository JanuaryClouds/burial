<?php

namespace App\Services\Mail;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Symfony\Component\Mime\Address;

class OutgoingMailPolicy
{
    public function sending(MessageSending $event): void
    {
        $headers = $event->message->getHeaders();
        $tags = [];
        foreach ($headers->all('X-SES-MESSAGE-TAGS') as $header) {
            foreach (explode(',', $header->getBodyAsString()) as $tag) {
                [$key, $value] = array_pad(explode('=', trim($tag), 2), 2, '');
                $key = trim($key);
                $value = trim($value);
                if ($key === 'site') {
                    continue;
                }
                if (! preg_match('/^[A-Za-z0-9_-]+$/D', $key) || ! preg_match('/^[A-Za-z0-9_-]+$/D', $value)) {
                    throw new \LogicException('Invalid SES message tag configuration.');
                }
                $tags[$key] = $value;
            }
        }
        $tags['site'] = 'burial';
        $headers->remove('X-SES-MESSAGE-TAGS');
        $headers->addTextHeader('X-SES-MESSAGE-TAGS', implode(', ', array_map(fn ($key, $value) => $key.'='.$value, array_keys($tags), $tags)));

        $logs = app(SharedEmailLogs::class);
        $recipients = [];
        foreach (['To', 'Cc', 'Bcc'] as $group) {
            $normalized = [];
            foreach ($event->message->{'get'.$group}() as $address) {
                $email = $logs->normalize($address->getAddress());
                $recipients[] = $email;
                $normalized[] = new Address($email, $address->getName());
            }
            $event->message->{strtolower($group)}(...$normalized);
        }
        $logs->assertAllowed($recipients);
    }

    public function sent(MessageSent $event): void
    {
        try {
            if (! app(SharedEmailLogs::class)->mockEnabled()) {
                return;
            }
            $recipients = array_unique(array_map(fn (Address $address) => strtolower(trim($address->getAddress())),
                array_merge($event->message->getTo(), $event->message->getCc(), $event->message->getBcc())));
            foreach ($recipients as $recipient) {
                app(MockEmailLogs::class)->append($recipient, 'Send', $event->sent->getMessageId(),
                    subject: $event->message->getSubject() ?? '');
            }
        } catch (\Throwable) {
            // SMTP already accepted this message. Do not manufacture a delivery failure or expose message data.
            try {
                logger()->warning('Mock email log write failed after SMTP acceptance. Do not retry the delivered message.');
            } catch (\Throwable) {
                // A logging outage must not turn an accepted email into a retryable delivery failure either.
            }
        }
    }
}
