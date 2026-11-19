<?php

namespace Tests\Unit;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPUnit\Framework\TestCase;

class SpreadsheetCompatibilityTest extends TestCase
{
    public function test_application_export_template_can_be_written_and_read(): void
    {
        $spreadsheet = IOFactory::load(__DIR__.'/../../storage/app/templates/burial-assistances-template.xlsx');
        $output = tempnam(sys_get_temp_dir(), 'burial-export-');
        self::assertNotFalse($output);

        try {
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A4', 'PHP-compatibility');
            $sheet->setCellValue('E4', 'Dela Cruz');
            $sheet->setCellValue('T4', 12500.50);
            $sheet->setCellValue('BK4', 'For Pickup');
            (new Xlsx($spreadsheet))->save($output);

            $reloaded = IOFactory::load($output);
            try {
                $export = $reloaded->getActiveSheet();
                self::assertSame('PHP-compatibility', $export->getCell('A4')->getValue());
                self::assertSame('Dela Cruz', $export->getCell('E4')->getValue());
                self::assertEquals(12500.50, $export->getCell('T4')->getValue());
                self::assertSame('For Pickup', $export->getCell('BK4')->getValue());
                self::assertSame($sheet->getStyle('T4')->getNumberFormat()->getFormatCode(), $export->getStyle('T4')->getNumberFormat()->getFormatCode());
            } finally {
                $reloaded->disconnectWorksheets();
            }
        } finally {
            $spreadsheet->disconnectWorksheets();
            unlink($output);
        }
    }
}
