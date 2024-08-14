<?php

namespace App\Incrudible\Http\Controllers;

use App\Incrudible\Models\Role;
use App\Incrudible\Filters\SearchFilter;
use Illuminate\Support\Facades\Pipeline;
use App\Incrudible\Filters\SortingFilter;
use Incrudible\Incrudible\Facades\Incrudible;
use App\Incrudible\Http\Resources\RoleResource;
use App\Incrudible\Http\Requests\Role\GetRolesRequest;
use App\Incrudible\Http\Requests\Role\StoreRoleRequest;
use App\Incrudible\Http\Requests\Role\UpdateRoleRequest;
use App\Incrudible\Http\Requests\Role\DestroyRoleRequest;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(GetRolesRequest $request)
    {
        if ($request->wantsJson()) {

            return RoleResource::collection(

                Pipeline::send(
                    Role::query(),
                )
                    ->through([
                        new SearchFilter(
                            search: $request->validated('search'),
                            fields: config('incrudible.roles.index.searchable')
                        ),
                        new SortingFilter(
                            orderBy: $request->validated('orderBy'),
                            orderDir: $request->validated('orderDir')
                        ),
                    ])
                    ->thenReturn()
                    ->paginate($request->validated('perPage', 25))
            );
        }

        return inertia('Roles/Index', [
            'listable' => config('incrudible.roles.index.listable'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Roles/Create', [
            'fields' => config('incrudible.roles.store.fields'),
            'rules' => config('incrudible.roles.store.rules'),
            'relations' => [
                [
                    'name' => 'permissions',
                    'enabled' => false,
                    'type' => 'BelongsToMany',
                    // 'model' => Permission::class,
                    // 'routeKey' => Incrudible::routePrefix() . '.permissions.index',
                    'value' => [],
                ],
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        $role = Role::create($request->validated());
        $prefix = Incrudible::routePrefix();

        return redirect()->route("$prefix.roles.show", $role)->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        return inertia('Roles/Show', [
            'role' => $role->toResource(),
            'fields' => config('incrudible.roles.update.fields'),
            'rules' => config('incrudible.roles.update.rules'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        return inertia('Roles/Edit', [
            'role' => $role->toResource(),
            'fields' => config('incrudible.roles.update.fields'),
            'rules' => config('incrudible.roles.update.rules'),
            'relations' => [
                [
                    'name' => 'permissions',
                    'enabled' => true,
                    'type' => 'BelongsToMany',
                    // 'model' => Permission::class,
                    'indexRoute' => incrudible_route('permissions.index'),
                    'storeRoute' => incrudible_route('roles.permissions.update', $role),
                    'value' => $role->permissions,
                ],
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        $validated = $request->validated();

        // Sync permissions
        if (isset($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }
        $role->update($request->validated());

        return redirect()->back()->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyRoleRequest $request, Role $role)
    {
        $role->delete();

        return redirect()->back()->with('success', 'Role deleted successfully.');
    }
}
