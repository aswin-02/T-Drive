@extends('layout')
@section('title', 'View File - ' . $file->original_name)

@push('styles')
    <style>
        /* ── Office viewer container ── */
        #office-viewer-wrap {
            width: 100%;
            min-height: 650px;
            background: #fff;
            border-radius: 8px;
            overflow: auto;
        }

        /* officeToHtml inner fixes */
        #office-viewer-wrap .main_officejs_container {
            min-height: 600px;
        }

        /* PPTX slide centering */
        #office-viewer-wrap .slide {
            margin: 0 auto 20px !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .12);
            border-radius: 6px;
            overflow: hidden;
        }

        /* Spreadsheet tab strip */
        #office-viewer-wrap .sheet-names-div {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 6px 10px;
        }

        /* Spinner overlay */
        #viewer-loading {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 400px;
            gap: 16px;
        }

        /* DOC render area */
        .docx-rendered-content {
            max-width: 860px;
            margin: 0 auto;
            padding: 40px 56px;
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 15px;
            line-height: 1.75;
            color: #222;
        }

        .docx-rendered-content table {
            border-collapse: collapse;
            width: 100%;
            margin: 10px 0;
        }

        .docx-rendered-content td,
        .docx-rendered-content th {
            border: 1px solid #ccc;
            padding: 6px 10px;
        }

        /* Spreadsheet sheet tabs */
        .sheet-tab-strip {
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
            padding: 10px 12px;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        #hot-container {
            border-radius: 0 0 8px 8px;
            overflow: hidden;
        }
    </style>
    @if($viewerType === 'spreadsheet')
        <link rel="stylesheet" href="{{ asset('vendor/doc-viewer/include/SheetJS/handsontable.full.min.css') }}">
    @endif
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">

                    {{-- ── Card Header ── --}}
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0">
                                <i class="ri-file-text-line mr-2"></i>{{ $file->original_name }}
                            </h4>
                            <p class="text-muted small mb-0 mt-1">
                                Size: {{ $file->formatted_size }} &nbsp;|&nbsp;
                                Type: {{ strtoupper($extension) }} &nbsp;|&nbsp;
                                Uploaded: {{ $file->created_at->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('files.download', $file->id) }}" class="btn btn-primary">
                                <i class="ri-download-line mr-1"></i> Download
                            </a>
                            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}"
                                class="btn btn-secondary ml-2">
                                <i class="ri-arrow-left-line mr-1"></i> Back
                            </a>
                        </div>
                    </div>

                    {{-- ── Card Body ── --}}
                    <div class="card-body p-0 p-md-3">

                        {{-- ════════════════════════════════════
                        PDF → Native browser iframe
                        ════════════════════════════════════ --}}
                        @if($viewerType === 'pdf')
                            <div style="height: 750px;">
                                <iframe src="{{ url($fileUrl) }}"
                                    style="width:100%; height:100%; border:none; border-radius:6px;"
                                    title="{{ $file->original_name }}">
                                    <p>Your browser does not support PDF preview.
                                        <a href="{{ route('files.download', $file->id) }}">Download the PDF</a>
                                    </p>
                                </iframe>
                            </div>

                            {{-- ════════════════════════════════════
                            DOC / DOCX → Mammoth.js (already loaded globally)
                            ════════════════════════════════════ --}}
                        @elseif($viewerType === 'doc')
                            <div class="alert alert-info d-flex align-items-center mb-3 mx-3 mt-3" role="alert">
                                <i class="ri-file-word-line mr-2" style="font-size:1.2rem;"></i>
                                <span>Word document rendered locally — no internet required.</span>
                            </div>

                            <div id="viewer-loading">
                                <div class="spinner-border text-primary" role="status"></div>
                                <p class="text-muted mb-0">Rendering document…</p>
                            </div>

                            <div id="doc-error" class="alert alert-danger mx-3" style="display:none;">
                                <i class="ri-error-warning-line mr-2"></i>
                                <span id="doc-error-msg">Failed to render document.</span>
                                <a href="{{ route('files.download', $file->id) }}" class="alert-link ml-2">Download instead</a>
                            </div>

                            <div id="docx-output" class="docx-rendered-content" style="display:none;"></div>

                            {{-- ════════════════════════════════════
                            XLS / XLSX → officeToHtml unified plugin
                            ════════════════════════════════════ --}}
                        @elseif($viewerType === 'spreadsheet')
                            <div class="alert alert-info d-flex align-items-center mb-0 mx-3 mt-3" role="alert">
                                <i class="ri-file-excel-line mr-2" style="font-size:1.2rem;"></i>
                                <span>Spreadsheet rendered locally — no internet required.</span>
                            </div>

                            <div id="viewer-loading" class="mt-3">
                                <div class="spinner-border text-success" role="status"></div>
                                <p class="text-muted mb-0">Loading spreadsheet…</p>
                            </div>

                            <div id="office-viewer-wrap" style="display:none; min-height:620px;">
                                <div id="office-content"></div>
                            </div>

                            <div id="viewer-error" class="alert alert-danger mx-3 mt-3" style="display:none;">
                                <i class="ri-error-warning-line mr-2"></i>
                                <span id="viewer-error-msg">Failed to render file.</span>
                                <a href="{{ route('files.download', $file->id) }}" class="alert-link ml-2">Download instead</a>
                            </div>

                            {{-- ════════════════════════════════════
                            PPT / PPTX → officeToHtml unified plugin
                            ════════════════════════════════════ --}}
                        @elseif($viewerType === 'presentation')
                            <div class="alert alert-info d-flex align-items-center mb-3 mx-3 mt-3" role="alert">
                                <i class="ri-file-ppt-2-line mr-2" style="font-size:1.2rem;"></i>
                                <span>Presentation converted to PDF via <strong>LibreOffice</strong> — no internet required.
                                    First load may take a few seconds.</span>
                            </div>

                            {{-- Loading state shown while LibreOffice converts --}}
                            <div id="viewer-loading" class="text-center py-5">
                                <div class="spinner-border text-warning" style="width:3rem;height:3rem;" role="status"></div>
                                <p class="text-muted mt-3 mb-0">Converting presentation to PDF…</p>
                                <small class="text-muted">This may take a few seconds on first view.</small>
                            </div>

                            {{-- PDF iframe shown after conversion --}}
                            <div id="ppt-pdf-wrap" style="display:none; height:750px;">
                                <iframe id="ppt-pdf-frame" style="width:100%; height:100%; border:none; border-radius:6px;"
                                    title="{{ $file->original_name }}">
                                </iframe>
                            </div>

                            <div id="viewer-error" class="alert alert-danger mx-3" style="display:none;">
                                <i class="ri-error-warning-line mr-2"></i>
                                <span id="viewer-error-msg">Failed to convert presentation.</span>
                                <a href="{{ route('files.download', $file->id) }}" class="alert-link ml-2">Download instead</a>
                            </div>

                            {{-- ════════════════════════════════════
                            IMAGE → Native <img>
                            ════════════════════════════════════ --}}
                        @elseif($viewerType === 'image')
                            <div class="text-center p-4">
                                <img src="{{ url($fileUrl) }}" alt="{{ $file->original_name }}"
                                    class="img-fluid rounded shadow-sm" style="max-height:720px; max-width:100%;"
                                    onerror="this.style.display='none'; document.getElementById('image-error').style.display='block';">
                                <div id="image-error" style="display:none;" class="alert alert-danger mt-3 mx-auto"
                                    style="max-width:600px;">
                                    <i class="ri-image-line mr-2"></i>Failed to load image.
                                    <a href="{{ route('files.download', $file->id) }}" class="alert-link">Download</a> to view.
                                </div>
                            </div>

                            {{-- ════════════════════════════════════
                            VIDEO → Native HTML5 <video> player
                                ════════════════════════════════════ --}}
                        @elseif($viewerType === 'video')
                                <div class="text-center p-4">
                                    <video controls
                                        style="max-width:100%; max-height:720px; border-radius:8px; box-shadow:0 4px 20px rgba(0,0,0,.15);"
                                        preload="metadata"
                                        onerror="this.style.display='none'; document.getElementById('video-error').style.display='block';">
                                        <source src="{{ url($fileUrl) }}" type="{{ $file->mime_type }}">
                                        Your browser does not support the video tag.
                                        <a href="{{ route('files.download', $file->id) }}">Download the video</a>.
                                    </video>
                                    <div id="video-error" style="display:none;" class="alert alert-danger mt-3 mx-auto"
                                        style="max-width:600px;">
                                        <i class="ri-video-line mr-2"></i>Failed to load video.
                                        <a href="{{ route('files.download', $file->id) }}" class="alert-link">Download</a> to
                                        watch.
                                    </div>
                                </div>

                                {{-- ════════════════════════════════════
                                AUDIO → Native HTML5 <audio> player
                                    ════════════════════════════════════ --}}
                        @elseif($viewerType === 'audio')
                                <div class="d-flex justify-content-center align-items-center p-5">
                                    <div class="card shadow-sm" style="max-width:600px; width:100%; border-radius:16px; overflow:hidden;">
                                        <div class="card-body text-center p-4" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);">
                                            <div class="mb-4">
                                                <div class="audio-icon-wrap d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                                                     style="width:90px; height:90px; background:rgba(255,255,255,0.1); animation: audioPulse 2s ease-in-out infinite;">
                                                    <i class="ri-music-2-fill" style="font-size:44px; color:#7c83fd;"></i>
                                                </div>
                                                <h5 class="text-white mb-1 text-truncate" title="{{ $file->original_name }}">
                                                    {{ pathinfo($file->original_name, PATHINFO_FILENAME) }}
                                                </h5>
                                                <small class="text-muted" style="color: rgba(255,255,255,0.5) !important;">
                                                    {{ strtoupper($extension) }} &nbsp;·&nbsp; {{ $file->formatted_size }}
                                                </small>
                                            </div>
                                            <audio controls id="audioPlayer"
                                                style="width:100%; border-radius:30px; outline:none;"
                                                preload="metadata"
                                                onerror="document.getElementById('audio-error').style.display='block'; this.style.display='none';">
                                                <source src="{{ url($fileUrl) }}" type="{{ $file->mime_type }}">
                                                Your browser does not support the audio element.
                                            </audio>
                                            <div id="audio-error" style="display:none;" class="alert alert-danger mt-3">
                                                <i class="ri-alarm-warning-fill mr-2"></i>Failed to load audio.
                                                <a href="{{ route('files.download', $file->id) }}" class="alert-link">Download</a> to listen.
                                            </div>
                                        </div>
                                        <div class="card-footer text-center py-3" style="background:#0d1117; border-top:1px solid rgba(255,255,255,0.08);">
                                            <a href="{{ route('files.download', $file->id) }}" class="btn btn-sm btn-outline-light">
                                                <i class="ri-download-line mr-1"></i>Download Audio
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @push('styles')
                                <style>
                                    @keyframes audioPulse {
                                        0%, 100% { transform: scale(1);   box-shadow: 0 0 0   0 rgba(124,131,253,.4); }
                                        50%       { transform: scale(1.07); box-shadow: 0 0 0 18px rgba(124,131,253,0); }
                                    }
                                </style>
                                @endpush

                                {{-- ════════════════════════════════════
                                TEXT
                                ════════════════════════════════════ --}}
                            @elseif($viewerType === 'text')
                                <div class="card bg-light m-3">
                                    <div class="card-header">
                                        <i class="ri-file-text-line mr-2"></i>Text Content
                                    </div>
                                    <div class="card-body">
                                        <pre id="text-content" class="mb-0"
                                            style="white-space:pre-wrap;word-wrap:break-word;max-height:620px;overflow-y:auto;">Loading…</pre>
                                    </div>
                                </div>

                                {{-- ════════════════════════════════════
                                ARCHIVE / UNKNOWN
                                ════════════════════════════════════ --}}
                            @else
                                <div class="text-center py-5">
                                    @if($viewerType === 'archive')
                                        <i class="ri-folder-zip-line" style="font-size:100px;color:#6c757d;"></i>
                                        <h4 class="mt-4 mb-2">Archive File</h4>
                                        <p class="text-muted small mb-4">
                                            Download to extract and view contents.
                                        </p>
                                    @else
                                        <i class="ri-file-unknow-line" style="font-size:100px;color:#6c757d;"></i>
                                        <h4 class="mt-4 mb-2">Preview Not Available</h4>
                                        <p class="text-muted small mb-4">
                                            {{ strtoupper($extension) }} files cannot be previewed in the browser.
                                        </p>
                                    @endif
                                    <a href="{{ route('files.download', $file->id) }}" class="btn btn-primary btn-lg">
                                        <i class="ri-download-line mr-2"></i>Download File
                                    </a>
                                </div>
                            @endif

                    </div>{{-- /card-body --}}
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- ════════════════════════════════════════════════════════
TEXT viewer (fetch only, no extra libraries needed)
════════════════════════════════════════════════════════ --}}
@if($viewerType === 'text')
    @push('scripts')
        <script>
            fetch("{{ url($fileUrl) }}")
                .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.text(); })
                .then(t => { document.getElementById('text-content').textContent = t; })
                .catch(e => {
                    document.getElementById('text-content').innerHTML =
                        '<span class="text-danger"><i class="ri-error-warning-line mr-2"></i>' +
                        'Error loading file: ' + e.message +
                        '. <a href="{{ route("files.download", $file->id) }}">Download instead</a></span>';
                });
        </script>
    @endpush
