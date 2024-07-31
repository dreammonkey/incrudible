<?php

namespace App\Incrudible\Models;

use App\Incrudible\Http\Resources\RoleResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
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
    public function toResource(): RoleResource
    {
        return new RoleResource($this);
    }
}
