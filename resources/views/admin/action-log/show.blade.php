@extends('admin.layout')
@section('title', 'Action Log Details')
@section('breadcrumbs')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.action-logs.index') }}">Action Logs</a></li>
    <li class="breadcrumb-item active">Log #{{ $log->id }}</li>
</ol>
@endsection
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Action Log Details</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">ID</dt>
                        <dd class="col-sm-8">{{ $log->id }}</dd>
                        <dt class="col-sm-4">User</dt>
                        <dd class="col-sm-8">
                            @if($log->user)
                                {{ $log->user->name }} 
                                <span class="badge bg-{{ $log->user->user_type == 'admin' ? 'primary' : 'info' }} ms-1">{{ $log->user->user_type }}</span>
                            @else
                                -
                            @endif
                        </dd>
                        <dt class="col-sm-4">Action</dt>
                        <dd class="col-sm-8">{{ $log->action }}</dd>
                        <dt class="col-sm-4">Model</dt>
                        <dd class="col-sm-8">{{ $log->model }}</dd>
                        <dt class="col-sm-4">Model ID</dt>
                        <dd class="col-sm-8">{{ $log->refer_id }}</dd>
                        <dt class="col-sm-4">IP</dt>
                        <dd class="col-sm-8">{{ $log->ip }}</dd>
                        <dt class="col-sm-4">Time</dt>
                        <dd class="col-sm-8">{{ $log->created_at ? $log->created_at->format('d-M-y h:i:s A') : '-' }}</dd>
                        
                        @if($log->action == 'update' && $log->old_data && $log->new_data)
                            <dt class="col-sm-12 mt-3"><h5>Changed Fields</h5></dt>
                            <dd class="col-sm-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 25%">Field</th>
                                                <th style="width: 37.5%">Old Value</th>
                                                <th style="width: 37.5%">New Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $oldData = is_array($log->old_data) ? $log->old_data : json_decode(json_encode($log->old_data), true);
                                                $newData = is_array($log->new_data) ? $log->new_data : json_decode(json_encode($log->new_data), true);
                                                $changedFields = [];
                                                
                                                foreach ($newData as $key => $newValue) {
                                                    $oldValue = $oldData[$key] ?? null;
                                                    if ($oldValue != $newValue && !in_array($key, ['updated_at', 'password', 'remember_token'])) {
                                                        $changedFields[$key] = [
                                                            'old' => $oldValue,
                                                            'new' => $newValue
                                                        ];
                                                    }
                                                }
                                            @endphp
                                            
                                            @forelse($changedFields as $field => $values)
                                                <tr>
                                                    <td class="fw-bold">{{ ucwords(str_replace('_', ' ', $field)) }}</td>
                                                    <td>
                                                        <span class="badge bg-danger">
                                                            {{ is_array($values['old']) ? json_encode($values['old']) : ($values['old'] ?? 'null') }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-success">
                                                            {{ is_array($values['new']) ? json_encode($values['new']) : ($values['new'] ?? 'null') }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted">No changes detected</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </dd>
                        @endif
                        
                        <dt class="col-sm-4 mt-3">Old Data</dt>
                        <dd class="col-sm-8 mt-3"><pre class="p-2 border">{{ json_encode($log->old_data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre></dd>
                        <dt class="col-sm-4">New Data</dt>
                        <dd class="col-sm-8"><pre class="p-2 border">{{ json_encode($log->new_data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre></dd>
                    </dl>
                    <a href="{{ route('admin.action-logs.index') }}" class="btn btn-secondary">Back to Logs</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
