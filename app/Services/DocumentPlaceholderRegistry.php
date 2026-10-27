<?php

namespace App\Services;

use App\Models\Application;
use Carbon\Carbon;

class DocumentPlaceholderRegistry
{
    public function definitions(): array
    {
        $definitions = [];
        foreach (['client', 'beneficiary'] as $person) {
            foreach (['fullname', 'first_name', 'middle_name', 'last_name', 'suffix', 'address', 'date_of_birth'] as $field) {
                $relation = $person === 'client' && in_array($field, ['fullname', 'first_name', 'middle_name', 'last_name', 'suffix']) ? 'client.user' : ($field === 'address' ? "$person.address" : $person);
                $definitions["$person.$field"] = [
                    'description' => ucfirst($person).' '.str_replace('_', ' ', $field),
                    'relations' => [$relation],
                    'resolve' => function (Application $application) use ($person, $field) {
                        $model = $application->{$person};

                        return match ($field) {
                            'fullname' => $model?->fullname(),
                            'address' => $model?->fullAddress(),
                            'date_of_birth' => $this->date($model?->date_of_birth),
                            default => $person === 'client' ? $model?->user?->{$field} : $model?->{$field},
                        };
                    },
                ];
            }
        }

        return $definitions + [
            'client.contact_number' => $this->definition('Client contact number', ['client'], fn ($a) => $a->client?->contact_number),
            'beneficiary.date_of_death' => $this->definition('Beneficiary date of death', ['beneficiary'], fn ($a) => $this->date($a->beneficiary?->date_of_death)),
            'application.tracking_no' => $this->definition('Application tracking number', [], fn ($a) => $a->tracking_no),
            'application.created_at' => $this->definition('Application date', [], fn ($a) => $this->date($a->created_at)),
            'recommendation.funeral_assistance_type' => $this->definition('Funeral assistance type', ['activeRecommendation.funeralAssistanceType'], fn ($a) => $a->activeRecommendation?->funeralAssistanceType?->name),
            'recommendation.mode_of_assistance' => $this->definition('Mode of assistance', ['activeRecommendation.modeOfAssistance'], fn ($a) => $a->activeRecommendation?->modeOfAssistance?->name),
            'recommendation.amount_extended' => $this->definition('Amount (e.g. 10,000.00)', ['activeRecommendation'], fn ($a) => $a->activeRecommendation?->amount_extended === null ? '' : number_format((float) $a->activeRecommendation->amount_extended, 2)),
            'recommendation.recommended_at' => $this->definition('Recommendation date', ['activeRecommendation'], fn ($a) => $this->date($a->activeRecommendation?->created_at)),
        ];
    }

    public function values(Application $application, array $keys): array
    {
        $definitions = $this->definitions();
        $relations = [];
        foreach ($keys as $key) {
            if (! isset($definitions[$key])) {
                throw new \InvalidArgumentException('Unsupported document placeholder.');
            }
            $relations = array_merge($relations, $definitions[$key]['relations']);
        }
        $application->loadMissing(array_unique($relations));
        $values = [];
        foreach ($keys as $key) {
            $values[$key] = (string) ($definitions[$key]['resolve']($application) ?? '');
        }

        return $values;
    }

    private function definition(string $description, array $relations, \Closure $resolve): array
    {
        return compact('description', 'relations', 'resolve');
    }

    private function date(mixed $value): string
    {
        return filled($value) ? Carbon::parse($value)->format('F j, Y') : '';
    }
}
