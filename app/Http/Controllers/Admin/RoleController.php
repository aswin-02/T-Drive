<?php


namespace App\Http\Controllers\Admin;

use App\Helpers\Logger;
use App\Http\Controllers\Controller;
use App\Models\Admin\Permission;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\RoleRequest;
use App\Models\Admin\Role;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('View Roles');
        if ($request->ajax()) {
            return $this->indexAPI(app(DataTables::class));
        }
        return view('admin.roles.index');
    }

    // API endpoint for DataTables AJAX
    public function indexAPI(DataTables $datatables)
    {
        $query = Role::with(['permissions'])->whereNot('name', 'Admin Developing-Team')->withTrashed();
        return $datatables->eloquent($query)
            ->addIndexColumn()
            ->addColumn('status', function ($role) {
                return $role->trashed()
                    ? '<span class="badge bg-danger">Deleted</span>'
                    : '<span class="badge bg-success">Active</span>';
            })
            ->addColumn('actions', function ($role) {
                // Note: $this is not available in closure, so permission checks must be handled in controller or via policies if needed
                $canRestore = auth()->user() && auth()->user()->can('Restore Roles');
                $canEdit = auth()->user() && auth()->user()->can('Edit Roles');
                $canDelete = auth()->user() && auth()->user()->can('Delete Roles');
                if ($role->name === 'Admin Developing-Team') {
                    return '--';
                }
                $button = '<div class="d-flex justify-content-center">';
                if ($role->trashed()) {
                    if ($canRestore) {
                        $button .= '<a href="javascript:void(0);" onclick="commonRestore(\'' . route('admin.roles.restore', $role->id) . '\', \'Role\')" class="btn btn-warning btn-sm m-1" title="Restore"><i class="ri-arrow-go-back-line"></i></a>';
                    } else {
                        $button .= '-';
                    }
                } else {
                    if ($canEdit) {
                        $button .= '<a href="' . route('admin.roles.edit', $role->id) . '" class="btn btn-warning btn-sm m-1" title="Edit"><i class="ri-pencil-line"></i></a>';
                    }
                    if ($canDelete) {
                        $button .= '<a href="javascript:void(0);" onclick="commonDelete(\'' . route('admin.roles.destroy', $role->id) . '\', \'Role\')" class="btn btn-danger btn-sm m-1" title="Delete"><i class="ri-delete-bin-line"></i></a>';
                    }
                }
                $button .= '</div>';
                return $button;
            })
            ->rawColumns(['permissions', 'status', 'actions'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('Create Roles');
        $permissions = Permission::all();
        $item = null;
        return view('admin.roles.data', compact('permissions', 'item'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request)
    {
        $this->authorize('Create Roles');
        $validated = $request->validated();
        try {
            DB::beginTransaction();
            $role = new Role();
            $role->name = $validated['name'];
            $role->save();
            if ($request->has('permissions')) {
                $role->permissions()->sync($request->input('permissions', []));
            }
            DB::commit();
            $new = $role->fresh()->load('permissions')->toArray();
            Logger::log('create', $role, null, $new);
            return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            Log::error('[RoleController@store] QueryException: ' . $e->getMessage(), ['exception' => $e]);
            $errorMsg = 'An error occurred while creating the role.';
            if ($e->getCode() == 23000) {
                $errorMsg = 'Role name already exists.';
            }
            return back()->withErrors(['name' => $errorMsg])->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[RoleController@store] Exception: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors(['name' => 'Unexpected error: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->authorize('Edit Roles');
        $item = Role::with(['permissions'])->findOrFail($id);
        $permissions = Permission::all();
        return view('admin.roles.data', compact('permissions', 'item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, $id)
    {
        $validated = $request->validated();
        $model = Role::withTrashed()->findOrFail($id);
        try {
            DB::beginTransaction();
            $old = $model->load('permissions')->toArray();
            $model->name = $validated['name'];
            $model->save();
            if ($request->has('permissions')) {
                $model->permissions()->sync($request->input('permissions', []));
            }
            DB::commit();
            $new = $model->fresh()->load('permissions')->toArray();
            Logger::log('update', $model, $old, $new);
            return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            Log::error('[RoleController@update] QueryException: ' . $e->getMessage(), ['exception' => $e]);
            $errorMsg = 'An error occurred while updating the role.';
            if ($e->getCode() == 23000) {
                $errorMsg = 'Role name already exists.';
            }
            return back()->withErrors(['name' => $errorMsg])->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[RoleController@update] Exception: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors(['name' => 'Unexpected error: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('Delete Roles');
        try {
            $role = Role::findOrFail($id);
            $role->delete();
            Logger::log('delete', $role, null, null);
            return response()->json(['success' => true, 'message' => 'Role deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('[RoleController@destroy] Exception: ' . $e->getMessage(), ['exception' => $e]);
            $errorMsg = 'Unexpected error: ' . $e->getMessage();
            return response()->json(['success' => false, 'message' => $errorMsg]);
        }
    }

    /**
     * Restore a soft-deleted role.
     */
    public function restore($id)
    {
        $this->authorize('Restore Roles');
        try {
            $role = Role::withTrashed()->findOrFail($id);
            $role->restore();
            Logger::log('restore', $role, null, null);
            $message = 'Role restored successfully.';
            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            Log::error('[RoleController@restore] Exception: ' . $e->getMessage(), ['exception' => $e]);
            $errorMsg = 'Unexpected error: ' . $e->getMessage();
            return response()->json(['success' => false, 'message' => $errorMsg], 500);
        }
    }
}
