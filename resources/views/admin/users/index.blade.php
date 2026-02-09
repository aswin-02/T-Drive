@extends('layout')
@section('title', 'Users')
@section('breadcrumbs')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">Users</li>
</ol>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
                    <div class="flex-grow-1 d-flex justify-content-center">
                        <h3 class="mb-0 text-center w-100">Admin Users</h3>
                    </div>
                    <div class="ms-auto">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Add User</a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="list_table" class="table table-bordered table-hover align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center align-middle">S.No</th>
                                <th class="text-center align-middle">Name</th>
                                <th class="text-center align-middle">Email</th>
                                <th class="text-center align-middle">Mobile</th>
                                <th class="text-center align-middle">Role</th>
                                <th class="text-center align-middle">Status</th>
                                @can('Password Users')
                                    <th class="text-center align-middle">Password</th>
                                @endcan
                                <th class="text-center align-middle">Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            @push('scripts')
            <script>
                @can('View Users')
                    const columns = [
                                {data: 'DT_RowIndex' , name: 'DT_RowIndex', orderable: false, searchable: false},
                                {data: 'name'},
                                {data: 'email'},
                                {data: 'mobile'},
                                {data: 'role_name'},
                                {data: 'status'}
                            ]
                            @can('Password Users')
                                columns.push({data: 'password1'});
                            @endcan
                                columns.push({data: 'actions', orderable: false});
                    $(function () {
                        $('#list_table').DataTable({
                            "columnDefs": [
                                {"className": "dt-center", "targets": "_all"}
                            ],
                            @can('Export Users')
                                buttons: [
                                    'copy', 'csv', 'excel', 'pdf', 'print'
                                ],
                            @endcan
                            serverSide: true,
                            iDisplayLength: 10,
                            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                            ajax: {
                                url: '{{ route("admin.users.index") }}',
                                type: 'GET'
                            },
                            columns: columns,
                            processing: true,
                        });
                    });
                @endcan
            </script>
            @endpush
        </div>
    </div>
</div>
@endsection
