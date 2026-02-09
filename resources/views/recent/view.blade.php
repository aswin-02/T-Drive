@extends('layout')
@section('title', 'Recent Files')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="ri-history-line mr-2"></i>
                            Recent Files
                        </h4>
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                            <i class="ri-arrow-left-line mr-1"></i> Back to Dashboard
                        </a>
                    </div>
                    <div class="card-body">
                        @if($recentFiles->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>File Name</th>
                                            <th>Type</th>
                                            <th>Size</th>
                                            <th>Viewed On</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentFiles as $recent)
                                            @php
                                                $file = $recent->file;
                                                $extension = strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION));
                                                $iconMap = [
                                                    'pdf' => 'ri-file-pdf-fill text-danger',
                                                    'doc' => 'ri-file-word-2-fill text-primary',
                                                    'docx' => 'ri-file-word-2-fill text-primary',
                                                    'xls' => 'ri-file-excel-2-fill text-success',
                                                    'xlsx' => 'ri-file-excel-2-fill text-success',
                                                    'ppt' => 'ri-file-ppt-2-fill text-warning',
                                                    'pptx' => 'ri-file-ppt-2-fill text-warning',
                                                    'jpg' => 'ri-image-fill text-info',
                                                    'jpeg' => 'ri-image-fill text-info',
                                                    'png' => 'ri-image-fill text-info',
                                                    'zip' => 'ri-folder-zip-fill text-dark',
                                                ];
                                                $icon = $iconMap[$extension] ?? 'ri-file-fill text-secondary';
                                            @endphp
                                            <tr>
                                                <td>
                                                    <i class="{{ $icon }} mr-2" style="font-size: 1.2em;"></i>
                                                    {{ $file->original_name }}
                                                </td>
                                                <td>{{ strtoupper($extension) }}</td>
                                                <td>{{ $file->formatted_size }}</td>
                                                <td>
                                                    {{ $recent->viewed_at->format('M d, Y h:i A') }}
                                                    <small class="text-muted d-block">
                                                        {{ $recent->viewed_at->diffForHumans() }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <a href="{{ route('files.view', $file->id) }}" class="btn btn-sm btn-primary"
                                                        title="View File">
                                                        <i class="ri-eye-line"></i>
                                                    </a>
                                                    <a href="{{ route('files.download', $file->id) }}"
                                                        class="btn btn-sm btn-success ml-1" title="Download File">
                                                        <i class="ri-download-line"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="ri-history-line" style="font-size: 80px; color: #ccc;"></i>
                                <p class="text-muted mt-3">No recent files viewed yet.</p>
                                <a href="{{ route('dashboard') }}" class="btn btn-primary mt-2">
                                    Browse My Files
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection