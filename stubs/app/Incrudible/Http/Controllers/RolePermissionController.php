<?php

namespace App\Incrudible\Http\Controllers;

use App\Incrudible\Http\Resources\PermissionResource;
use App\Incrudible\Models\Permission;
use App\Incrudible\Models\Role;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    /**
     * Retrieve all permissions except the ones already assigned to the role.
     */
    public function options(Role $role)
    {
        return PermissionResource::collection(
            Permission::whereNotIn('id', $role->permissions->pluck('id'))->get()
        );
    }

    /**
     * Get all permissions assigned to the role.
     */
    public function value(Role $role)
    {
        return PermissionResource::collection($role->permissions);
    }

    /**
     * Update the permissions for a specific role.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'items' => 'array',
            'items.*.id' => 'required|exists:permissions,id',
        ]);

        $modelIds = collect($request->input('items'))->pluck('id')->all();

        $role->permissions()->sync($modelIds);

        return redirect()->back()
            ->with('success', 'Permissions updated successfully.');
    }
}
