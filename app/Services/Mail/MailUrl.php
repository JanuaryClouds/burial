<?php

namespace App\Services\Mail;

final class MailUrl
{
    public static function base(): string
    {
        $base = rtrim(config('app.url'), '/');
        $parts = parse_url($base);
        if (! filter_var($base, FILTER_VALIDATE_URL) || ! in_array($parts['scheme'] ?? '', ['http', 'https'], true)
            || isset($parts['user']) || isset($parts['pass']) || isset($parts['query']) || isset($parts['fragment'])
            || (app()->isProduction() && $parts['scheme'] !== 'https')) {
            throw new \LogicException('Invalid mail URL configuration.');
        }

        return $base;
    }
}
