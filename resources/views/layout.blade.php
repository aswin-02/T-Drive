<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>T-Drive</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico')}}" />

    <link rel="stylesheet" href="{{ asset('css/backend-plugin.min.css')}}">
    <link rel="stylesheet" href="{{ asset('css/backende209.css?v=1.0.0')}}">

    <link rel="stylesheet" href="{{ asset('vendor/%40fortawesome/fontawesome-free/css/all.min.css')}}">
    <link rel="stylesheet" href="{{ asset('vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css')}}">
    <link rel="stylesheet" href="{{ asset('vendor/remixicon/fonts/remixicon.css')}}">

    <!-- Viewer Plugin -->
    <!--PDF-->
    <link rel="stylesheet" href="{{ asset('vendor/doc-viewer/include/pdf/pdf.viewer.css')}}">
    <!--Docs-->
    <!--PPTX-->
    <link rel="stylesheet" href="{{ asset('vendor/doc-viewer/include/PPTXjs/css/pptxjs.css')}}">
    <link rel="stylesheet" href="{{ asset('vendor/doc-viewer/include/PPTXjs/css/nv.d3.min.css')}}">
    <!--All Spreadsheet -->
    <link rel="stylesheet" href="{{ asset('vendor/doc-viewer/include/SheetJS/handsontable.full.min.css')}}">
    <!--Image viewer-->
    <link rel="stylesheet"
        href="{{ asset('vendor/doc-viewer/include/verySimpleImageViewer/css/jquery.verySimpleImageViewer.css')}}">
    <!--officeToHtml-->
    <link rel="stylesheet" href="{{ asset('vendor/doc-viewer/include/officeToHtml/officeToHtml.css')}}">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<style>
    .dark #globalSuggestionsBox {
        background-color: #000;
        border:1px solid #3d3d3dff;
    }

    #globalSuggestionsBox {
        background-color: #fff;
        border:1px solid #e2e8f0;
    }
</style>
@stack('styles')

