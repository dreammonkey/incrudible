<?php

namespace App\Incrudible\Http\Controllers;

use App\Incrudible\Filters\SearchFilter;
use App\Incrudible\Filters\SortingFilter;
use App\Incrudible\Http\Requests\Admin\DestroyAdminRequest;
use App\Incrudible\Http\Requests\Admin\GetAdminsRequest;
use App\Incrudible\Http\Requests\Admin\StoreAdminRequest;
use App\Incrudible\Http\Requests\Admin\UpdateAdminRequest;
use App\Incrudible\Http\Resources\AdminResource;
use App\Incrudible\Models\Admin;
use App\Incrudible\Traits\FormBuilder;
use Illuminate\Support\Facades\Pipeline;
use Incrudible\Incrudible\Facades\Incrudible;

class AdminController extends Controller
{
    use FormBuilder;

    /**
     * Display a listing of the resource.
     */
    public function index(GetAdminsRequest $request)
    {
        if ($request->wantsJson()) {

            return AdminResource::collection(
                Pipeline::send(
                    Admin::query(),
                )
                    ->through([
                        new SearchFilter(
                            search: $request->validated('search'),
                            fields: config('incrudible.admins.index.searchable')
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

        return inertia('Admins/Index', [
            'listable' => config('incrudible.admins.index.listable'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Admins/Create', [
            'fields' => config('incrudible.admins.store.fields'),
            'rules' => config('incrudible.admins.store.rules'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAdminRequest $request)
    {
        $admin = Admin::create($request->validated());
        $prefix = Incrudible::routePrefix();

        return redirect()->route("$prefix.admins.show", $admin)->with('success', 'Admin created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        $rules = (new StoreAdminRequest)->rules();
        $metadata = $this->generateFormMetadata($rules);

        return inertia('Admins/Show', [
            'admin' => $admin->toResource(),
            'metadata' => $metadata,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Admin $admin)
    {
        return inertia('Admins/Edit', [
            'admin' => $admin->toResource(),
            'fields' => config('incrudible.admins.update.fields'),
            'rules' => config('incrudible.admins.update.rules'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAdminRequest $request, Admin $admin)
    {
        $admin->update($request->validated());

        return redirect()->back()->with('success', 'Admin updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyAdminRequest $request, Admin $admin)
    {
        $admin->delete();

        return redirect()->back()->with('success', 'Admin deleted successfully.');
    }
}
