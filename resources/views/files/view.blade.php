@extends('layout')
@section('title', 'View File - ' . $file->original_name)
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0">
                                <i class="ri-file-text-line mr-2"></i>{{ $file->original_name }}
                            </h4>
                            <p class="text-muted small mb-0 mt-1">
                                Size: {{ $file->formatted_size }} | Type: {{ strtoupper($extension) }} | 
                                Uploaded: {{ $file->created_at->format('M d, Y') }}
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('files.download', $file->id) }}" class="btn btn-primary">
                                <i class="ri-download-line mr-1"></i> Download
                            </a>
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                <i class="ri-arrow-left-line mr-1"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="file-viewer-container" style="min-height: 600px;">
                            @if($viewerType === 'pdf')
                                {{-- PDF Viewer - Using Browser Native --}}
                                <div class="embed-responsive" style="height: 700px;">
                                    <iframe src="{{ url($fileUrl) }}" 
                                            class="embed-responsive-item border" 
                                            style="width: 100%; height: 100%;"
                                            frameborder="0">
                                        <p>Your browser does not support iframes. 
                                            <a href="{{ route('files.download', $file->id) }}">Download the PDF</a>
                                        </p>
                                    </iframe>
                                </div>

                            @elseif($viewerType === 'doc' || $viewerType === 'spreadsheet' || $viewerType === 'presentation')
                                {{-- Office Document Viewer - Using Google Docs Viewer --}}
                                @if(request()->getHost() === 'localhost' || str_starts_with(request()->getHost(), '127.0.0.1'))
                                    {{-- Local Development Notice --}}
                                    <div class="alert alert-warning mb-3">
                                        <i class="ri-alert-line mr-2"></i>
                                        <strong>Local Development Mode:</strong> 
                                        Document preview is not available on localhost. This will work in production when deployed to a public domain.
                                    </div>
                                @endif
                                
                                <div class="alert alert-info mb-3">
                                    <i class="ri-information-line mr-2"></i>
                                    @if($viewerType === 'doc')
                                        Word document preview powered by Google Docs Viewer.
                                    @elseif($viewerType === 'spreadsheet')
                                        Excel spreadsheet preview powered by Google Docs Viewer.
                                    @elseif($viewerType === 'presentation')
                                        PowerPoint presentation preview powered by Google Docs Viewer.
                                    @endif
                                    <a href="{{ route('files.download', $file->id) }}" class="alert-link">Download</a> for full features.
                                </div>
                                
                                <div class="embed-responsive" style="height: 700px;">
                                    <iframe src="https://docs.google.com/viewer?url={{ urlencode(url($fileUrl)) }}&embedded=true" 
                                            class="embed-responsive-item border" 
                                            style="width: 100%; height: 100%;"
                                            frameborder="0"
                                            onload="console.log('Google Docs Viewer loaded');"
                                            onerror="console.error('Google Docs Viewer failed to load');">
                                        <p>Unable to display document preview. 
                                            <a href="{{ route('files.download', $file->id) }}">Download the file</a>
                                        </p>
                                    </iframe>
                                </div>
                                
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="ri-information-line mr-1"></i>
                                        Preview powered by Google Docs Viewer. 
                                        If preview doesn't load, try <a href="{{ route('files.download', $file->id) }}">downloading the file</a>.
                                        <br>
                                        <strong>Note:</strong> This feature requires the application to be deployed on a public domain (not localhost).
                                    </small>
                                </div>

                            @elseif($viewerType === 'image')
                                {{-- Image Viewer --}}
                                <div class="text-center p-4">
                                    <img src="{{ url($fileUrl) }}" 
                                         alt="{{ $file->original_name }}" 
                                         class="img-fluid rounded shadow-sm" 
                                         style="max-height: 700px; max-width: 100%;"
                                         onerror="this.style.display='none'; document.getElementById('image-error').style.display='block';">
                                    <div id="image-error" style="display: none;" class="alert alert-danger mt-3 mx-auto" style="max-width: 600px;">
                                        <i class="ri-image-line mr-2"></i>Failed to load image. 
                                        <a href="{{ route('files.download', $file->id) }}" class="alert-link">Download</a> to view.
                                    </div>
                                </div>

                            @elseif($viewerType === 'text')
                                {{-- Text File Viewer --}}
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <i class="ri-file-text-line mr-2"></i>Text Content
                                    </div>
                                    <div class="card-body">
                                        <pre id="text-content" class="mb-0" style="white-space: pre-wrap; word-wrap: break-word; max-height: 600px; overflow-y: auto;">Loading content...</pre>
                                    </div>
                                </div>

                            @elseif($viewerType === 'archive')
                                {{-- Archive Files (ZIP, RAR, etc.) --}}
                                <div class="text-center mt-5 mb-5">
                                    <div class="mb-4">
                                        <i class="ri-folder-zip-line" style="font-size: 120px; color: #6c757d;"></i>
                                    </div>
                                    <h4 class="mb-3">Archive File</h4>
                                    <p class="text-muted mb-2">{{ $file->original_name }}</p>
                                    <p class="text-muted small mb-4">
                                        {{ $file->formatted_size }} • {{ strtoupper($extension) }} • 
                                        Uploaded {{ $file->created_at->diffForHumans() }}
                                    </p>
                                    <div class="alert alert-info mx-auto" style="max-width: 600px;">
                                        <i class="ri-information-line mr-2"></i>
                                        Archive files cannot be previewed online. Download to extract and view contents.
                                    </div>
                                    <a href="{{ route('files.download', $file->id) }}" class="btn btn-primary btn-lg">
                                        <i class="ri-download-line mr-2"></i>Download Archive
                                    </a>
                                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg ml-2">
                                        <i class="ri-arrow-left-line mr-2"></i>Back to Files
                                    </a>
                                </div>

                            @else
                                {{-- Unknown File Type --}}
                                <div class="text-center mt-5 mb-5">
                                    <div class="mb-4">
                                        <i class="ri-file-unknow-line" style="font-size: 120px; color: #6c757d;"></i>
                                    </div>
                                    <h4 class="mb-3">Preview Not Available</h4>
                                    <p class="text-muted mb-2">{{ $file->original_name }}</p>
                                    <p class="text-muted small mb-4">
                                        {{ $file->formatted_size }} • {{ strtoupper($extension) }} • 
                                        Uploaded {{ $file->created_at->diffForHumans() }}
                                    </p>
                                    <div class="alert alert-warning mx-auto" style="max-width: 600px;">
                                        <i class="ri-alert-line mr-2"></i>
                                        This file type ({{ strtoupper($extension) }}) cannot be previewed in the browser.
                                    </div>
                                    <a href="{{ route('files.download', $file->id) }}" class="btn btn-primary btn-lg">
                                        <i class="ri-download-line mr-2"></i>Download File
                                    </a>
                                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg ml-2">
                                        <i class="ri-arrow-left-line mr-2"></i>Back to Files
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@if($viewerType === 'text')
@push('scripts')
<script>
    // Simple text file viewer
    fetch("{{ url($fileUrl) }}")
        .then(response => {
            if (!response.ok) throw new Error('Failed to load file');
            return response.text();
        })
        .then(text => {
            document.getElementById('text-content').textContent = text;
        })
        .catch(error => {
            document.getElementById('text-content').innerHTML = 
                '<span class="text-danger"><i class="ri-error-warning-line mr-2"></i>Error loading file: ' + 
                error.message + 
                '. <a href="{{ route("files.download", $file->id) }}">Download instead</a></span>';
        });
</script>
@endpush
@endif
