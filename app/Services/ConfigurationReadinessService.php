<?php

namespace App\Services;

use Illuminate\Encryption\Encrypter;

class ConfigurationReadinessService
{
    /** @return list<array{label: string, mode: string, status: string, detail: string, variables: string}> */
    public function inspect(): array
    {
        $development = app()->environment('local', 'testing');
        $policy = app(FileserverPolicy::class);
        $key = (string) config('app.key');
        $bytes = str_starts_with($key, 'base64:') ? base64_decode(substr($key, 7), true) : $key;
        $rows = [
            $this->row('Environment', 'Live', config('app.env') ? 'Configuration complete' : 'Missing configuration', 'Environment: '.app()->environment(), 'APP_ENV'),
            $this->row('Application URL', 'Live', $this->urlValid(config('app.url')) ? 'Configuration complete' : 'Invalid configuration', 'Application URL must be a valid HTTP(S) URL. Its value is hidden.', 'APP_URL'),
            $this->row('Timezone', 'Live', in_array(config('app.timezone'), timezone_identifiers_list(), true) ? 'Configuration complete' : 'Invalid configuration', 'Timezone: '.(string) config('app.timezone'), 'APP_TIMEZONE'),
            $this->row('Debug mode', 'Live', ! $development && config('app.debug') ? 'Invalid configuration' : 'Configuration complete', config('app.debug') ? 'Debug enabled. Disable outside local/testing.' : 'Debug disabled.', 'APP_DEBUG'),
            $this->row('Application encryption', 'Live', $key === '' ? 'Missing configuration' : (is_string($bytes) && Encrypter::supported($bytes, (string) config('app.cipher')) ? 'Configuration complete' : 'Invalid configuration'), 'Required for encrypted data. Preserve existing keys.', 'APP_KEY'),
            $this->row('Configuration cache', 'Live', 'Configuration complete', app()->configurationIsCached() ? 'Cached configuration is active. CI/CD must refresh it after environment changes.' : 'Configuration is not cached.', ''),
        ];
        foreach (['get' => 'Document retrieval', 'post' => 'Document uploads'] as $method => $label) {
            $mode = $policy->skip() ? 'Skipped' : ($policy->mock() ? 'Mock' : ($policy->enabled($method) ? 'Live' : 'Disabled'));
            $prefix = 'FILESERVER_'.strtoupper($method);
            $fields = $policy->mock() ? ['services.fileserver.mock_port' => 'FILESERVER_MOCK_PORT', 'services.fileserver.mock_token' => 'FILESERVER_MOCK_TOKEN'] : ['services.fileserver.'.$method.'.endpoint' => $prefix.'_ENDPOINT', 'services.fileserver.'.$method.'.key' => $prefix.'_API_KEY'];
            $rows[] = $this->integration($label, $mode, $fields, $method === 'post' ? 'Required or selected uploads block submission when unavailable.' : 'Stored document viewing requires retrieval.', $prefix.'_ENABLE', required: ! $development);
        }
        $key = (string) config('services.fileserver.enc_key');
        $bytes = base64_decode(str_starts_with($key, 'base64:') ? substr($key, 7) : $key, true);
        $rows[] = $this->row('Fileserver encryption', $policy->skip() ? 'Skipped' : ($policy->mock() ? 'Mock' : 'Live'), $policy->skip() ? 'Not applicable' : ($key === '' ? 'Missing configuration' : ($bytes !== false && strlen($bytes) === 32 ? 'Configuration complete' : 'Invalid configuration')), 'Requires a base64-encoded 32-byte key. Preserve the key for existing documents.', 'FILESERVER_ENCRYPTION_KEY');
        foreach (['sms' => ['SMS', 'SMS_POST'], 'portal.notification' => ['Portal notifications', 'PORTAL_NOTIFICATION_POST']] as $path => [$label, $env]) {
            $rows[] = $this->integration($label, $development ? 'Skipped' : (config('services.'.$path.'.enable.post') ? 'Live' : 'Disabled'), ['services.'.$path.'.endpoint' => $env.'_ENDPOINT', 'services.'.$path.'.key' => $env.'_API_KEY'], $path === 'sms' ? 'Required for live interview scheduling.' : 'Required for live interview scheduling for linked citizens.', $env.'_ENABLE', required: ! $development);
        }
        $rows[] = $this->integration('Portal users', config('services.portal.users.enable.get') ? ($development && config('services.portal.users.mock') ? 'Mock' : 'Live') : 'Disabled', ['services.portal.users.endpoint' => 'PORTAL_USERS_GET_ENDPOINT', 'services.portal.users.key' => 'PORTAL_USERS_GET_API_KEY'], 'Required for Portal profile synchronization. Mock mode applies only to supported local fixtures.', 'PORTAL_USERS_GET_ENABLE', required: ! $development);
        $rows[] = $this->integration('Portal SSO', 'Live', ['services.portal.endpoint' => 'PORTAL_ENDPOINT', 'services.portal.sso.secret' => 'PORTAL_USERS_SSO_SECRET_KEY'], 'Required for citizen sign-in.');
        $mailer = (string) config('mail.default');
        $smtp = config('mail.mailers.'.$mailer.'.transport') === 'smtp';
        $fields = ['mail.from.address' => 'MAIL_FROM_ADDRESS'];
        if ($smtp) {
            if (config('mail.mailers.'.$mailer.'.url')) {
                $fields['mail.mailers.'.$mailer.'.url'] = 'MAIL_URL';
            } else {
                $fields += ['mail.mailers.'.$mailer.'.host' => 'MAIL_HOST', 'mail.mailers.'.$mailer.'.port' => 'MAIL_PORT'];
                if (! $development || config('mail.mailers.'.$mailer.'.username') || config('mail.mailers.'.$mailer.'.password')) {
                    $fields += ['mail.mailers.'.$mailer.'.username' => 'MAIL_USERNAME', 'mail.mailers.'.$mailer.'.password' => 'MAIL_PASSWORD'];
                }
            }
        }
        $mail = $this->integration('Email transport', in_array($mailer, ['log', 'array'], true) ? 'Mock' : 'Live', $fields, 'Production requires SMTP with TLS. Configuration does not establish delivery.', 'MAIL_MAILER');
        if ((! $development && (! $smtp || ! config('mail.mailers.'.$mailer.'.require_tls'))) || ! config('mail.mailers.'.$mailer.'.transport')) {
            $mail['status'] = 'Invalid configuration';
            $mail['variables'] .= ', MAIL_REQUIRE_TLS';
        }
        $rows[] = $mail;
        $mock = $development && config('email_logs.mock');
        $logs = $this->integration('Email suppression logs', $mock ? 'Mock' : 'Live', $mock ? [] : ['database.connections.email_logs_db.host' => 'EMAIL_LOGS_DB_HOST', 'database.connections.email_logs_db.database' => 'EMAIL_LOGS_DB_DATABASE', 'database.connections.email_logs_db.username' => 'EMAIL_LOGS_DB_USERNAME', 'database.connections.email_logs_db.password' => 'EMAIL_LOGS_DB_PASSWORD'], 'Sending stops when suppression checks are unavailable.', 'EMAIL_LOGS_MOCK');
        if (! $development && config('email_logs.mock')) {
            $logs['status'] = 'Invalid configuration';
        }
        $connection = $mock ? 'email_logs_mock' : 'email_logs_db';
        $settings = config('database.connections.'.$connection, []);
        $main = config('database.connections.'.config('database.default'), []);
        if (($settings['driver'] ?? null) !== ($mock ? 'sqlite' : 'mysql') || empty($settings['database']) || config('database.default') === $connection || ($mock && ($main['driver'] ?? null) === 'sqlite' && ($settings['database'] ?? null) !== ':memory:' && ($settings['database'] ?? null) === ($main['database'] ?? null))) {
            $logs['status'] = 'Invalid configuration';
        }
        $rows[] = $logs;
        foreach (['psa_classification' => ['PSA classification (supplementary)', 'PSA_CLASSIFICATION_GET', 'get'], 'disbursement' => ['Disbursement (supplementary)', 'DISBURSEMENT_SYSTEM', 'enable.get']] as $path => [$label, $env, $flag]) {
            $enabled = config('services.'.$path.'.'.$flag) || ($path === 'disbursement' && config('services.disbursement.enable.post'));
            $fields = ['services.'.$path.'.endpoint' => $env.'_ENDPOINT', 'services.'.$path.'.'.($path === 'disbursement' ? 'key' : 'api_key') => $env.'_API_KEY'];
            if ($path === 'psa_classification') {
                $fields['services.psa_classification.version'] = $env.'_VERSION';
            }
            $rows[] = $this->integration($label, $enabled ? 'Live' : 'Disabled', $fields, 'Optional configuration only; runtime integration is not verified.', $path === 'disbursement' ? 'DISBURSEMENT_SYSTEM_ENABLE_GET, DISBURSEMENT_SYSTEM_ENABLE_POST' : 'PSA_CLASSIFICATION_GET_ENABLE');
        }

        return $rows;
    }