@endif

{{-- ════════════════════════════════════════════════════════
DOC / DOCX → mammoth (already loaded via layout.blade.php)
We just call it directly — no duplicate
<script> tags
════════════════════════════════════════════════════════ --}}
    @if($viewerType === 'doc')
        @push('scripts')
                <script>
                    (function () {
                                                    const fileUrl = "{{ url($fileUrl) }}";

                    fetch(fileUrl)
                                                        .then(r => {
                                                            if (!r.ok) throw new Error('HTTP ' + r.status);
                    return r.arrayBuffer();
                                                        })
                                                        .then(buf => mammoth.convertToHtml({arrayBuffer: buf }))
                                                        .then(result => {
                        document.getElementById('viewer-loading').style.display = 'none';
                    const out = document.getElementById('docx-output');
                    out.innerHTML = result.value || '<p class="text-muted">Document appears to be empty.</p>';
                    out.style.display = 'block';
                                                        })
                                                        .catch(err => {
                        document.getElementById('viewer-loading').style.display = 'none';
                    document.getElementById('doc-error-msg').textContent = err.message;
                    document.getElementById('doc-error').style.display   = 'block';
                                                        });
                                                })();
            </script>
        @endpush
    @endif

{{-- ════════════════════════════════════════════════════════
XLS / XLSX → SheetJS + Handsontable
(both already loaded globally in layout.blade.php)
════════════════════════════════════════════════════════ --}}
@if($viewerType === 'spreadsheet')
    @push('scripts')
        <script>
                (function () {
                                    const fileUrl = "{{ url($fileUrl) }}";
                let hotInstance = null;
                let workbook    = null;

                fetch(fileUrl)
                                        .then(r => {
                                            if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.arrayBuffer();
                                        })
                                        .then(buf => {
                    workbook = XLSX.read(new Uint8Array(buf), { type: 'array' });

                document.getElementById('viewer-loading').style.display = 'none';
                document.getElementById('office-viewer-wrap').style.display = 'block';

                // Build sheet-tab strip + handsontable container
                const wrap = document.getElementById('office-content');
                let tabHtml = '<div class="sheet-tab-strip">';
                                            workbook.SheetNames.forEach((name, i) => {
                        tabHtml += `<button class="sheet-tab btn btn-sm ${i === 0 ? 'btn-primary' : 'btn-outline-secondary'}"
                                                                      data-sheet="${escH(name)}"
                                                                      style="border-radius:6px 6px 0 0;">${escH(name)}</button>`;
                                            });
                    tabHtml += '</div><div id="hot-container" style="height:580px;"></div>';
                wrap.innerHTML = tabHtml;

                                            // Tab click handler
                                            wrap.querySelectorAll('.sheet-tab').forEach(btn => {
                    btn.addEventListener('click', function () {
                        wrap.querySelectorAll('.sheet-tab').forEach(b => {
                            b.classList.remove('btn-primary');
                            b.classList.add('btn-outline-secondary');
                        });
                        this.classList.remove('btn-outline-secondary');
                        this.classList.add('btn-primary');
                        renderSheet(this.dataset.sheet);
                    });
                                            });

                renderSheet(workbook.SheetNames[0]);
                                        })
                                        .catch(err => {
                    document.getElementById('viewer-loading').style.display = 'none';
                document.getElementById('viewer-error-msg').textContent = 'Spreadsheet error: ' + err.message;
                document.getElementById('viewer-error').style.display   = 'block';
                                        });

                function renderSheet(name) {
                                        const sheet = workbook.Sheets[name];
                const data  = XLSX.utils.sheet_to_json(sheet, {header: 1, defval: '' });

                if (hotInstance) {hotInstance.destroy(); hotInstance = null; }

                const container = document.getElementById('hot-container');
                container.innerHTML = '';

                hotInstance = new Handsontable(container, {
                    data              : data,
                readOnly          : true,
                rowHeaders        : true,
                colHeaders        : true,
                stretchH          : 'all',
                height            : 560,
                licenseKey        : 'non-commercial-and-evaluation',
                contextMenu       : false,
                manualColumnResize: true,
                manualRowResize   : true,
                renderAllRows     : false,
                viewportRowRenderingOffset: 50,
                                        });
                                    }

                function escH(s) {
                                        return String(s)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                                            .replace(/>/g, '&gt;');
                                    }
                                }) ();
        </script>
    @endpush
