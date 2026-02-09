<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Logger;
use App\Http\Controllers\Controller;
use App\Models\Admin\Role;
use App\Models\User;
use App\Http\Requests\Admin\UserRequest;
use App\Models\State;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('View Users');
        if($request->ajax()){
            return $this->indexAPI(app(DataTables::class));
        }
        return view('admin.users.index');
    }

    // API endpoint for DataTables AJAX
    public function indexAPI(DataTables $datatables)
    {
        $this->authorize('View Users');
        $query = User::where('user_type', 'admin')->where('is_show',1)->withTrashed();
        return $datatables->eloquent($query)
            ->addIndexColumn()
            ->addColumn('status', function ($user) {
                if ($user->trashed()) {
                    return '<span class="badge bg-danger">Deleted</span>';
                } elseif ($user->is_active == 0) {
                    return '<span class="badge bg-warning">Inactive</span>';
                } else {
                    return '<span class="badge bg-success">Active</span>';
                }
            })
            ->addColumn('role_name', function($data) {
                $role = $data->roles->first();
                return $role ? $role->name : '';
            })
            ->addColumn('password1', function($data) {
                if (auth()->user()->can('Password Users') && $data->password1) {
                    return EncryptDecrypt("decrypt", $data->password1);
                }
                return '';
            })
            ->addColumn('actions', function ($user) {
                // Note: $this is not available in closure, so permission checks must be handled in controller or via policies if needed
                $canRestore = auth()->user() && auth()->user()->can('Restore Users');
                $canEdit = auth()->user() && auth()->user()->can('Edit Users');
                $canDelete = auth()->user() && auth()->user()->can('Delete Users');
                $button = '<div class="d-flex justify-content-center">';
                if ($user->trashed()) {
                    if ($canRestore) {
                        $button .= '<a href="javascript:void(0);" onclick="commonRestore(\'' . route('admin.users.restore', $user->id) . '\', \'User\')" class="btn btn-warning btn-sm m-1"><i class="fa fa-undo"></i></a>';
                    } else {
                        $button .= '-';
                    }
                } else {
                    if ($canEdit) {
                        $button .= '<a href="' . route('admin.users.edit', $user->id) . '" class="btn btn-warning btn-sm m-1"><i class="fa fa-pencil"></i></a>';
                    }
                    if ($canDelete) {
                        $button .= '<a href="javascript:void(0);" onclick="commonDelete(\'' . route('admin.users.destroy', $user->id) . '\', \'User\')" class="btn btn-danger btn-sm m-1"><i class="fa fa-trash"></i></a>';
                    }
                }
                $button .= '</div>';
                return $button;
            })
            ->rawColumns(['status', 'actions'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('Create Users');
        $item = null;
        $roles = Role::whereNot('id','1')->get();
        return view('admin.users.data', compact('item', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $this->authorize('Create Users');
        try {
           DB::beginTransaction();
            $data = $request->validated();
            $data['password1'] = EncryptDecrypt("encrypt",$data['password']);
            $data['password'] = Hash::make($data['password']);

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')?->store('users', 'public');
            }
            $data['user_type'] = 'admin';
            $user = User::create($data);
            $role = Role::findOrFail($data['role_id']);
            $user->assignRole($role->name);
            DB::commit();
            Logger::log('create', $user, null, $user->toArray());
            return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            Log::error('[UserController@store] QueryException: ' . $e->getMessage(), ['exception' => $e]);
            $errorMsg = 'An error occurred while creating the user.';
            if ($e->getCode() == 23000) {
                $errorMsg = 'Email or mobile already exists.';
            }
            return back()->withErrors(['name' => $errorMsg])->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[UserController@store] Exception: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors(['name' => 'Unexpected error: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $this->authorize('Edit Users');
        $item = $user;
        $roles = Role::whereNot('id','1')->get();
        return view('admin.users.data', compact('item', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        $this->authorize('Edit Users');
        $data = $request->validated();
        if (!empty($data['password'])) {
           $data['password1'] = EncryptDecrypt("encrypt",$data['password']);
           $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
            unset($data['password1']);
        }
        try {
            DB::beginTransaction();
            $old = $user->toArray();
            if ($request->hasFile('image')) {
                $oldImage = $user->image;
                $data['image'] = $request->file('image')->store('users', 'public');
            }
            $user->update($data);

            $role = Role::findOrFail($data['role_id']);
            $user->syncRoles([$role->name]);

            DB::commit();

            if (isset($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
            Logger::log('update', $user, $old, $user->toArray());
            return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            // Delete uploaded image if transaction failed
            if (isset($data['image'])) {
                Storage::disk('public')->delete($data['image']);
            }
            Log::error('[UserController@update] QueryException: ' . $e->getMessage(), ['exception' => $e]);
            $errorMsg = 'An error occurred while updating the user.';
            if ($e->getCode() == 23000) {
                $errorMsg = 'Email or mobile already exists.';
            }
            return back()->withErrors(['name' => $errorMsg])->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[UserController@update] Exception: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors(['name' => 'Unexpected error: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('Delete Users');
        try {
            $user = User::where('user_type', 'admin')->findOrFail($id);
            $user->delete();
            Logger::log('delete', $user, null, null);
            return response()->json(['success' => true, 'message' => 'User deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('[UserController@destroy] Exception: ' . $e->getMessage(), ['exception' => $e]);
            $errorMsg = 'Unexpected error: ' . $e->getMessage();
            return response()->json(['success' => false, 'message' => $errorMsg]);
        }
    }

    /**
     * Restore a soft-deleted user.
     */
    public function restore($id)
    {
        $this->authorize('Restore Users');
        try {
            $user = User::withTrashed()->where('user_type', 'admin')->findOrFail($id);
            $user->restore();
            Logger::log('restore', $user, null, null);
            $message = 'User restored successfully.';
            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            Log::error('[UserController@restore] Exception: ' . $e->getMessage(), ['exception' => $e]);
            $errorMsg = 'Unexpected error: ' . $e->getMessage();
            return response()->json(['success' => false, 'message' => $errorMsg], 500);
        }
    }

    /**
     * Approve bank details for a user.
     */
    public function approveBankDetails($id)
    {
        $this->authorize('Edit Users');
        try {
            DB::beginTransaction();
            
            $user = User::findOrFail($id);
            $old = $user->toArray();
            
            $user->update([
                'bank_details_approved' => true,
                'bank_details_rejection_reason' => null,
            ]);
            
            Logger::log('update', $user, $old, $user->toArray());
            
            DB::commit();
            
            return response()->json(['success' => true, 'message' => 'Bank details approved successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[UserController@approveBankDetails] Exception: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Failed to approve bank details.'], 500);
        }
    }

    /**
     * Reject bank details for a user.
     */
    public function rejectBankDetails(Request $request, $id)
    {
        $this->authorize('Edit Users');
        
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);
        
        try {
            DB::beginTransaction();
            
            $user = User::findOrFail($id);
            $old = $user->toArray();
            
            $user->update([
                'bank_details_approved' => false,
                'bank_details_rejection_reason' => $request->rejection_reason,
            ]);
            
            Logger::log('update', $user, $old, $user->toArray());
            
            DB::commit();
            
            return response()->json(['success' => true, 'message' => 'Bank details rejected.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[UserController@rejectBankDetails] Exception: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Failed to reject bank details.'], 500);
        }
    }
}
