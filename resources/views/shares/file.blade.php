<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $file->original_name }} · BlackBox</title>
    <meta name="description" content="Shared file: {{ $file->original_name }}">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Remix Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <!-- Bootstrap 4 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Handsontable CSS (XLS/XLSX) -->
    <link rel="stylesheet" href="{{ asset('vendor/doc-viewer/include/SheetJS/handsontable.full.min.css') }}">

    @php
        $extension = strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION));
        $fileUrl = url(\Illuminate\Support\Facades\Storage::url($file->path));

        $typeLabels = [
            'pdf' => 'PDF Document',
            'doc' => 'Word Document',
            'docx' => 'Word Document',
            'xls' => 'Excel Spreadsheet',
            'xlsx' => 'Excel Spreadsheet',
            'ppt' => 'PowerPoint',
            'pptx' => 'PowerPoint',
            'jpg' => 'JPEG Image',
            'jpeg' => 'JPEG Image',
            'png' => 'PNG Image',
            'gif' => 'GIF Image',
            'webp' => 'WebP Image',
            'zip' => 'ZIP Archive',
            'rar' => 'RAR Archive',
            'txt' => 'Text File',
        ];
        $typeLabel = $typeLabels[$extension] ?? strtoupper($extension) . ' File';

        // Icon + colour per type
        $typeConfig = [
            'pdf' => ['icon' => 'ri-file-pdf-2-line', 'color' => '#ef4444', 'bg' => '#fef2f2'],
            'doc' => ['icon' => 'ri-file-word-2-line', 'color' => '#2563eb', 'bg' => '#eff6ff'],
            'docx' => ['icon' => 'ri-file-word-2-line', 'color' => '#2563eb', 'bg' => '#eff6ff'],
            'xls' => ['icon' => 'ri-file-excel-2-line', 'color' => '#16a34a', 'bg' => '#f0fdf4'],
            'xlsx' => ['icon' => 'ri-file-excel-2-line', 'color' => '#16a34a', 'bg' => '#f0fdf4'],
            'ppt' => ['icon' => 'ri-file-ppt-2-line', 'color' => '#ea580c', 'bg' => '#fff7ed'],
            'pptx' => ['icon' => 'ri-file-ppt-2-line', 'color' => '#ea580c', 'bg' => '#fff7ed'],
            'jpg' => ['icon' => 'ri-image-2-line', 'color' => '#7c3aed', 'bg' => '#ede9fe'],
            'jpeg' => ['icon' => 'ri-image-2-line', 'color' => '#7c3aed', 'bg' => '#ede9fe'],
            'png' => ['icon' => 'ri-image-2-line', 'color' => '#7c3aed', 'bg' => '#ede9fe'],
            'gif' => ['icon' => 'ri-image-2-line', 'color' => '#7c3aed', 'bg' => '#ede9fe'],
            'webp' => ['icon' => 'ri-image-2-line', 'color' => '#7c3aed', 'bg' => '#ede9fe'],
            'zip' => ['icon' => 'ri-folder-zip-line', 'color' => '#d97706', 'bg' => '#fffbeb'],
            'rar' => ['icon' => 'ri-folder-zip-line', 'color' => '#d97706', 'bg' => '#fffbeb'],
            'txt' => ['icon' => 'ri-file-text-line', 'color' => '#475569', 'bg' => '#f8fafc'],
        ];
        $tc = $typeConfig[$extension] ?? ['icon' => 'ri-file-line', 'color' => '#6366f1', 'bg' => '#eef2ff'];

        $canPreview = in_array($extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'txt']);
        $canDownload = in_array($share->permission, ['download', 'edit']);
    @endphp

    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            color: #0f172a;
        }

        /* ── Top bar ── */
        .topbar {
            height: 56px;
            display: flex;
            align-items: center;
            padding: 0 28px;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 0 rgba(255, 255, 255, .06);
        }

        .topbar-logo {
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: -.3px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .topbar-logo i {
            color: #818cf8;
            font-size: 1.3rem;
        }

        .topbar-sep {
            flex: 1;
        }

        .topbar-badge {
            background: rgba(99, 102, 241, .18);
            color: #a5b4fc;
            font-size: .72rem;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 20px;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        /* ── Page layout ── */
        .page-wrap {
            display: flex;
            min-height: calc(100vh - 56px);
        }

        /* ── SIDEBAR ── */
        .sidebar {
            width: 300px;
            min-width: 300px;
            display: flex;
            flex-direction: column;
            padding: 32px 24px;
            gap: 0;
        }

        .file-icon-wrap {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            background:
                {{ $tc['bg'] }}
            ;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .25);
        }

        .file-icon-wrap i {
            font-size: 2.6rem;
            color:
                {{ $tc['color'] }}
            ;
        }

        .file-name {
            font-size: 1rem;
            font-weight: 600;
            color: #000;
            word-break: break-word;
            line-height: 1.5;
            margin-bottom: 24px;
        }

        .meta-list {
            list-style: none;
            margin-bottom: 28px;
        }

        .meta-list li {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 8px;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, .07);
            font-size: .82rem;
        }

        .meta-list li:last-child {
            border-bottom: none;
        }

        .meta-key {
            color: #3f3f3fff;
            font-weight: 500;
            white-space: nowrap;
        }

        .meta-val {
            color: #94a3b8;
            text-align: right;
            font-weight: 500;
        }

        .perm-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: .75rem;
            font-weight: 600;
        }

        .perm-view {
            background: rgba(148, 163, 184, .15);
            color: #94a3b8;
        }

        .perm-download {
            background: rgba(34, 197, 94, .15);
            color: #4ade80;
        }

        .perm-edit {
            background: rgba(251, 191, 36, .15);
            color: #fbbf24;
        }

        .expire-box {
            background: rgba(251, 191, 36, .1);
            border: 1px solid rgba(251, 191, 36, .25);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: .78rem;
            color: #fcd34d;
            margin-bottom: 20px;
            display: flex;
            gap: 8px;
            align-items: flex-start;
        }

        .expire-box i {
            font-size: 1rem;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .btn-download-main {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: #fff;
            font-size: .95rem;
            font-weight: 600;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all .2s ease;
            box-shadow: 0 4px 14px rgba(22, 163, 74, .4);
            margin-top: auto;
        }

        .btn-download-main:hover {
            background: linear-gradient(135deg, #16a34a, #15803d);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(22, 163, 74, .5);
            color: #fff;
            text-decoration: none;
        }

        .btn-download-main i {
            font-size: 1.1rem;
        }

        .sidebar-footer {
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, .07);
            font-size: .72rem;
            color: #475569;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ── MAIN CONTENT ── */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .viewer-toolbar {
            height: 48px;
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            padding: 0 24px;
            gap: 12px;
        }

        .viewer-toolbar .vt-label {
            font-size: .8rem;
            font-weight: 600;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .viewer-toolbar .vt-label i {
            font-size: 1rem;
        }

        .viewer-powered {
            margin-left: auto;
            font-size: .72rem;
            color: #94a3b8;
        }

        .viewer-area {
            flex: 1;
            background: #f8fafc;
            padding: 24px;
            overflow: auto;
        }

        /* Spinner */
        .viewer-spinner {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 400px;
            gap: 16px;
        }

        .viewer-spinner .spinner-ring {
            width: 52px;
            height: 52px;
            border: 4px solid #e2e8f0;
            border-top-color: #6366f1;
            border-radius: 50%;
            animation: spin .8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .viewer-spinner p {
            font-size: .9rem;
            color: #64748b;
            font-weight: 500;
        }

        .viewer-spinner small {
            font-size: .8rem;
            color: #94a3b8;
        }

        /* Error box */
        .viewer-error {
            max-width: 480px;
            margin: 60px auto;
            background: #fff;
            border-radius: 16px;
            padding: 32px;
            text-align: center;
            box-shadow: 0 4px 24px rgba(0, 0, 0, .06);
            border: 1px solid #fee2e2;
        }

        .viewer-error i {
            font-size: 2.5rem;
            color: #ef4444;
            margin-bottom: 12px;
            display: block;
        }

        .viewer-error h5 {
            font-size: 1rem;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .viewer-error p {
            font-size: .85rem;
            color: #64748b;
            margin-bottom: 20px;
        }

        /* PDF */
        .pdf-iframe,
        .ppt-iframe {
            width: 100%;
            height: calc(100vh - 56px - 48px - 48px);
            min-height: 500px;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, .08);
        }

        /* DOC */
        .docx-body {
            max-width: 820px;
            margin: 0 auto;
            background: #fff;
            border-radius: 16px;
            padding: 48px 64px;
            font-family: 'Segoe UI', Georgia, serif;
            font-size: 15px;
            line-height: 1.8;
            color: #1e293b;
            box-shadow: 0 4px 24px rgba(0, 0, 0, .07);
        }

        .docx-body table {
            border-collapse: collapse;
            width: 100%;
            margin: 12px 0;
        }

        .docx-body td,
        .docx-body th {
            border: 1px solid #e2e8f0;
            padding: 8px 12px;
        }

        /* Spreadsheet */
        .sheet-tabs {
            display: flex;
            gap: 4px;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }

        .sheet-tab-btn {
            padding: 6px 16px;
            border-radius: 8px 8px 0 0;
            border: 1px solid #e2e8f0;
            background: #fff;
            font-size: .8rem;
            font-weight: 600;
            color: #64748b;
            cursor: pointer;
            transition: all .15s;
        }

        .sheet-tab-btn.active {
            background: #6366f1;
            color: #fff;
            border-color: #6366f1;
        }

        #hot-container {
            border-radius: 0 12px 12px 12px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0, 0, 0, .07);
        }

        /* Image */
        .img-preview {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 400px;
        }

        .img-preview img {
            max-width: 100%;
            max-height: calc(100vh - 200px);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, .12);
        }

        /* No preview */
        .no-preview {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 400px;
            gap: 16px;
            color: #94a3b8;
            text-align: center;
        }

        .no-preview i {
            font-size: 4rem;
        }

        .no-preview h5 {
            font-size: 1rem;
            font-weight: 600;
            color: #475569;
            margin: 0;
        }

        .no-preview p {
            font-size: .85rem;
            margin: 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-wrap {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                min-width: unset;
                padding: 24px 20px;
            }

            .docx-body {
                padding: 24px 20px;
            }
        }

        /* Fade-in animation */
        .fade-in {
            animation: fadeIn .4s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }
    </style>
