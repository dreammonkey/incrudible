<?php

namespace App\Incrudible\Http\Controllers;

use App\Incrudible\Models\Admin;
use Illuminate\Http\Request;

class AdminRoleController extends Controller
{
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
