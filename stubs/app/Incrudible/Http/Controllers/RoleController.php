<?php

namespace App\Incrudible\Http\Controllers;

use App\Incrudible\Filters\SearchFilter;
use App\Incrudible\Filters\SortingFilter;
use App\Incrudible\Http\Requests\Role\DeleteRoleRequest;
use App\Incrudible\Http\Requests\Role\GetRolesRequest;
use App\Incrudible\Http\Requests\Role\StoreRoleRequest;
use App\Incrudible\Http\Requests\Role\UpdateRoleRequest;
use App\Incrudible\Http\Resources\RoleResource;
use App\Incrudible\Models\Permission;
use App\Incrudible\Models\Role;
use App\Incrudible\Traits\FormBuilder;
use Illuminate\Support\Facades\Pipeline;
use Incrudible\Incrudible\Facades\Incrudible;

class RoleController extends Controller
{
    use FormBuilder;

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
                            fields: [
                                'name',
                                'guard_name',
                            ]
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

        return inertia('Roles/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $rules = (new StoreRoleRequest())->rules();

        $metadata = $this->generateFormMetadata($rules);

        return inertia('Roles/Create', [
            'role' => Role::make()->toResource(),
            'metadata' => $metadata,
            'relations' => [
                [
                    'name' => 'permissions',
                    'enabled' => false,
                    'type' => 'BelongsToMany',
                    'model' => Permission::class,
                    'routeKey' => Incrudible::routePrefix().'.permissions.index',
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
        $metadata = $this->getFormMetaData('roles');

        return inertia('Roles/Show', [
            'role' => $role->toResource(),
            'metadata' => $metadata,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $rules = (new StoreRoleRequest())->rules();

        $metadata = $this->generateFormMetadata($rules);

        // $role->load('permissions');  // Eager loading the permissions

        return inertia('Roles/Edit', [
            'role' => $role->toResource(),
            'metadata' => $metadata,
            // 'relations' => [
            //     [
            //         'name' => 'permissions',
            //         'enabled' => true,
            //         'type' => 'BelongsToMany',
            //         'model' => Permission::class,
            //         'routeKey' => Incrudible::routePrefix() . '.permissions.index',
            //         'value' => $role->permissions,
            //     ],
            // ],
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
    public function destroy(DeleteRoleRequest $request, Role $role)
    {
        $role->delete();

        return redirect()->back()->with('success', 'Role deleted successfully.');
    }
}
