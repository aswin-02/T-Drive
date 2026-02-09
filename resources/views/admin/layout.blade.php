<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>CloudBOX | Responsive Bootstrap 4 Admin Dashboard Template</title>

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
</head>

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
                    <div class="iq-search-bar device-search">

                        <form>
                            <div class="input-prepend input-append">
                                <div class="btn-group">
                                    <label class="dropdown-toggle searchbox" data-toggle="dropdown">
                                        <input class="dropdown-toggle search-query text search-input" type="text"
                                            placeholder="Type here to search..."><span class="search-replace"></span>
                                        <a class="search-link" href="#"><i class="ri-search-line"></i></a>
                                        <span class="caret"><!--icon--></span>
                                    </label>
                                    <ul class="dropdown-menu">
                                        <li><a href="#">
                                                <div class="item"><i class="far fa-file-pdf bg-info"></i>PDFs</div>
                                            </a></li>
                                        <li><a href="#">
                                                <div class="item"><i class="far fa-file-alt bg-primary"></i>Documents
                                                </div>
                                            </a></li>
                                        <li><a href="#">
                                                <div class="item"><i
                                                        class="far fa-file-excel bg-success"></i>Spreadsheet</div>
                                            </a></li>
                                        <li><a href="#">
                                                <div class="item"><i
                                                        class="far fa-file-powerpoint bg-danger"></i>Presentation</div>
                                            </a></li>
                                        <li><a href="#">
                                                <div class="item"><i class="far fa-file-image bg-warning"></i>Photos &
                                                    Images</div>
                                            </a></li>
                                        <li><a href="#">
                                                <div class="item"><i class="far fa-file-video bg-info"></i>Videos</div>
                                            </a></li>
                                    </ul>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="d-flex align-items-center">
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
                                        <form action="#" class="searchbox p-2">
                                            <div class="form-group mb-0 position-relative">
                                                <input type="text" class="text search-input font-size-12"
                                                    placeholder="type here to search...">
                                                <a href="#" class="search-link"><i class="las la-search"></i></a>
                                            </div>
                                        </form>
                                    </div>
                                </li>
                                <li class="nav-item nav-icon dropdown">
                                    <a href="#" class="search-toggle dropdown-toggle" id="dropdownMenuButton01"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="ri-question-line"></i>
                                    </a>
                                    <div class="iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownMenuButton01">
                                        <div class="card shadow-none m-0">
                                            <div class="card-body p-0 ">
                                                <div class="p-3">
                                                    <a href="#" class="iq-sub-card pt-0"><i
                                                            class="ri-questionnaire-line"></i>Help</a>
                                                    <a href="#" class="iq-sub-card"><i
                                                            class="ri-recycle-line"></i>Training</a>
                                                    <a href="#" class="iq-sub-card"><i
                                                            class="ri-refresh-line"></i>Updates</a>
                                                    <a href="#" class="iq-sub-card"><i class="ri-service-line"></i>Terms
                                                        and Policy</a>
                                                    <a href="#" class="iq-sub-card"><i class="ri-feedback-line"></i>Send
                                                        Feedback</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="nav-item nav-icon dropdown">
                                    <a href="#" class="search-toggle dropdown-toggle" id="dropdownMenuButton02"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="ri-settings-3-line"></i>
                                    </a>
                                    <div class="iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownMenuButton02">
                                        <div class="card shadow-none m-0">
                                            <div class="card-body p-0 ">
                                                <div class="p-3">
                                                    <a href="#" class="iq-sub-card pt-0"><i
                                                            class="ri-settings-3-line"></i> Settings</a>
                                                    <a href="#" class="iq-sub-card"><i class="ri-hard-drive-line"></i>
                                                        Get Drive for desktop</a>
                                                    <a href="#" class="iq-sub-card"><i class="ri-keyboard-line"></i>
                                                        Keyboard Shortcuts</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="nav-item nav-icon dropdown caption-content">
                                    <a href="#" class="search-toggle dropdown-toggle" id="dropdownMenuButton03"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <div class="caption bg-primary line-height">P</div>
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
                                                            P
                                                            <a href="#">

                                                            </a>
                                                        </div>
                                                        <div class="profile-detail mt-3">
                                                            <h5><a
                                                                    href="https://templates.iqonic.design/cloudbox/html/app/user-profile-edit.html">Panny
                                                                    Marco</a></h5>
                                                            <p>pannymarco@gmail.com</p>
                                                        </div>
                                                        <a href="auth-sign-in.html" class="btn btn-primary">Sign Out</a>
                                                    </div>
                                                    <div class="profile-details mt-4 pt-4 border-top">
                                                        <div class="media align-items-center mb-3">
                                                            <div class="rounded-circle iq-card-icon-small bg-primary">
                                                                A
                                                            </div>
                                                            <div class="media-body ml-3">
                                                                <div class="media justify-content-between">
                                                                    <h6 class="mb-0">Anna Mull</h6>
                                                                    <p class="mb-0 font-size-12"><i>Signed Out</i></p>
                                                                </div>
                                                                <p class="mb-0 font-size-12">annamull@gmail.com</p>
                                                            </div>
                                                        </div>
                                                        <div class="media align-items-center mb-3">
                                                            <div class="rounded-circle iq-card-icon-small bg-success">
                                                                K
                                                            </div>
                                                            <div class="media-body ml-3">
                                                                <div class="media justify-content-between">
                                                                    <h6 class="mb-0">King Poilin</h6>
                                                                    <p class="mb-0 font-size-12"><i>Signed Out</i></p>
                                                                </div>
                                                                <p class="mb-0 font-size-12">kingpoilin@gmail.com</p>
                                                            </div>
                                                        </div>
                                                        <div class="media align-items-center">
                                                            <div class="rounded-circle iq-card-icon-small bg-danger">
                                                                D
                                                            </div>
                                                            <div class="media-body ml-3">
                                                                <div class="media justify-content-between">
                                                                    <h6 class="mb-0">Devid Worner</h6>
                                                                    <p class="mb-0 font-size-12"><i>Signed Out</i></p>
                                                                </div>
                                                                <p class="mb-0 font-size-12">devidworner@gmail.com</p>
                                                            </div>
                                                        </div>
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
                    </span> <a href="#" class="">CloudBOX</a>.
                </div>
            </div>
        </div>
    </footer>
    <!-- Backend Bundle JavaScript -->
    <!-- <script src="{{ asset('js/backend-bundle.min.js')}}"></script> -->

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