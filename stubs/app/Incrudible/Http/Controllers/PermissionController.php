<?php

namespace App\Incrudible\Http\Controllers;

use App\Incrudible\Models\Permission;
use App\Incrudible\Traits\FormBuilder;
use App\Incrudible\Filters\SearchFilter;
use Illuminate\Support\Facades\Pipeline;
use App\Incrudible\Filters\SortingFilter;
use Incrudible\Incrudible\Facades\Incrudible;
use App\Incrudible\Http\Resources\PermissionResource;
use App\Incrudible\Http\Requests\Permission\GetPermissionsRequest;
use App\Incrudible\Http\Requests\Permission\StorePermissionRequest;
use App\Incrudible\Http\Requests\Permission\UpdatePermissionRequest;
use App\Incrudible\Http\Requests\Permission\DestroyPermissionRequest;

class PermissionController extends Controller
{
    use FormBuilder;

    /**
     * Display a listing of the resource.
     */
    public function index(GetPermissionsRequest $request)
    {
        if ($request->wantsJson()) {

            return PermissionResource::collection(
                Pipeline::send(
                    Permission::query(),
                )
                    ->through([
                        new SearchFilter(
                            search: $request->validated('search'),
                            fields: config('incrudible.permissions.index.searchable')
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

        return inertia('Permissions/Index', [
            'listable' => config('incrudible.permissions.index.listable'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Permissions/Create', [
            'fields' => config('incrudible.permissions.store.fields'),
            'rules' => config('incrudible.permissions.store.rules'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePermissionRequest $request)
    {
        $permission = Permission::create($request->validated());
        $prefix = Incrudible::routePrefix();

        return redirect()->route("$prefix.permissions.show", $permission)->with('success', 'Permission created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        $rules = (new StorePermissionRequest())->rules();
        $metadata = $this->generateFormMetadata($rules);

        return inertia('Permissions/Show', [
            'permission' => $permission->toResource(),
            'metadata' => $metadata,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        return inertia('Permissions/Edit', [
            'permission' => $permission->toResource(),
            'fields' => config('incrudible.permissions.update.fields'),
            'rules' => config('incrudible.permissions.update.rules'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        $permission->update($request->validated());

        return redirect()->back()->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyPermissionRequest $request, Permission $permission)
    {
        $permission->delete();

        return redirect()->back()->with('success', 'Permission deleted successfully.');
    }
}
