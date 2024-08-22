<?php

namespace App\Incrudible\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
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
            'username' => $this->username,
            'email' => $this->email,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'actions' => [
                [
                    'action' => 'show',
                    'url' => incrudible_route('admins.show', [
                        'admin' => $this->id
                    ]),
                ],
                [
                    'action' => 'edit',
                    'url' => incrudible_route('admins.edit', [
                        'admin' => $this->id
                    ]),
                ],
                [
                    'action' => 'destroy',
                    'url' => incrudible_route('admins.destroy', [
                        'admin' => $this->id
                    ]),
                ],
            ],
        ];
    }
}
