<?php

namespace Tests\Unit;

use App\Models\Application;
use App\Models\Beneficiary;
use App\Services\BeneficiaryService;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class BeneficiaryNatalityTest extends TestCase
{
    private function beneficiary(?string $birth, ?string $death, bool $hasApplication = true): Beneficiary
    {
        $beneficiary = new Beneficiary([
            'date_of_birth' => $birth,
            'date_of_death' => $death,
        ]);
        $beneficiary->setRelation('application', $hasApplication ? new Application : null);

        return $beneficiary;
    }

    public function test_counts_use_age_at_death_and_include_the_existing_day_boundaries(): void
    {
        $beneficiaries = collect([0, 1, 7, 8, 27, 28, 29, 365, 3650])->map(
            fn ($days) => $this->beneficiary('2020-01-01', Carbon::parse('2020-01-01')->addDays($days)->toDateString())
        );

        $this->assertSame([
            ['name' => 'Perinatal', 'count' => 3],
            ['name' => 'Neonatal', 'count' => 3],
        ], (new BeneficiaryService)->groupByNatality($beneficiaries)->all());
    }

    public function test_older_beneficiaries_are_not_counted_as_infant_deaths(): void
    {
        $beneficiaries = collect([6, 12, 15, 27, 32, 35, 45, 46])->map(
            fn ($years) => $this->beneficiary('1980-01-01', Carbon::parse('1980-01-01')->addYears($years)->toDateString())
        );

        $this->assertTrue((new BeneficiaryService)->groupByNatality($beneficiaries)->isEmpty());
    }

    public function test_missing_dates_reversed_dates_and_drafts_are_excluded(): void
    {
        $beneficiaries = collect([
            $this->beneficiary(null, '2026-01-01'),
            $this->beneficiary('2026-01-01', null),
            $this->beneficiary('2026-01-02', '2026-01-01'),
            $this->beneficiary('2026-01-01', '2026-01-01', false),
        ]);

        $this->assertTrue((new BeneficiaryService)->groupByNatality($beneficiaries)->isEmpty());
        $this->assertTrue((new BeneficiaryService)->groupByNatality(collect())->isEmpty());
    }
}
