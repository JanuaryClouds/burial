<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use ReflectionClass;
use ReflectionMethod;

class ModelTreeCommand extends Command
{
    protected $signature = 'model:tree
        {model : Model class name, e.g. Application}
        {--depth=3 : Maximum recursion depth}
        {--paths : Output eager-load paths}';

    protected $description = 'Display model relationship tree';

    public function handle(): int
    {
        $modelClass = 'App\\Models\\' . $this->argument('model');

        if (! class_exists($modelClass)) {
            $this->error("Model [{$modelClass}] not found.");
            return self::FAILURE;
        }

        $this->displayTree(
            new $modelClass(),
            '',
            [],
            (int) $this->option('depth')
        );

        if ($this->option('paths')) {
            $this->displayPaths(
                new $modelClass(),
                [],
                [],
                (int) $this->option('depth')
            );
        
            return self::SUCCESS;
        }

        return self::SUCCESS;
    }

    protected function displayTree(
        Model $model,
        string $prefix,
        array $visited,
        int $remainingDepth
    ): void {
        $modelName = class_basename($model);

        if ($prefix === '') {
            $this->line($modelName);
        }

        if ($remainingDepth <= 0) {
            return;
        }

        $visited[] = get_class($model);

        $relations = $this->getRelations($model);

        $lastIndex = count($relations) - 1;

        foreach ($relations as $index => $relation) {
            $isLast = $index === $lastIndex;

            $branch = $isLast ? '└── ' : '├── ';
            $nextPrefix = $prefix . ($isLast ? '    ' : '│   ');

            $relationName = $relation['name'];
            $relationType = $relation['type'];
            $relatedModel = $relation['related'];

            $this->line(
                $prefix .
                $branch .
                "{$relationName} ({$relationType})"
            );

            if (
                in_array($relatedModel, $visited, true)
            ) {
                continue;
            }

            $this->displayTree(
                new $relatedModel(),
                $nextPrefix,
                $visited,
                $remainingDepth - 1
            );
        }
    }

    protected function displayPaths(
        Model $model,
        array $path,
        array $visited,
        int $remainingDepth
    ): void {
        if ($remainingDepth <= 0) {
            return;
        }
    
        $visited[] = get_class($model);
    
        foreach ($this->getRelations($model) as $relation) {
            $currentPath = [...$path, $relation['name']];
    
            $this->line(implode('.', $currentPath));
    
            if (in_array($relation['related'], $visited, true)) {
                continue;
            }
    
            $this->displayPaths(
                new $relation['related'](),
                $currentPath,
                $visited,
                $remainingDepth - 1
            );
        }
    }

    protected function getRelations(Model $model): array
    {
        $relations = [];

        $reflection = new ReflectionClass($model);

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (
                $method->class !== $reflection->getName()
                || $method->getNumberOfParameters() > 0
            ) {
                continue;
            }

            try {
                $result = $method->invoke($model);

                if ($result instanceof Relation) {
                    $relations[] = [
                        'name' => $method->getName(),
                        'type' => class_basename($result),
                        'related' => get_class($result->getRelated()),
                    ];
                }
            } catch (\Throwable) {
                //
            }
        }

        return $relations;
    }
}