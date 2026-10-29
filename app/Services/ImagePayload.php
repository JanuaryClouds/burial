<?php

namespace App\Services;

use Illuminate\Encryption\Encrypter;
use RuntimeException;

class ImagePayload
{
    private const PREFIX = "BURIAL-IMAGE-V1\n";

    private function key(): string
    {
        $value = (string) config('services.fileserver.enc_key');
        $key = base64_decode(str_starts_with($value, 'base64:') ? substr($value, 7) : $value, true);
        if ($key === false || strlen($key) !== 32) {
            throw new RuntimeException('File encryption key must be a base64-encoded 32-byte key.');
        }

        return $key;
    }

    public function encrypt(string $jpeg): string
    {
        return self::PREFIX.(new Encrypter($this->key(), 'aes-256-gcm'))->encryptString($jpeg);
    }

    public function decrypt(string $payload): string
    {
        $key = $this->key();
        if (str_starts_with($payload, self::PREFIX)) {
            $jpeg = (new Encrypter($key, 'aes-256-gcm'))->decryptString(substr($payload, strlen(self::PREFIX)));
        } else {
            if (str_starts_with($payload, 'BURIAL-IMAGE-')) {
                throw new RuntimeException('Unsupported encrypted image version.');
            }
            // Compatibility only: old payloads authenticated ciphertext, but not the IV.
            if (strlen($payload) < 64 || (strlen($payload) - 48) % 16 !== 0) {
                throw new RuntimeException('Invalid encrypted image.');
            }
            $ciphertext = substr($payload, 48);
            if (! hash_equals(substr($payload, 16, 32), hash_hmac('sha256', $ciphertext, $key, true))) {
                throw new RuntimeException('Invalid encrypted image.');
            }
            $jpeg = openssl_decrypt($ciphertext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, substr($payload, 0, 16));
        }

        if (! is_string($jpeg) || (@getimagesizefromstring($jpeg)[2] ?? null) !== IMAGETYPE_JPEG) {
            throw new RuntimeException('Invalid decrypted image.');
        }

        return $jpeg;
    }
}
