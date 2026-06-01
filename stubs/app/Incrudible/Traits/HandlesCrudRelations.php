<?php

namespace App\Incrudible\Traits;

use Incrudible\Incrudible\Facades\Incrudible;

trait HandlesCrudRelations
{
    /**
     * Define relations for the model.
     */
    public function relations(string $route, mixed $resource = null): array
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
                        'urls' => [
                            'index' => $this->relationUrl("{$relationRoute}.index", $resource),
                            'create' => $this->relationUrl("{$relationRoute}.create", $resource),
                        ],
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
                        'urls' => [
                            'value' => $this->relationUrl("{$relationRoute}.value", $resource),
                            'options' => $this->relationUrl("{$relationRoute}.options", $resource),
                            'update' => $this->relationUrl("{$relationRoute}.update", $resource),
                        ],
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

    private function relationUrl(string $name, mixed $resource = null): string
    {
        try {
            return incrudible_route($name, $resource ? [$resource] : [], false);
        } catch (\Throwable) {
            return '';
        }
    }
}