</head>

<body>
    <!-- ── Top Bar ── -->
    <div class="topbar">
        <div class="topbar-logo text-dark">
            <i class="ri-hard-drive-2-line"></i> Black-Box
        </div>
        <div class="topbar-sep"></div>
        <span class="topbar-badge">
            <i class="ri-share-line"></i> Shared File
        </span>
    </div>

    <div class="page-wrap">

        <!-- ══════════════════════════════════════
             SIDEBAR — File meta + Actions
        ══════════════════════════════════════ -->
        <aside class="sidebar">

            <!-- File icon -->
            <div class="file-icon-wrap">
                <i class="{{ $tc['icon'] }}"></i>
            </div>

            <!-- File name -->
            <div class="file-name">{{ $file->original_name }}</div>

            <!-- Expiry warning -->
            @if($share->expires_at)
                <div class="expire-box">
                    <i class="ri-alarm-warning-line"></i>
                    <div>
                        <strong>Expires</strong><br>
                        {{ $share->expires_at->format('d M Y, g:i A') }}
                        &nbsp;({{ $share->expires_at->diffForHumans() }})
                    </div>
                </div>
            @endif

            <!-- Meta list -->
            <ul class="meta-list">
                <li>
                    <span class="meta-key"><i class="ri-scales-line mr-1"></i>Size</span>
                    <span class="meta-val">{{ $file->formatted_size }}</span>
                </li>
                <li>
                    <span class="meta-key"><i class="ri-file-type-line mr-1"></i>Type</span>
                    <span class="meta-val">{{ $typeLabel }}</span>
                </li>
                <li>
                    <span class="meta-key"><i class="ri-user-line mr-1"></i>Shared by</span>
                    <span class="meta-val">{{ $share->owner->name }}</span>
                </li>
                <li>
                    <span class="meta-key"><i class="ri-calendar-line mr-1"></i>Date</span>
                    <span class="meta-val">{{ $share->created_at->format('d M Y') }}</span>
                </li>
                <li>
                    <span class="meta-key"><i class="ri-lock-line mr-1"></i>Access</span>
                    <span class="meta-val">
                        @if($share->permission === 'view')
                            <span class="perm-badge perm-view"><i class="ri-eye-line"></i> View Only</span>
                        @elseif($share->permission === 'download')
                            <span class="perm-badge perm-download"><i class="ri-download-line"></i> Download</span>
                        @elseif($share->permission === 'edit')
                            <span class="perm-badge perm-edit"><i class="ri-edit-line"></i> Full Access</span>
                        @endif
                    </span>
                </li>
            </ul>

            <!-- Download button (only if permission allows) -->
            @if($canDownload)
                <a href="{{ route('shares.download', $share->token) }}" class="btn-download-main">
                    <i class="ri-download-cloud-2-line"></i>
                    Download File
                </a>
            @endif

            <!-- Footer -->
            <div class="sidebar-footer">
                <i class="ri-shield-check-line"></i>
                Secure &amp; encrypted link
            </div>

        </aside>

        <!-- ══════════════════════════════════════
             MAIN — File viewer
        ══════════════════════════════════════ -->
        <main class="main-content">

            <!-- Viewer toolbar -->
            <div class="viewer-toolbar">
                <span class="vt-label">
                    <i class="{{ $tc['icon'] }}" style="color:{{ $tc['color'] }};"></i>
                    Preview
                </span>
                <span class="viewer-powered">
                    @if($extension === 'pdf') Native PDF viewer
                    @elseif(in_array($extension, ['doc', 'docx'])) Powered by Mammoth.js
                    @elseif(in_array($extension, ['xls', 'xlsx'])) Powered by SheetJS
                    @elseif(in_array($extension, ['ppt', 'pptx'])) Converted via LibreOffice
                    @elseif(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) Image viewer
                    @else No preview available
                    @endif
                </span>
            </div>

            <div class="viewer-area">

                {{-- ═══ PDF ═══ --}}
                @if($extension === 'pdf')
                    <iframe src="{{ $fileUrl }}" class="pdf-iframe" title="{{ $file->original_name }}">
                        Your browser does not support PDF preview.
                    </iframe>

                    {{-- ═══ DOC / DOCX → Mammoth.js ═══ --}}
                @elseif(in_array($extension, ['doc', 'docx']))
                    <div id="doc-loading" class="viewer-spinner fade-in">
                        <div class="spinner-ring"></div>
                        <p>Rendering document…</p>
                    </div>
                    <div id="doc-error" class="viewer-error" style="display:none;">
                        <i class="ri-error-warning-line"></i>
                        <h5>Could not render document</h5>
                        <p id="doc-error-msg">An error occurred while loading the file.</p>
                        @if($canDownload)
                            <a href="{{ route('shares.download', $share->token) }}" class="btn btn-sm btn-primary px-4">Download
                                instead</a>
                        @endif
                    </div>
                    <div id="docx-output" class="docx-body fade-in" style="display:none;"></div>

                    {{-- ═══ XLS / XLSX → SheetJS + Handsontable ═══ --}}
                @elseif(in_array($extension, ['xls', 'xlsx']))
                    <div id="sheet-loading" class="viewer-spinner fade-in">
                        <div class="spinner-ring" style="border-top-color:#16a34a;"></div>
                        <p>Loading spreadsheet…</p>
                    </div>
                    <div id="sheet-error" class="viewer-error" style="display:none;">
                        <i class="ri-error-warning-line"></i>
                        <h5>Could not load spreadsheet</h5>
                        <p id="sheet-error-msg">An error occurred while reading the file.</p>
                        @if($canDownload)
                            <a href="{{ route('shares.download', $share->token) }}" class="btn btn-sm btn-success px-4">Download
                                instead</a>
                        @endif
                    </div>
                    <div id="sheet-wrap" class="fade-in" style="display:none;">
                        <div class="sheet-tabs" id="sheet-tabs"></div>
                        <div id="hot-container" style="height:560px;"></div>
                    </div>

                    {{-- ═══ PPT / PPTX → LibreOffice PDF ═══ --}}
                @elseif(in_array($extension, ['ppt', 'pptx']))
                    <div id="ppt-loading" class="viewer-spinner fade-in">
                        <div class="spinner-ring" style="border-top-color:#ea580c;"></div>
                        <p>Converting presentation to PDF…</p>
                        <small>First load may take a few seconds</small>
                    </div>
                    <div id="ppt-error" class="viewer-error" style="display:none;">
                        <i class="ri-error-warning-line"></i>
                        <h5>Could not convert presentation</h5>
                        <p id="ppt-error-msg">LibreOffice conversion failed.</p>
                        @if($canDownload)
                            <a href="{{ route('shares.download', $share->token) }}" class="btn btn-sm btn-warning px-4">Download
                                instead</a>
                        @endif
                    </div>
                    <div id="ppt-wrap" class="fade-in" style="display:none; height:calc(100vh - 160px); min-height:500px;">
                        <iframe id="ppt-frame" class="ppt-iframe" title="{{ $file->original_name }}"></iframe>
                    </div>

                    {{-- ═══ Image ═══ --}}
                @elseif(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                    <div class="img-preview">
                        <img src="{{ $fileUrl }}" alt="{{ $file->original_name }}"
                            onerror="this.parentElement.innerHTML='<div class=\'no-preview\'><i class=\'ri-image-off-line\'></i><h5>Image failed to load</h5></div>'">
                    </div>

                    {{-- ═══ Text ═══ --}}
                @elseif($extension === 'txt')
                    <div id="txt-loading" class="viewer-spinner fade-in">
                        <div class="spinner-ring" style="border-top-color:#475569;"></div>
                        <p>Loading text…</p>
                    </div>
                    <div id="txt-wrap" class="fade-in" style="display:none;">
                        <pre id="txt-content"
                            style="background:#fff;padding:28px;border-radius:16px;font-size:.875rem;line-height:1.75;color:#1e293b;box-shadow:0 4px 24px rgba(0,0,0,.07);white-space:pre-wrap;word-break:break-word;"></pre>
                    </div>

                    {{-- ═══ No preview ═══ --}}
                @else
                    <div class="no-preview">
                        <i class="ri-file-unknow-line" style="color:{{ $tc['color'] }};"></i>
                        <h5>Preview not available</h5>
                        <p>{{ $typeLabel }} files cannot be previewed in the browser.</p>
                        @if($canDownload)
                            <a href="{{ route('shares.download', $share->token) }}" class="btn btn-primary mt-2 px-5">
                                <i class="ri-download-line mr-2"></i>Download to View
                            </a>
                        @endif
                    </div>
                @endif

            </div>{{-- /viewer-area --}}
        </main>
    </div>{{-- /page-wrap --}}

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    @php $ext2 = $extension; @endphp

    {{-- DOC / DOCX --}}
    @if(in_array($ext2, ['doc', 'docx']))
        <script src="{{ asset('vendor/doc-viewer/include/docx/mammoth.browser.min.js') }}"></script>
        <script>
            fetch("{{ $fileUrl }}")
                .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.arrayBuffer(); })
                .then(buf => mammoth.convertToHtml({ arrayBuffer: buf }))
                .then(res => {
                    document.getElementById('doc-loading').style.display = 'none';
                    const out = document.getElementById('docx-output');
                    out.innerHTML = res.value || '<p style="color:#94a3b8;text-align:center;padding:40px 0;">Empty document.</p>';
                    out.style.display = 'block';
                })
                .catch(err => {
                    document.getElementById('doc-loading').style.display = 'none';
                    document.getElementById('doc-error-msg').textContent = err.message;
                    document.getElementById('doc-error').style.display = 'block';
                });
        </script>
    @endif

    {{-- XLS / XLSX --}}
    @if(in_array($ext2, ['xls', 'xlsx']))
        <script src="{{ asset('vendor/doc-viewer/include/SheetJS/xlsx.full.min.js') }}"></script>
        <script src="{{ asset('vendor/doc-viewer/include/SheetJS/handsontable.full.min.js') }}"></script>
        <script>
            (function () {
                let hot = null, wb = null;
                fetch("{{ $fileUrl }}")
                    .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.arrayBuffer(); })
                    .then(buf => {
                        wb = XLSX.read(new Uint8Array(buf), { type: 'array' });
                        document.getElementById('sheet-loading').style.display = 'none';
                        document.getElementById('sheet-wrap').style.display = 'block';
                        const tabs = document.getElementById('sheet-tabs');
                        wb.SheetNames.forEach((name, i) => {
                            const btn = document.createElement('button');
                            btn.className = 'sheet-tab-btn' + (i === 0 ? ' active' : '');
                            btn.textContent = name;
                            btn.onclick = () => {
                                tabs.querySelectorAll('.sheet-tab-btn').forEach(b => b.classList.remove('active'));
                                btn.classList.add('active');
                                render(name);
                            };
                            tabs.appendChild(btn);
                        });
                        render(wb.SheetNames[0]);
                    })
                    .catch(err => {
                        document.getElementById('sheet-loading').style.display = 'none';
                        document.getElementById('sheet-error-msg').textContent = err.message;
                        document.getElementById('sheet-error').style.display = 'block';
                    });
                function render(name) {
                    const data = XLSX.utils.sheet_to_json(wb.Sheets[name], { header: 1, defval: '' });
                    if (hot) { hot.destroy(); hot = null; }
                    document.getElementById('hot-container').innerHTML = '';
                    hot = new Handsontable(document.getElementById('hot-container'), {
                        data, readOnly: true, rowHeaders: true, colHeaders: true,
                        stretchH: 'all', height: 560,
                        licenseKey: 'non-commercial-and-evaluation',
                        contextMenu: false, manualColumnResize: true,
                    });
                }
            })();
        </script>
    @endif

    {{-- PPT / PPTX --}}
    @if(in_array($ext2, ['ppt', 'pptx']))
        <script>
            (function () {
                const url = "{{ route('shares.preview-pdf', $share->token) }}";
                fetch(url, { method: 'HEAD' })
                    .then(r => {
                        if (!r.ok) throw new Error('Conversion failed (HTTP ' + r.status + ')');
                        const fr = document.getElementById('ppt-frame');
                        fr.src = url;
                        fr.onload = () => {
                            document.getElementById('ppt-loading').style.display = 'none';
                            document.getElementById('ppt-wrap').style.display = 'block';
                        };
                    })
                    .catch(err => {
                        document.getElementById('ppt-loading').style.display = 'none';
                        document.getElementById('ppt-error-msg').textContent = err.message;
                        document.getElementById('ppt-error').style.display = 'block';
                    });
            })();
        </script>
    @endif

    {{-- TXT --}}
    @if($ext2 === 'txt')
        <script>
            fetch("{{ $fileUrl }}")
                .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.text(); })
                .then(t => {
                    document.getElementById('txt-loading').style.display = 'none';
                    document.getElementById('txt-content').textContent = t;
                    document.getElementById('txt-wrap').style.display = 'block';
                })
                .catch(err => {
                    document.getElementById('txt-loading').innerHTML =
                        '<i class="ri-error-warning-line" style="font-size:2rem;color:#ef4444;"></i><p>' + err.message + '</p>';
                });
        </script>
    @endif

</body>

</html>