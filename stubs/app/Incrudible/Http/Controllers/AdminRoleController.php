<?php

namespace App\Incrudible\Http\Controllers;

use App\Incrudible\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use App\Incrudible\Models\Admin;
use App\Incrudible\Models\Role;

class AdminRoleController extends Controller
{
    /**
     * Retrieve all roles except the ones already assigned to the admin.
     */
    public function options(Admin $admin)
    {
        return RoleResource::collection(
            Role::whereNotIn('id', $admin->roles->pluck('id'))->get()
        );
    }

    /**
     * Get all roles assigned to the admin.
     */
    public function value(Admin $admin)
    {
        return RoleResource::collection($admin->roles);
    }

    // Update the roles for a specific admin
    public function update(Request $request, Admin $admin)
    {
        // Validate that roles is an array of objects, each with an id
        $request->validate([
            'items' => 'array',
            'items.*.id' => 'required|exists:roles,id',
        ]);

        // Extract the ids from the full role objects
        $roleIds = collect($request->input('items'))->pluck('id')->all();

        // Sync the roles using the extracted ids
        $admin->roles()->sync($roleIds);

        return redirect()->back()
            ->with('success', 'Roles updated successfully.');
    }
}