@endif

{{-- ════════════════════════════════════════════════════════
PPT / PPTX → LibreOffice server-side PDF conversion
Fetches /files/{id}/preview-pdf which runs LibreOffice
headless and streams back a PDF. Result is cached.
════════════════════════════════════════════════════════ --}}
@if($viewerType === 'presentation')
    @push('scripts')
        <script>
            (function () {
                const previewUrl = "{{ route('files.preview-pdf', $file->id) }}";
                const frame = document.getElementById('ppt-pdf-frame');
                const loading = document.getElementById('viewer-loading');
                const wrap = document.getElementById('ppt-pdf-wrap');
                const errBox = document.getElementById('viewer-error');
                const errMsg = document.getElementById('viewer-error-msg');

                // Test the endpoint first so we can show a proper error if it fails
                fetch(previewUrl, { method: 'HEAD' })
                    .then(r => {
                        if (!r.ok) throw new Error('Conversion failed (HTTP ' + r.status + ')');
                        // Success — load in iframe
                        frame.src = previewUrl;
                        frame.onload = function () {
                            loading.style.display = 'none';
                            wrap.style.display = 'block';
                        };
                    })
                    .catch(err => {
                        loading.style.display = 'none';
                        errMsg.textContent = err.message;
                        errBox.style.display = 'block';
                    });
            })();
        </script>
    @endpush
@endif