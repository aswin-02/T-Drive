@extends('layout')
@section('title', 'Shared With Me')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="ri-share-line mr-2"></i>
                            Shared With Me
                        </h4>
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                            <i class="ri-arrow-left-line mr-1"></i> Back to Dashboard
                        </a>
                    </div>
                    <div class="card-body">
                        @if($shares->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>File/Folder</th>
                                            <th>Type</th>
                                            <th>Shared By</th>
                                            <th>Permission</th>
                                            <th>Shared On</th>
                                            <th>Expires</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($shares as $share)
                                            @php
                                                $item = $share->shareable;
                                                $isFile = $share->shareable_type === 'file';
                                            @endphp
                                            <tr>
                                                <td>
                                                    @if($isFile)
                                                        <i class="ri-file-line mr-2"></i>
                                                        {{ $item->original_name }}
                                                    @else
                                                        <i class="ri-folder-line mr-2"></i>
                                                        {{ $item->name }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($isFile)
                                                        <span class="badge badge-primary">File</span>
                                                    @else
                                                        <span class="badge badge-info">Folder</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <i class="ri-user-line mr-1"></i>
                                                    {{ $share->owner->name }}
                                                </td>
                                                <td>
                                                    @if($share->permission === 'view')
                                                        <span class="badge badge-secondary">View</span>
                                                    @elseif($share->permission === 'download')
                                                        <span class="badge badge-success">Download</span>
                                                    @elseif($share->permission === 'edit')
                                                        <span class="badge badge-warning">Edit</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $share->created_at->format('M d, Y') }}
                                                </td>
                                                <td>
                                                    @if($share->expires_at)
                                                        @if($share->isExpired())
                                                            <span class="badge badge-danger">Expired</span>
                                                        @else
                                                            <span class="text-warning">
                                                                {{ $share->expires_at->diffForHumans() }}
                                                            </span>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">Never</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(!$share->isExpired())
                                                        @if($isFile)
                                                            <a href="{{ route('files.view', $item->id) }}" class="btn btn-sm btn-primary"
                                                                title="View File">
                                                                <i class="ri-eye-line"></i>
                                                            </a>
                                                            @if(in_array($share->permission, ['download', 'edit']))
                                                                <a href="{{ route('files.download', $item->id) }}"
                                                                    class="btn btn-sm btn-success ml-1" title="Download File">
                                                                    <i class="ri-download-line"></i>
                                                                </a>
                                                            @endif
                                                        @else
                                                            <button class="btn btn-sm btn-primary" title="View Folder">
                                                                <i class="ri-folder-open-line"></i>
                                                            </button>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="ri-inbox-line" style="font-size: 80px; color: #ccc;"></i>
                                <p class="text-muted mt-3">No files or folders have been shared with you yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection