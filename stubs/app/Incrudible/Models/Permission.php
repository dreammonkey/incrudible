<?php

namespace App\Incrudible\Models;

use App\Incrudible\Http\Resources\PermissionResource;
use Database\Factories\PermissionFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
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
    public function toResource(): PermissionResource
    {
        return new PermissionResource($this);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return PermissionFactory::new();
    }
}
