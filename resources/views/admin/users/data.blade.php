@extends('layout')
@section('title', ($item ? 'Edit User' : 'Add User'))
@section('breadcrumbs')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
    <li class="breadcrumb-item active">{{ $item ? 'Edit User' : 'Add User' }}</li>
</ol>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">{{ $item ? 'Edit User' : 'Add User' }}</h3>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" action="{{ $item ? route('admin.users.update', $item->id) : route('admin.users.store') }}">
                        @csrf
                        @if($item)
                            @method('PUT')
                        @endif
                        <div class="mb-3">
                            <div class="d-flex justify-content-center align-items-center">
                                <div class="text-center">
                                    <label class="d-block">Profile Image</label>
                                    <div id="image-dropzone" class="image-box border rounded d-flex align-items-center justify-content-center flex-column text-center"
                                        style="width: 200px; height: 200px; cursor: pointer; background: #f8f9fa; border: 2px dashed #ccc;">
                                        <i class="fa fa-upload fa-2x text-secondary"></i>
                                        <p class="text-muted m-0">Drag &amp; drop a file here or click</p>
                                        <img id="image-preview" src="" class="img-fluid d-none" style="max-width: 100%; max-height: 100%;" alt="">
                                    </div>
                                    <input type="file" id="image-input" name="image" class="d-none" accept="image/*">
                                </div>
                                @error('image')
                                    <span class="error invalid-feedback">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $item->name ?? '') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6 mt-3 mt-md-0">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $item->email ?? '') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Mobile <span class="text-danger">*</span></label>
                                <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile', $item->mobile ?? '') }}" required>
                                @error('mobile')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6 mt-3 mt-md-0">
                                <label class="form-label">Role <span class="text-danger">*</span></label>
                                <select name="role_id" class="form-control select2 @error('role_id') is-invalid @enderror" required>
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id', $item->role_id ?? null) == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12 col-md-6 mt-3 mt-md-0">
                                <label class="form-label">Active Status <span class="text-danger">*</span></label>
                                <select name="is_active" class="form-control @error('is_active') is-invalid @enderror" required>
                                    <option value="1" {{ old('is_active', $item->is_active ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('is_active', $item->is_active ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <hr class="my-3">
                        <div class="row mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">New Password @if(!$item)<span class="text-danger">*</span>@endif</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" @if(!$item) required @endif>
                                @if(!$item)
                                    <small class="form-text text-muted">Password is required for new users.</small>
                                @else
                                    <small class="form-text text-muted">Leave blank to keep current password.</small>
                                @endif
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6 mt-3 mt-md-0">
                                <label class="form-label">Confirm New Password @if(!$item)<span class="text-danger">*</span>@endif</label>
                                <input type="password" name="password_confirmation" class="form-control" @if(!$item) required @endif>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12 text-end">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-{{ $item ? 'success' : 'primary' }}">{{ $item ? 'Update' : 'Create' }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        @if(old('image'))
            $("#image-preview").removeClass("d-none").attr("src", "{{ old('image') }}");
            $("#image-dropzone i, #image-dropzone p").hide();
        @elseif($item && $item->profile_photo)
            $("#image-preview").removeClass("d-none").attr("src", "{{ $item->profile_photo }}");
            $("#image-dropzone i, #image-dropzone p").hide();
        @endif
        getDistricts();
    });
</script>
@endsection
