@extends('layout')
@section('title', ($item ? 'Edit Role' : 'Add Role'))
@section('breadcrumbs')
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa fa-home"></i></a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles & Permissions</a></li>
	<li class="breadcrumb-item active">{{ $item ? 'Edit Role' : 'Add Role' }}</li>
</ol>
@endsection

@section('content')
<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-md-8">
			<div class="card">
				<div class="card-header">
					<h3 class="mb-0">{{ $item ? 'Edit Role' : 'Add Role' }}</h3>
				</div>
				<div class="card-body">
					<form method="POST" action="{{ $item ? route('admin.roles.update', $item->id) : route('admin.roles.store') }}">
						@csrf
						@if($item)
							@method('PUT')
						@endif
						<div class="mb-3">
							<label class="form-label">Name</label>
							<input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $item->name ?? '') }}" required>
							@error('name')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="mb-3">
							<label class="form-label">Permissions</label>
							<div class="mb-2 text-center">
								<input type="checkbox" id="checkAllPermsData" onclick="toggleAllPermsData(this)"> <label for="checkAllPermsData"><strong>Check all</strong></label>
							</div>
							@php
								// Group permissions by module (model field if available, else parse from name)
								$grouped = collect($permissions)->groupBy(function($perm) {
									if (isset($perm->model)) return $perm->model;
									$parts = explode(' ', $perm->name, 2);
									return count($parts) > 1 ? $parts[1] : 'Other';
								});
								$actions = ['View', 'Create', 'Edit', 'Delete', 'Restore', 'Export', 'Password', 'Payment', 'Approve'];
							@endphp
							<div class="row">
								@foreach($grouped as $module => $perms)
									<hr>
									<div class="col-12 my-2">
										<h6 class="mb-3 ">{{ $module }}</h6>
										<div class="row">
											@foreach($actions as $action)
												@php
													$perm = $perms->first(function($p) use ($action, $module) {
														return $p->name === ($action . ' ' . $module);
													});
												@endphp
												@if($perm)
													<div class="col-6 col-md-2">
														<div class="form-check">
															<input class="form-check-input perm-data" type="checkbox" name="permissions[]" value="{{ $perm->id }}" id="perm{{ $perm->id }}" 
																{{ (is_array(old('permissions')) && in_array($perm->id, old('permissions'))) || (isset($item) && $item->permissions->contains($perm->id)) ? 'checked' : '' }}>
															<label class="form-check-label" for="perm{{ $perm->id }}">{{ $action }}</label>
														</div>
													</div>
												@endif
											@endforeach
										</div>
									</div>
								@endforeach
							</div>
						</div>
						<script>
						function toggleAllPermsData(source) {
							document.querySelectorAll('.perm-data').forEach(cb => { cb.checked = source.checked; });
						}
						</script>
						@error('permissions')
							<div class="text-danger mb-2">{{ $message }}</div>
						@enderror
						<button type="submit" class="btn btn-{{ $item ? 'success' : 'primary' }}">{{ $item ? 'Update' : 'Create' }}</button>
						<a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancel</a>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
