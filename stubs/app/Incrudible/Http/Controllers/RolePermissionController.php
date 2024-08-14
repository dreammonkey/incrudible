<?php

namespace App\Incrudible\Http\Controllers;

use App\Incrudible\Models\Role;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    // Update the permissions for a specific role
    public function update(Request $request, Role $role)
    {
        // dd($request->all());
        $request->validate([
            'permissions' => 'array|exists:permissions,id',
        ]);

        $role->permissions()->sync($request->input('permissions', []));

        return redirect()->back()
            ->with('success', 'Permissions updated successfully.');
    }
}
