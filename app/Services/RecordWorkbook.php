<?php

namespace App\Services;

use DateTimeInterface;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Throwable;

class RecordWorkbook
{
    private Spreadsheet $book;

    private array $next = [];

    private array $ends = [];

    public function __construct(private string $report, private array $metadata)
    {
        $this->book = new Spreadsheet;
        $this->book->getActiveSheet()->setTitle(ucfirst($report));
    }

    public function table(string $title, array $headers): void
    {
        $sheet = $this->book->getSheetByName($title) ?? $this->book->createSheet()->setTitle($title);
        $headerRow = 8;
        foreach ($headers as $offset => $label) {
            $column = Coordinate::stringFromColumnIndex($offset + 1);
            $sheet->setCellValue($column.$headerRow, $label);
            $sheet->getColumnDimension($column)->setWidth(min(62, max(20, strlen($label) + 5)));
        }
        $end = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A'.$headerRow.':'.$end.$headerRow)->getFont()->setBold(true);
        $sheet->freezePane('B'.($headerRow + 1));
        $this->next[$title] = $headerRow + 1;
        $this->ends[$title] = [$headerRow, $end];
    }

    public function row(string $title, array $values): void
    {
        $sheet = $this->book->getSheetByName($title);
        $row = $this->next[$title]++;
        foreach ($values as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $column = Coordinate::stringFromColumnIndex($key + 1);
            $cell = $sheet->getCell($column.$row);
            if ($value instanceof DateTimeInterface) {
                $cell->setValueExplicit(Date::PHPToExcel($value), DataType::TYPE_NUMERIC);
                $cell->getStyle()->getNumberFormat()->setFormatCode('yyyy-mm-dd');
            } elseif (is_int($value) || is_float($value)) {
                $cell->setValueExplicit($value, DataType::TYPE_NUMERIC);
                if (is_float($value)) {
                    $cell->getStyle()->getNumberFormat()->setFormatCode('#,##0.00');
                }
            } else {
                $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
                $cell->getStyle()->getAlignment()->setWrapText(true);
            }
        }
    }

    public function save(): ?string
    {
        if ($this->next[ucfirst($this->report)] === $this->ends[ucfirst($this->report)][0] + 1) {
            return null;
        }
        foreach ($this->book->getWorksheetIterator() as $sheet) {
            $title = $sheet->getTitle();
            [$headerRow, $end] = $this->ends[$title];
            $last = $this->next[$title] - 1;
            $sheet->setAutoFilter('A'.$headerRow.':'.$end.max($headerRow + 1, $last));
            $lines = [$title, ...$this->metadata, 'Records on this sheet: '.($last - $headerRow)];
            foreach ($lines as $offset => $line) {
                $row = $offset + 1;
                $sheet->mergeCells("A{$row}:F{$row}");
                $sheet->setCellValueExplicit('A'.$row, $line, DataType::TYPE_STRING);
                $sheet->getStyle('A'.$row)->getAlignment()->setWrapText(true);
            }
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getRowDimension(5)->setRowHeight(42);
            if ($last === $headerRow) {
                $sheet->setCellValue('A'.($last + 1), 'No history recorded');
            }
        }
        $this->book->setActiveSheetIndex(0);
        $path = tempnam(sys_get_temp_dir(), 'burial-xlsx-');
        try {
            (new Xlsx($this->book))->save($path);
        } catch (Throwable $exception) {
            unlink($path);
            throw $exception;
        } finally {
            $this->book->disconnectWorksheets();
        }

        return $path;
    }
}
