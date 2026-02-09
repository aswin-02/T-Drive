<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $folder->name }} - Shared Folder</title>

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
            max-width: 1000px;
            margin: 0 auto;
        }

        .share-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .share-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .folder-icon {
            font-size: 80px;
            margin-bottom: 15px;
        }

        .share-body {
            padding: 40px;
        }

        .file-list {
            margin-top: 20px;
        }

        .file-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: all 0.3s;
        }

        .file-item:hover {
            background: #f8f9fa;
            transform: translateX(5px);
        }

        .file-item-icon {
            font-size: 30px;
            margin-right: 15px;
            color: #667eea;
        }

        .file-item-info {
            flex: 1;
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

        .empty-folder {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <div class="share-container">
        <div class="share-card">
            <!-- Header -->
            <div class="share-header">
                <div class="folder-icon">
                    <i class="ri-folder-open-line"></i>
                </div>
                <h2 class="mb-2">{{ $folder->name }}</h2>
                <p class="mb-0">Shared folder with you</p>
                @if($share->permission)
                    <div class="mt-3">
                        @if($share->permission === 'view')
                            <span class="permission-badge badge-view">View Only</span>
                        @elseif($share->permission === 'download')
                            <span class="permission-badge badge-download">View & Download</span>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Body -->
            <div class="share-body">
                <h5 class="mb-3">
                    <i class="ri-folder-line mr-2"></i>
                    Folder Contents
                </h5>

                @if($files->count() > 0 || $subfolders->count() > 0)
                    <div class="file-list">
                        <!-- Subfolders -->
                        @foreach($subfolders as $subfolder)
                            <div class="file-item">
                                <div class="file-item-icon">
                                    <i class="ri-folder-fill"></i>
                                </div>
                                <div class="file-item-info">
                                    <strong>{{ $subfolder->name }}</strong>
                                    <br>
                                    <small class="text-muted">Folder</small>
                                </div>
                            </div>
                        @endforeach

                        <!-- Files -->
                        @foreach($files as $file)
                            <div class="file-item">
                                <div class="file-item-icon">
                                    @php
                                        $extension = strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION));
                                        $iconMap = [
                                            'pdf' => 'ri-file-pdf-fill',
                                            'doc' => 'ri-file-word-2-fill',
                                            'docx' => 'ri-file-word-2-fill',
                                            'xls' => 'ri-file-excel-2-fill',
                                            'xlsx' => 'ri-file-excel-2-fill',
                                            'jpg' => 'ri-image-fill',
                                            'jpeg' => 'ri-image-fill',
                                            'png' => 'ri-image-fill',
                                            'zip' => 'ri-folder-zip-fill',
                                        ];
                                        $icon = $iconMap[$extension] ?? 'ri-file-fill';
                                    @endphp
                                    <i class="{{ $icon }}"></i>
                                </div>
                                <div class="file-item-info">
                                    <strong>{{ $file->original_name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $file->formatted_size }} • {{ strtoupper($extension) }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-folder">
                        <i class="ri-inbox-line" style="font-size: 60px;"></i>
                        <p class="mt-3">This folder is empty</p>
                    </div>
                @endif

                <!-- Shared Info -->
                <div class="text-center mt-4 text-muted">
                    <small>
                        <i class="ri-user-line mr-1"></i>
                        Shared by {{ $share->owner->name }}
                        •
                        <i class="ri-calendar-line mr-1"></i>
                        {{ $share->created_at->format('F d, Y') }}
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