<body class="  ">
    <!-- loader Start -->
    <div id="loading">
        <div id="loading-center">
        </div>
    </div>
    <!-- loader END -->
    <!-- Wrapper Start -->
    <div class="wrapper">

        @include('components.sidebar')
        <!-- Sidebar -->
        <div class="iq-top-navbar">
            <div class="iq-navbar-custom">
                <nav class="navbar navbar-expand-lg navbar-light p-0">
                    <div class="iq-navbar-logo d-flex align-items-center justify-content-between">
                        <i class="ri-menu-line wrapper-menu"></i>
                        <a href="index.html" class="header-logo">
                            <img src="{{ asset('images/logo.png')}}" class="img-fluid rounded-normal light-logo"
                                alt="logo">
                            <img src="{{ asset('images/logo-white.png')}}"
                                class="img-fluid rounded-normal darkmode-logo" alt="logo">
                        </a>
                    </div>
                    <div class="iq-search-bar device-search" id="globalSearchWrap" style="position:relative;">
                        <form action="{{ route('search.index') }}" method="GET" id="globalSearchForm"
                            autocomplete="off">
                            <div class="input-prepend input-append">
                                <div class="btn-group" style="width:100%;">
                                    <label class="searchbox" style="width:100%;margin:0;">
                                        <input class="search-query text search-input" type="text" name="q"
                                            id="globalSearchInput" placeholder="Type here to search…"
                                            autocomplete="off">
                                        <span class="search-replace"></span>
                                        <button type="submit" class="search-link"
                                            style="background:none;border:none;cursor:pointer;">
                                            <i class="ri-search-line"></i>
                                        </button>
                                    </label>
                                </div>
                            </div>
                        </form>
                        <!-- Live suggestion dropdown -->
                        <div id="globalSuggestionsBox" style="display:none;position:absolute;top:100%;left:0;right:0;z-index:9999;border-radius:10px;
                                    box-shadow:0 8px 24px rgba(0,0,0,.12);max-height:380px;overflow-y:auto;">
                        </div>
                    </div>

                    <div class="d-flex align-items-center my-md-4">
                        <div class="change-mode">
                            <div class="custom-control custom-switch custom-switch-icon custom-control-inline">
                                <div class="custom-switch-inner">
                                    <p class="mb-0"> </p>
                                    <input type="checkbox" class="custom-control-input" id="dark-mode"
                                        data-active="true">
                                    <label class="custom-control-label" for="dark-mode" data-mode="toggle">
                                        <span class="switch-icon-left"><i class="a-left"></i></span>
                                        <span class="switch-icon-right"><i class="a-right"></i></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <button class="navbar-toggler" type="button" data-toggle="collapse"
                            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-label="Toggle navigation">
                            <i class="ri-menu-3-line"></i>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav ml-auto navbar-list align-items-center">
                                <li class="nav-item nav-icon search-content">
                                    <a href="#" class="search-toggle rounded" id="dropdownSearch" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        <i class="ri-search-line"></i>
                                    </a>
                                    <div class="iq-search-bar iq-sub-dropdown dropdown-menu"
                                        aria-labelledby="dropdownSearch">
                                        <form action="{{ route('search.index') }}" method="GET" class="searchbox p-2">
                                            <div class="form-group mb-0 position-relative">
                                                <input type="text" name="q" class="text search-input font-size-12"
                                                    placeholder="type here to search…">
                                                <button type="submit" class="search-link"
                                                    style="background:none;border:none;cursor:pointer;">
                                                    <i class="las la-search"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </li>
                                <li class="nav-item nav-icon dropdown caption-content">
                                    <a href="#" class="search-toggle dropdown-toggle" id="dropdownMenuButton03"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <div class="caption bg-primary line-height">
                                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                        </div>
                                    </a>
                                    <div class="iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownMenuButton03">
                                        <div class="card mb-0">
                                            <div
                                                class="card-header d-flex justify-content-between align-items-center mb-0">
                                                <div class="header-title">
                                                    <h4 class="card-title mb-0">Profile</h4>
                                                </div>
                                                <div class="close-data text-right badge badge-primary cursor-pointer ">
                                                    <i class="ri-close-fill"></i>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="profile-header">
                                                    <div class="cover-container text-center">
                                                        <div
                                                            class="rounded-circle profile-icon bg-primary mx-auto d-block">
                                                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                                        </div>
                                                        <div class="profile-detail mt-3">
                                                            <h5>{{ Auth::user()->name ?? 'User' }}</h5>
                                                            <p>{{ Auth::user()->email ?? '' }}</p>
                                                        </div>
                                                        <form id="logoutForm"
                                                            action="{{ Auth::user()->user_type === 'admin' ? route('admin.logout') : route('user.logout') }}"
                                                            method="POST" style="display: inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-primary"
                                                                style="border: none; background: none; color: inherit; cursor: pointer;">
                                                                <span class="btn btn-primary">Log Out</span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
        <div class="content-page">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
    </div>
    <!-- Wrapper End-->
    <footer class="iq-footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6">
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item"><a href="privacy-policy.html">Privacy Policy</a></li>
                        <li class="list-inline-item"><a href="terms-of-service.html">Terms of Use</a></li>
                    </ul>
                </div>
                <div class="col-lg-6 text-right">
                    <span class="mr-1">
                        <script>document.write(new Date().getFullYear())</script>©
                    </span> <a href="#" class="">T-Drive</a>.
                </div>
            </div>
        </div>
    </footer>
    <!-- Backend Bundle JavaScript -->
    <script src="{{ asset('js/backend-bundle.min.js')}}"></script>

    <!-- Chart Custom JavaScript -->
    <script src="{{ asset('js/customizer.js')}}"></script>

    <!-- Chart Custom JavaScript -->
    <script src="{{ asset('js/chart-custom.js')}}"></script>

    <!--PDF-->
    <script src="{{ asset('vendor/doc-viewer/include/pdf/pdf.js')}}"></script>
    <!--Docs-->
    <script src="{{ asset('vendor/doc-viewer/include/docx/jszip-utils.js')}}"></script>
    <script src="{{ asset('vendor/doc-viewer/include/docx/mammoth.browser.min.js')}}"></script>
    <!--PPTX-->
    <script src="{{ asset('vendor/doc-viewer/include/PPTXjs/js/filereader.js')}}"></script>
    <script src="{{ asset('vendor/doc-viewer/include/PPTXjs/js/d3.min.js')}}"></script>
    <script src="{{ asset('vendor/doc-viewer/include/PPTXjs/js/nv.d3.min.js')}}"></script>
    <script src="{{ asset('vendor/doc-viewer/include/PPTXjs/js/pptxjs.js')}}"></script>
    <script src="{{ asset('vendor/doc-viewer/include/PPTXjs/js/divs2slides.js')}}"></script>
    <!--All Spreadsheet -->
    <script src="{{ asset('vendor/doc-viewer/include/SheetJS/handsontable.full.min.js')}}"></script>
    <script src="{{ asset('vendor/doc-viewer/include/SheetJS/xlsx.full.min.js')}}"></script>
    <!--Image viewer-->
    <script
        src="{{ asset('vendor/doc-viewer/include/verySimpleImageViewer/js/jquery.verySimpleImageViewer.js')}}"></script>
    <!--officeToHtml-->
    <script src="{{ asset('vendor/doc-viewer/include/officeToHtml/officeToHtml.js')}}"></script>
    <!-- app JavaScript -->
    <script src="{{ asset('js/app.js')}}"></script>
    <script src="{{ asset('js/doc-viewer.js')}}"></script>
    @stack('scripts')
    {{-- Global live-search AJAX --}}
    <script>
            (function () {
                const input = document.getElementById('globalSearchInput');
                const suggestBox = document.getElementById('globalSuggestionsBox');
                const searchForm = document.getElementById('globalSearchForm');
                if (!input) return;

                let debounceTimer = null;
                const SUGGEST_URL = '{{ route("search.suggest") }}';
                const SEARCH_URL = '{{ route("search.index") }}';
                const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                input.addEventListener('input', function () {
                    clearTimeout(debounceTimer);
                    const q = input.value.trim();
                    if (q.length < 1) { hideSuggestions(); return; }
                    debounceTimer = setTimeout(() => fetchSuggestions(q), 280);
                });

                input.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        hideSuggestions();
                        searchForm.submit();
                    }
                    if (e.key === 'Escape') hideSuggestions();
                });

                document.addEventListener('click', function (e) {
                    if (!document.getElementById('globalSearchWrap').contains(e.target)) hideSuggestions();
                });

                function fetchSuggestions(q) {
                    fetch(SUGGEST_URL + '?q=' + encodeURIComponent(q), {
                        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
                    })
                        .then(r => r.json())
                        .then(data => renderSuggestions(data, q))
                        .catch(() => { });
                }

                function renderSuggestions(data, q) {
                    const { files = [], folders = [], search_url } = data;
                    if (files.length === 0 && folders.length === 0) {
                        suggestBox.innerHTML = '<div style="padding:14px 16px;color:#6c757d;font-size:.9rem;"><i class="ri-search-eye-line mr-2"></i>No results for <strong>"' + escH(q) + '"</strong></div>';
                        showSuggestions(); return;
                    }
                    let html = '';

                    if (folders.length > 0) {
                        html += '<div style="padding:8px 14px 4px;font-size:.7rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#9ca3af;">Folders</div>';
                        folders.forEach(f => {
                            html += `<a href="${f.url}" style="display:flex;align-items:center;padding:9px 14px;text-decoration:none;color:inherit;gap:10px;" class="suggest-item">
                        <span style="width:32px;height:32px;background:linear-gradient(135deg,#fbbf24,#f59e0b);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1rem;flex-shrink:0;"><i class="ri-folder-fill"></i></span>
                        <span style="flex:1;overflow:hidden;">
                            <span style="display:block;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${escH(f.name)}</span>
                        </span>
                        <i class="ri-arrow-right-s-line" style="color:#9ca3af;"></i>
                    </a>`;
                        });
                    }

                    if (files.length > 0) {
                        html += '<div style="padding:8px 14px 4px;font-size:.7rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#9ca3af;">Files</div>';
                        files.forEach(f => {
                            html += `<a href="${f.view_url}" style="display:flex;align-items:center;padding:9px 14px;text-decoration:none;color:inherit;gap:10px;" class="suggest-item">
                        <span style="width:32px;height:32px;background:#f3f4f6;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#6366f1;font-size:1rem;flex-shrink:0;"><i class="ri-file-line"></i></span>
                        <span style="flex:1;overflow:hidden;">
                            <span style="display:block;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${escH(f.name)}</span>
                            <span style="font-size:.78rem;color:#9ca3af;">${escH(f.size)}</span>
                        </span>
                    </a>`;
                        });
                    }

                    // View all results link
                    html += `<div style="border-top:1px solid #f1f5f9;padding:8px 14px;">
                <a href="${search_url}" style="font-size:.85rem;color:#6366f1;text-decoration:none;font-weight:600;">
                    <i class="ri-search-line mr-1"></i>View all results for "${escH(q)}"
                </a></div>`;

                    suggestBox.innerHTML = html;
                    // Hover highlight for suggest items
                    suggestBox.querySelectorAll('.suggest-item').forEach(el => {
                        el.addEventListener('mouseenter', () => el.style.background = '#f8f9ff');
                        el.addEventListener('mouseleave', () => el.style.background = '');
                    });
                    showSuggestions();
                }

                function showSuggestions() { suggestBox.style.display = 'block'; }
                function hideSuggestions() { suggestBox.style.display = 'none'; }
                function escH(s) { return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }
            })();
    </script>
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Title</h4>
                    <div>
                        <a class="btn" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </a>
                    </div>
                </div>
                <div class="modal-body">
                    <div id="resolte-contaniner" style="height: 500px;" class="overflow-auto">
                        File not found
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>