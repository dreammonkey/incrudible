<?php

namespace App\Incrudible\Models;

use App\Incrudible\Http\Resources\PermissionResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Transform model into a resource.
     */
    public function toResource(): PermissionResource
    {
        return new PermissionResource($this);
    }
}
