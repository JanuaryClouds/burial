<?php

namespace Tests\Unit;

use Dotenv\Repository\Adapter\ArrayAdapter;
use Dotenv\Repository\RepositoryBuilder;
use Illuminate\Support\Env;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class ServicesConfigurationTest extends TestCase
{
    private function configuration(array $values): array
    {
        $property = new ReflectionProperty(Env::class, 'repository');
        $original = $property->getValue();
        $repository = RepositoryBuilder::createWithNoAdapters()->addAdapter(ArrayAdapter::class)->make();
        foreach ($values as $key => $value) {
            $repository->set($key, $value);
        }
        $property->setValue(null, $repository);

        try {
            return require __DIR__.'/../../config/services.php';
        } finally {
            $property->setValue(null, $original);
        }
    }

    public function test_new_settings_override_legacy_including_false_and_empty_values(): void
    {
        $config = $this->configuration([
            'PORTAL_USERS_GET_ENABLE' => 'false', 'PORTAL_USERS_ENABLE_GET' => 'true',
            'PORTAL_USERS_GET_ENDPOINT' => '', 'PORTAL_USERS_ENDPOINT' => 'https://legacy.invalid',
            'PORTAL_USERS_SSO_SECRET_KEY' => 'new-secret', 'SSO_SECRET_KEY' => 'old-secret',
            'SMS_POST_ENABLE' => 'true', 'SMS_POST_API_KEY' => 'sms-key',
            'PORTAL_NOTIFICATION_POST_ENDPOINT' => 'https://notify.invalid',
            'PSA_CLASSIFICATION_GET_ENABLE' => 'true', 'PSA_CLASSIFICATION_GET_VERSION' => 'v2',
            'FILESERVER_GET_ENDPOINT' => 'https://read.invalid', 'FILESERVER_GET_API_KEY' => 'read-key',
            'FILESERVER_POST_ENDPOINT' => 'https://write.invalid', 'FILESERVER_POST_API_KEY' => 'write-key',
            'PORTAL_USERS_SEEDER' => 'false',
        ]);
        self::assertFalse($config['portal']['users']['enable']['get']);
        self::assertSame('', $config['portal']['users']['endpoint']);
        self::assertSame('new-secret', $config['portal']['sso']['secret']);
        self::assertFalse($config['portal']['users']['seeder']['preset']);
        self::assertTrue($config['sms']['enable']['post']);
        self::assertSame('sms-key', $config['sms']['key']);
        self::assertSame('https://notify.invalid', $config['portal']['notification']['endpoint']);
        self::assertTrue($config['psa_classification']['get']);
        self::assertSame('v2', $config['psa_classification']['version']);
        self::assertSame(['endpoint' => 'https://read.invalid', 'key' => 'read-key'], $config['fileserver']['get']);
        self::assertSame(['endpoint' => 'https://write.invalid', 'key' => 'write-key'], $config['fileserver']['post']);
        self::assertArrayNotHasKey('post', $config['portal']['users']['enable']);
        self::assertArrayNotHasKey('get', $config['portal']['notification']['enable']);
    }

    public function test_legacy_deployments_keep_their_settings_when_new_names_are_absent(): void
    {
        $config = $this->configuration([
            'PORTAL_USERS_ENABLE_GET' => 'true', 'PORTAL_USERS_ENDPOINT' => 'https://legacy.invalid',
            'SMS_ENABLE_POST' => 'true', 'SMS_API_KEY' => 'legacy-key',
            'FILESERVER_ENABLE_GET' => 'true', 'FILESERVER_ENDPOINT' => 'https://files.invalid',
            'FILESERVER_API' => '/upload',
        ]);
        self::assertTrue($config['portal']['users']['enable']['get']);
        self::assertSame('https://legacy.invalid', $config['portal']['users']['endpoint']);
        self::assertTrue($config['portal']['users']['seeder']['preset']);
        self::assertTrue($config['sms']['enable']['post']);
        self::assertSame('legacy-key', $config['sms']['key']);
        self::assertTrue($config['fileserver']['enable']['get']);
        self::assertSame('https://files.invalid', $config['fileserver']['endpoint']);
        self::assertSame('/upload', $config['fileserver']['api']);
    }
}
