<?php

namespace App\Services;

use PhpOffice\PhpWord\TemplateProcessor;

/** PHPWord closes its ZIP on destruction but leaves unsaved temporary copies behind. */
class TemporaryWordTemplate extends TemplateProcessor
{
    public function __destruct()
    {
        parent::__destruct();
        if (is_file($this->tempDocumentFilename)) {
            @unlink($this->tempDocumentFilename);
        }
    }
}
