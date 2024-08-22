<?php

namespace App\Incrudible\Models;

use App\Incrudible\Http\Resources\RoleResource;
use Database\Factories\RoleFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'guard_name',
    ];

    /**
     * Transform model into a resource.
     */
    public function toResource(): RoleResource
    {
        return new RoleResource($this);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return RoleFactory::new();
    }
}
