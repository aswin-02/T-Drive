@extends('layout')
@section('title', 'Roles & Permissions')
@section('breadcrumbs')
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa fa-home"></i></a></li>
	<li class="breadcrumb-item active">Roles & Permissions</li>
</ol>
@endsection

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="card mb-4">
				<div class="card-header d-flex align-items-center justify-content-between flex-wrap">
					<div class="flex-grow-1 d-flex justify-content-center">
						<h3 class="mb-0 text-center w-100">Roles & Permissions</h3>
					</div>
					<div class="ms-auto">
						<a href="{{ route('admin.roles.create') }}" class="btn btn-primary">Add Role</a>
					</div>
				</div>
				<div class="card-body">
					<table id="list_table" class="table table-bordered table-hover align-middle" style="width:100%">
						<thead>
							<tr>
								<th class="text-center align-middle">S.No</th>
								<th class="text-center align-middle">Role Name</th>
								<th class="text-center align-middle">Status</th>
								<th class="text-center align-middle">Actions</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
			@push('scripts')
			<script>
			$(function() {
				if ($.fn.DataTable.isDataTable('#list_table')) {
					$('#list_table').DataTable().destroy();
				}
				var table = $('#list_table').DataTable({
					processing: true,
					serverSide: true,
					ajax: '{{ route('admin.roles.index') }}',
					columns: [
						{ data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
						{ data: 'name', name: 'name' },
						{ data: 'status', name: 'status', className: 'text-center', orderable: false, searchable: false },
						{ data: 'actions', name: 'actions', className: 'text-center', orderable: false, searchable: false },
					],
					order: [[1, 'asc']],
				});
			});
			</script>
			@endpush
		</div>
	</div>
</div>
@endsection
