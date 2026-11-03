<?php

namespace Tests\Unit;

use App\Models\Application;
use App\Models\Closure;
use App\Models\Interview;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ApplicationProcessingStateTest extends TestCase
{
    public static function terminalRelations(): array
    {
        return [['rejection'], ['cancellation'], ['referral'], ['closure']];
    }

    #[DataProvider('terminalRelations')]
    public function test_each_terminal_outcome_makes_application_inactive(string $relation): void
    {
        $application = new Application;
        foreach (['rejection', 'cancellation', 'referral', 'closure'] as $name) {
            $application->setRelation($name, null);
        }
        $this->assertTrue($application->isActive());
        $application->setRelation($relation, new Closure);
        $this->assertFalse($application->isActive());
    }

    public static function interviewStatuses(): array
    {
        return [[[], false], [['scheduled'], false], [['done'], true], [['scheduled', 'done'], true]];
    }

    #[DataProvider('interviewStatuses')]
    public function test_only_completed_current_cycle_interviews_satisfy_processing(array $statuses, bool $expected): void
    {
        $application = new Application;
        $application->setRelation('currentInterviews', new Collection(array_map(fn ($status) => new Interview(['status' => $status]), $statuses)));
        $this->assertSame($expected, $application->finishedInterview());
    }
}
