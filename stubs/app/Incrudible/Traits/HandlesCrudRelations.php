<?php

namespace App\Incrudible\Traits;

use Incrudible\Incrudible\Facades\Incrudible;

trait HandlesCrudRelations
{
    /**
     * Define relations for the model.
     */
    public function relations(string $route): array
    {
        $routePrefix = Incrudible::routePrefix();
        $relationsConfig = config("{$routePrefix}.{$route}.relations");

        // Build the relations array
        $relations = [];

        foreach ($relationsConfig as $config) {

            $relationRoute = $config['route'];

            switch ($config['type']) {
                case 'HasMany':
                    $relations[] = [
                        ...$config,
                        ...config("{$routePrefix}.{$relationRoute}.index"),
                    ];
                    break;
                case 'BelongsTo':
                    // TODO
                    // $relationRoute = $relationRoute;
                    break;
                case 'BelongsToMany':
                    $relations[] = [
                        'name' => $config['name'],
                        'enabled' => true,
                        'type' => $config['type'],
                        'route' => $relationRoute,
                        'idKey' => $config['idKey'],
                        'labelKey' => $config['labelKey'],
                    ];
                    break;
                default:
                    $relationRoute = "{$route}.{$relationRoute}";
                    break;
            }
        }

        return $relations;
    }
}
