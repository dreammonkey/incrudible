<?php

namespace App\Incrudible\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
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
                    'url' => incrudible_route('roles.show', [
                        'role' => $this->id,
                    ]),
                ],
                [
                    'action' => 'edit',
                    'url' => incrudible_route('roles.edit', [
                        'role' => $this->id,
                    ]),
                ],
                [
                    'action' => 'destroy',
                    'url' => incrudible_route('roles.destroy', [
                        'role' => $this->id,
                    ]),
                ],
            ],
        ];
    }
}
