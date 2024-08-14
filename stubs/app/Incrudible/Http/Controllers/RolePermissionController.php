<?php

namespace App\Incrudible\Http\Controllers;

use Illuminate\Http\Request;
use App\Incrudible\Models\Role;

class RolePermissionController extends Controller
{
    // Update the permissions for a specific role
    public function update(Request $request, Role $role)
    {
        // Validate that permissions is an array of objects, each with an id
        $request->validate([
            'items' => 'array',
            'items.*.id' => 'required|exists:permissions,id',
        ]);

        // Extract the ids from the full permission objects
        $permissionIds = collect($request->input('items'))->pluck('id')->all();

        // Sync the permissions using the extracted ids
        $role->permissions()->sync($permissionIds);

        return redirect()->back()
            ->with('success', 'Permissions updated successfully.');
    }
}
