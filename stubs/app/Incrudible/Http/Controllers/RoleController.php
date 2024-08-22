<?php

namespace App\Incrudible\Http\Controllers;

use App\Incrudible\Filters\SearchFilter;
use App\Incrudible\Filters\SortingFilter;
use App\Incrudible\Http\Requests\Role\DestroyRoleRequest;
use App\Incrudible\Http\Requests\Role\GetRolesRequest;
use App\Incrudible\Http\Requests\Role\StoreRoleRequest;
use App\Incrudible\Http\Requests\Role\UpdateRoleRequest;
use App\Incrudible\Http\Resources\RoleResource;
use App\Incrudible\Models\Role;
use App\Incrudible\Traits\HandlesCrudRelations;
use Illuminate\Support\Facades\Pipeline;
use Incrudible\Incrudible\Facades\Incrudible;

/*NESTED BABY*/

class RoleController extends Controller
{
    use HandlesCrudRelations;

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
            ...config('incrudible.roles.index'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Roles/Create', [
            ...config('incrudible.roles.store'),
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
            ...config('incrudible.roles.update'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        return inertia('Roles/Edit', [
            'role' => $role->toResource(),
            ...config('incrudible.roles.update'),
            'relations' => $this->relations('roles'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
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
