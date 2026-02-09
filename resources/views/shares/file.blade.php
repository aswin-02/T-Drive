<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $file->original_name }} - Shared File</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Remix Icon -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .share-container {
            max-width: 1100px;
            margin: 0 auto;
        }
        .share-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .share-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .file-icon {
            font-size: 80px;
            margin-bottom: 15px;
        }
        .share-body {
            padding: 40px;
        }
        .file-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .btn-download {
            padding: 15px 40px;
            font-size: 18px;
            border-radius: 50px;
        }
        .permission-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        .badge-view {
            background: #e3f2fd;
            color: #1976d2;
        }
        .badge-download {
            background: #e8f5e9;
            color: #388e3c;
        }
        .badge-edit {
            background: #fff3e0;
            color: #f57c00;
        }
        .expire-warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .file-preview {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .preview-iframe {
            width: 100%;
            height: 600px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
        }
        .preview-image {
            max-width: 100%;
            max-height: 600px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .localhost-notice {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="share-container">
        <div class="share-card">
            <!-- Header -->
            <div class="share-header">
                <div class="file-icon">
                    @php
                        $extension = strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION));
                        $iconMap = [
                            'pdf' => 'ri-file-pdf-line',
                            'doc' => 'ri-file-word-2-line',
                            'docx' => 'ri-file-word-2-line',
                            'xls' => 'ri-file-excel-2-line',
                            'xlsx' => 'ri-file-excel-2-line',
                            'ppt' => 'ri-file-ppt-2-line',
                            'pptx' => 'ri-file-ppt-2-line',
                            'jpg' => 'ri-image-line',
                            'jpeg' => 'ri-image-line',
                            'png' => 'ri-image-line',
                            'gif' => 'ri-image-line',
                            'zip' => 'ri-folder-zip-line',
                            'rar' => 'ri-folder-zip-line',
                            'txt' => 'ri-file-text-line',
                        ];
                        $icon = $iconMap[$extension] ?? 'ri-file-line';
                    @endphp
                    <i class="{{ $icon }}"></i>
                </div>
                <h2 class="mb-2">{{ $file->original_name }}</h2>
                <p class="mb-0">Shared with you</p>
            </div>

            <!-- Body -->
            <div class="share-body">
                <!-- Expiration Warning -->
                @if($share->expires_at)
                    <div class="expire-warning">
                        <i class="ri-time-line mr-2"></i>
                        <strong>Expires:</strong> {{ $share->expires_at->format('F d, Y \a\t g:i A') }}
                        ({{ $share->expires_at->diffForHumans() }})
                    </div>
                @endif

                <!-- File Preview Section -->
                @php
                    $previewableExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'gif', 'webp'];
                    $canPreview = in_array($extension, $previewableExtensions);
                    $fileUrl = url(\Illuminate\Support\Facades\Storage::url($file->path));
                    $isLocalhost = request()->getHost() === 'localhost' || str_starts_with(request()->getHost(), '127.0.0.1');
                @endphp

                @if($canPreview)
                    <div class="file-preview">
                        <h5 class="mb-3">
                            <i class="ri-eye-line mr-2"></i>
                            File Preview
                        </h5>

                        @if($isLocalhost && in_array($extension, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']))
                            <div class="localhost-notice mb-3">
                                <i class="ri-information-line mr-2"></i>
                                <strong>Local Development:</strong> Document preview requires a public URL. This will work when deployed to production.
                            </div>
                        @endif

                        @if($extension === 'pdf')
                            {{-- PDF Preview - Browser Native --}}
                            <iframe src="{{ $fileUrl }}" class="preview-iframe">
                                Your browser does not support PDF preview.
                                <a href="{{ route('shares.download', $share->token) }}">Download the file</a>
                            </iframe>

                        @elseif(in_array($extension, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']))
                            {{-- Office Documents - Google Docs Viewer --}}
                            <iframe src="https://docs.google.com/viewer?url={{ urlencode($fileUrl) }}&embedded=true" 
                                    class="preview-iframe"
                                    onload="console.log('Google Docs Viewer loaded');"
                                    onerror="console.error('Google Docs Viewer failed');">
                                Unable to display preview.
                                <a href="{{ route('shares.download', $share->token) }}">Download the file</a>
                            </iframe>
                            <div class="text-center mt-2">
                                <small class="text-muted">
                                    <i class="ri-information-line mr-1"></i>
                                    Preview powered by Google Docs Viewer
                                </small>
                            </div>

                        @elseif(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                            {{-- Image Preview --}}
                            <div class="text-center">
                                <img src="{{ $fileUrl }}" 
                                     alt="{{ $file->original_name }}" 
                                     class="preview-image"
                                     onerror="this.style.display='none'; document.getElementById('image-error').style.display='block';">
                                <div id="image-error" style="display: none;" class="alert alert-danger mt-3">
                                    <i class="ri-image-line mr-2"></i>
                                    Failed to load image. 
                                    <a href="{{ route('shares.download', $share->token) }}">Download to view</a>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- File Information -->
                <div class="file-info">
                    <div class="info-row">
                        <span><strong>File Name:</strong></span>
                        <span>{{ $file->original_name }}</span>
                    </div>
                    <div class="info-row">
                        <span><strong>File Size:</strong></span>
                        <span>{{ $file->formatted_size }}</span>
                    </div>
                    <div class="info-row">
                        <span><strong>File Type:</strong></span>
                        <span>{{ strtoupper($extension) }}</span>
                    </div>
                    <div class="info-row">
                        <span><strong>Shared By:</strong></span>
                        <span>{{ $share->owner->name }}</span>
                    </div>
                    <div class="info-row">
                        <span><strong>Permission:</strong></span>
                        <span>
                            @if($share->permission === 'view')
                                <span class="permission-badge badge-view">View Only</span>
                            @elseif($share->permission === 'download')
                                <span class="permission-badge badge-download">View & Download</span>
                            @elseif($share->permission === 'edit')
                                <span class="permission-badge badge-edit">Full Access</span>
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="text-center">
                    @if(in_array($share->permission, ['download', 'edit']))
                        <a href="{{ route('shares.download', $share->token) }}" class="btn btn-primary btn-download">
                            <i class="ri-download-cloud-line mr-2"></i>
                            Download File
                        </a>
                    @else
                        <div class="alert alert-info">
                            <i class="ri-information-line mr-2"></i>
                            You have view-only access to this file. Download is not permitted.
                        </div>
                    @endif
                </div>

                <!-- Shared on -->
                <div class="text-center mt-4 text-muted">
                    <small>
                        <i class="ri-calendar-line mr-1"></i>
                        Shared on {{ $share->created_at->format('F d, Y') }}
                    </small>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-4">
            <p class="text-white">
                <i class="ri-shield-check-line mr-2"></i>
                This is a secure share link. Keep it private.
            </p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>