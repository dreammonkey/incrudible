<?php

namespace App\Incrudible\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'guard_name' => $this->guard_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            'actions' => [
                [
                    'action' => 'show',
                    'url' => incrudible_route('permissions.show', [
                        'permission' => $this->id
                    ]),
                ],
                [
                    'action' => 'edit',
                    'url' => incrudible_route('permissions.edit', [
                        'permission' => $this->id
                    ]),
                ],
                [
                    'action' => 'destroy',
                    'url' => incrudible_route('permissions.destroy', [
                        'permission' => $this->id
                    ]),
                ],
            ],
        ];
    }
}