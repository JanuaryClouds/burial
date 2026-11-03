<?php

namespace App\Services\Mail;

interface MessagePayload
{
    public function type(): string;

    public function toArray(): array;
}
