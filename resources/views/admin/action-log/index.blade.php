@extends('admin.layout')
@section('title', 'Action Logs')
@section('breadcrumbs')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">Action Logs</li>
</ol>
@endsection
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-center align-items-center">
                    <h3 class="mb-0">Action Logs</h3>
                </div>
                <div class="card-body table-responsive">
                    <table id="actionlog_table" class="table table-bordered table-hover align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center align-middle">Action</th>
                                <th class="text-center align-middle">Menu</th>
                                <th class="text-center align-middle">Model ID</th>
                                <th class="text-center align-middle">IP</th>
                                <th class="text-center align-middle">Time</th>
                                <th class="text-center align-middle">User</th>
                                <th class="text-center align-middle">View</th>
                            </tr>
                        </thead>
                    </table>
                    @push('scripts')
                    <script>
                    $(function() {
                        $('#actionlog_table').DataTable({
                            processing: true,
                            serverSide: true,
                            ajax: window.location.href,
                            columns: [
                                
                                { data: 'action', name: 'action' },
                                { data: 'model', name: 'model' },
                                { data: 'refer_id', name: 'refer_id' },
                                { data: 'ip', name: 'ip' },
                                { data: 'created_at', name: 'created_at' },
                                { data: 'user_name', name: 'user_name', orderable: false, searchable: false },
                                { data: 'view', name: 'view', orderable: false, searchable: false, className: 'text-center' },
                            ],
                            order: [[0, 'desc']]
                        });
                    });
                    </script>
                    @endpush
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
