<?php

namespace Tests\Feature;

use App\Services\ImagePayload;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImagePayloadTest extends TestCase
{
    private string $key;

    private string $jpeg;

    protected function setUp(): void
    {
        parent::setUp();
        $this->key = random_bytes(32);
        config(['services.fileserver.enc_key' => 'base64:'.base64_encode($this->key)]);
        $file = UploadedFile::fake()->image('photo.jpg', 20, 20);
        $this->jpeg = file_get_contents($file->getRealPath());
    }

    public function test_new_payload_round_trip_and_randomized_encryption(): void
    {
        $codec = new ImagePayload;
        $first = $codec->encrypt($this->jpeg);
        $this->assertSame($this->jpeg, $codec->decrypt($first));
        $this->assertNotSame($first, $codec->encrypt($this->jpeg));
    }

    public function test_legacy_payload_is_still_readable(): void
    {
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($this->jpeg, 'aes-256-cbc', $this->key, OPENSSL_RAW_DATA, $iv);
        $payload = $iv.hash_hmac('sha256', $encrypted, $this->key, true).$encrypted;
        $this->assertSame($this->jpeg, (new ImagePayload)->decrypt($payload));
        $payload[50] = chr(ord($payload[50]) ^ 1);
        $this->expectException(\RuntimeException::class);
        (new ImagePayload)->decrypt($payload);
    }

    public function test_authenticated_payload_rejects_iv_ciphertext_and_tag_tampering(): void
    {
        $codec = new ImagePayload;
        $payload = $codec->encrypt($this->jpeg);
        foreach (['iv', 'value', 'tag'] as $field) {
            $parts = json_decode(base64_decode(explode("\n", $payload, 2)[1]), true);
            $bytes = base64_decode($parts[$field]);
            $bytes[0] = chr(ord($bytes[0]) ^ 1);
            $parts[$field] = base64_encode($bytes);
            try {
                $codec->decrypt("BURIAL-IMAGE-V1\n".base64_encode(json_encode($parts)));
                $this->fail('Tampered '.$field.' accepted.');
            } catch (DecryptException) {
                $this->addToAssertionCount(1);
            }
        }
    }

    public function test_invalid_key_and_malformed_payloads_fail_closed(): void
    {
        foreach (['garbage', "BURIAL-IMAGE-V1\ninvalid", "BURIAL-IMAGE-V2\ninvalid"] as $payload) {
            try {
                (new ImagePayload)->decrypt($payload);
                $this->fail('Invalid payload accepted.');
            } catch (\RuntimeException) {
                $this->addToAssertionCount(1);
            }
        }
        config(['services.fileserver.enc_key' => 'base64:'.str_repeat('!', 44)]);
        $this->expectException(\RuntimeException::class);
        (new ImagePayload)->encrypt($this->jpeg);
    }

    public function test_decrypted_non_image_is_rejected(): void
    {
        $codec = new ImagePayload;
        $this->expectException(\RuntimeException::class);
        $codec->decrypt($codec->encrypt('not a jpeg'));
    }
}