    /**
     * @param  array<string, string>  $fields
     * @return array{label: string, mode: string, status: string, detail: string, variables: string}
     */
    private function integration(string $label, string $mode, array $fields, string $detail, string $flag = '', bool $required = false): array
    {
        $status = in_array($mode, ['Disabled', 'Skipped'], true) ? 'Not applicable' : 'Configuration complete';
        if ($mode === 'Disabled' && $required) {
            $status = 'Missing configuration';
        }
        if (in_array($mode, ['Live', 'Mock'], true)) {
            foreach ($fields as $path => $variable) {
                $value = config($path);
                if ($value === null || trim((string) $value) === '') {
                    $status = 'Missing configuration';
                    break;
                }
                if ((str_ends_with($path, '.endpoint') && ! $this->urlValid($value)) || (str_ends_with($path, 'port') && ! filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => $path === 'services.fileserver.mock_port' ? 1024 : 1, 'max_range' => 65535]])) || ($path === 'mail.from.address' && ! filter_var($value, FILTER_VALIDATE_EMAIL))) {
                    $status = 'Invalid configuration';
                }
                if (str_starts_with($path, 'mail.mailers.') && str_ends_with($path, '.url') && (! filter_var($value, FILTER_VALIDATE_URL) || ! in_array(parse_url($value, PHP_URL_SCHEME), ['smtp', 'smtps'], true))) {
                    $status = 'Invalid configuration';
                }
            }
        }

        return $this->row($label, $mode, $status, $detail, implode(', ', array_filter([$flag, ...array_values($fields)])));
    }

    private function urlValid(mixed $value): bool
    {
        return is_string($value) && filter_var($value, FILTER_VALIDATE_URL) && in_array(parse_url($value, PHP_URL_SCHEME), ['http', 'https'], true);
    }

    /** @return array{label: string, mode: string, status: string, detail: string, variables: string} */
    private function row(string $label, string $mode, string $status, string $detail, string $variables): array
    {
        return compact('label', 'mode', 'status', 'detail', 'variables');
    }
}
