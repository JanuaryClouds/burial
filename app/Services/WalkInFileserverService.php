<?php

namespace App\Services;

class WalkInFileserverService
{
    public function submissionWarning(): ?string
    {
        return app(FileserverPolicy::class)->warning();
    }
}
