<?php

namespace App\Traits;

trait HasRelationSets
{
    protected static function prefixRelations(
        string $prefix,
        array $relations
    ): array {
        return [
            $prefix,
            ...collect($relations)
                ->map(fn ($relation) => "{$prefix}.{$relation}")
                ->all(),
        ];
    }

    public function eagerLoadRelations(array $relations): static
    {
        return $this->loadMissing($relations);
    }
}