<?php

namespace App\Services;

use App\Models\Application;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;
use ZipArchive;

class DocumentTemplateProcessor
{
    public function __construct(private DocumentPlaceholderRegistry $registry) {}

    public function inspect(string $path): TemplateProcessor
    {
        $zip = new ZipArchive;
        if ($zip->open($path) !== true) {
            $this->invalid();
        }
        try {
            if ($zip->numFiles > 2000) {
                $this->invalid();
            }
            $size = 0;
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entry = $zip->statIndex($i);
                $size += $entry['size'];
                if ($size > 50 * 1024 * 1024 || str_contains(strtolower($entry['name']), 'vbaproject')) {
                    $this->invalid();
                }
            }
            $parts = ['[Content_Types].xml', '_rels/.rels', 'word/document.xml'];
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                if (str_ends_with($name, '.xml') || str_ends_with($name, '.rels')) {
                    $parts[] = $name;
                }
            }
            foreach (array_unique($parts) as $part) {
                $xml = $zip->getFromName($part);
                if ($xml === false || stripos($xml, '<!DOCTYPE') !== false) {
                    $this->invalid();
                }
                $previous = libxml_use_internal_errors(true);
                try {
                    $document = new \DOMDocument;
                    if (! $document->loadXML($xml, LIBXML_NONET)) {
                        $this->invalid();
                    }
                    if ($part === 'word/document.xml' && ($document->documentElement->localName !== 'document' || $document->documentElement->namespaceURI !== 'http://schemas.openxmlformats.org/wordprocessingml/2006/main')) {
                        $this->invalid();
                    }
                } finally {
                    libxml_clear_errors();
                    libxml_use_internal_errors($previous);
                }
            }
        } finally {
            $zip->close();
        }
        try {
            $processor = new TemporaryWordTemplate($path);
            $unknown = array_diff($processor->getVariables(), array_keys($this->registry->definitions()));
        } catch (\Throwable) {
            $this->invalid();
        }
        if ($unknown) {
            throw ValidationException::withMessages(['upload' => 'Unsupported placeholders: '.implode(', ', $unknown)]);
        }

        return $processor;
    }

    public function generate(string $source, string $destination, Application $application): void
    {
        $processor = $this->inspect($source);
        $previous = Settings::isOutputEscapingEnabled();
        Settings::setOutputEscapingEnabled(true);
        try {
            $processor->setValues($this->registry->values($application, $processor->getVariables()));
            $processor->saveAs($destination);
        } finally {
            Settings::setOutputEscapingEnabled($previous);
        }
    }

    private function invalid(): never
    {
        throw ValidationException::withMessages(['upload' => 'Upload a readable, non-macro Word DOCX document (maximum expanded size 50 MB).']);
    }
}
