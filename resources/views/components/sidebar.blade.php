<div class="iq-sidebar  sidebar-default " data-current-folder-id="{{ $currentFolderId ?? '' }}">
    <div class="iq-sidebar-logo d-flex align-items-center justify-content-between">
        <a href="index.html" class="header-logo">
            <img src="{{ asset('images/logo.png') }}" class="img-fluid rounded-normal light-logo" alt="logo">
            <img src="{{ asset('images/logo-white.png') }}" class="img-fluid rounded-normal darkmode-logo" alt="logo">
        </a>
        <div class="iq-menu-bt-sidebar">
            <i class="las la-bars wrapper-menu"></i>
        </div>
    </div>
    <div class="data-scrollbar" data-scroll="1">
        <!-- Hidden file input -->
        <input type="file" id="fileUploadInput" multiple style="display: none;"
            accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip">

        <div class="new-create select-dropdown input-prepend input-append">
            <div class="btn-group">
                <div data-toggle="dropdown">
                    <div class="search-query selet-caption"><i class="las la-plus pr-2"></i>Create New</div>
                    <span class="search-replace"></span>
                    <span class="caret"><!--icon--></span>
                </div>
                <ul class="dropdown-menu">
                    <li>
                        <div class="item" id="uploadFilesBtn" style="cursor: pointer;"><i
                                class="ri-file-upload-line pr-3"></i>Upload Files</div>
                    </li>
                    <li>
                        <div class="item" id="newFolderBtn" style="cursor: pointer;"><i
                                class="ri-folder-add-line pr-3"></i>New Folder</div>
                    </li>
                </ul>
            </div>
        </div>
        <nav class="iq-sidebar-menu">
            <ul id="iq-sidebar-toggle" class="iq-menu">
                <li class="active">
                    <a href="{{ route('dashboard') }}" class="">
                        <i class="las la-home iq-arrow-left"></i><span>Dashboard</span>
                    </a>
                    <ul id="dashboard" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                    </ul>
                </li>
                <li class=" ">
                    <a href="{{ route('recent.index') }}" class="">
                        <i class="las la-stopwatch iq-arrow-left"></i><span>Recent</span>
                    </a>
                </li>
                <li class=" ">
                    <a href="{{ route('trash.index') }}" class="">
                        <i class="las la-trash-alt iq-arrow-left"></i><span>Trash</span>
                    </a>
                    <ul id="page-delete" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                    </ul>
                </li>
                <li class=" ">
                    <a href="{{ route('shares.shared-with-me') }}" class="">
                        <i class="las la-share-alt iq-arrow-left"></i><span>Shared With Me</span>
                    </a>
                    <ul id="page-shared" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                    </ul>
                </li>
                <li class=" ">
                    <a href="#otherpage" class="collapsed" data-toggle="collapse" aria-expanded="false">
                        <i class="lab la-wpforms iq-arrow-left"></i><span>Master</span>
                        <i class="las la-angle-right iq-arrow-right arrow-active"></i>
                        <i class="las la-angle-down iq-arrow-right arrow-hover"></i>
                    </a>
                    <ul id="otherpage" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                        @can('View Roles')
                            <li class="{{ request()->routeIs('admin.roles.*') ? ' active' : '' }}">
                                <a class="nav-link menu-title" href="{{ route('admin.roles.index') }}">
                                    <i data-feather="shield"></i><span>Roles & Permissions</span>
                                </a>
                            </li>
                        @endcan
                        @can('View Users')
                            <li class="{{ request()->routeIs('admin.users.*') ? ' active' : '' }}">
                                <a class="nav-link menu-title" href="{{ route('admin.users.index') }}">
                                    <i data-feather="users"></i><span>Users</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            </ul>
        </nav>
        <div class="sidebar-bottom">
            <h4 class="mb-3"><i class="las la-cloud mr-2"></i>Storage</h4>
            @if(isset($storage))
                <p>{{ $storage['used_gb'] }} / {{ $storage['max_gb'] }} GB Used</p>
                <div class="iq-progress-bar mb-3">
                    <span class="bg-primary iq-progress progress-1" data-percent="{{ $storage['percentage'] }}">
                    </span>
                </div>
                <p>{{ $storage['percentage'] }}% Full - {{ $storage['free_gb'] }} GB Free</p>
            @else
                <p>0 / 5 GB Used</p>
                <div class="iq-progress-bar mb-3">
                    <span class="bg-primary iq-progress progress-1" data-percent="0">
                    </span>
                </div>
                <p>0% Full - 5 GB Free</p>
            @endif
        </div>
        <div class="p-3"></div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const uploadBtn = document.getElementById('uploadFilesBtn');
        const fileInput = document.getElementById('fileUploadInput');

        if (uploadBtn && fileInput) {
            uploadBtn.addEventListener('click', function (e) {
                e.preventDefault();
                fileInput.click();
            });

            fileInput.addEventListener('change', function (e) {
                const files = e.target.files;
                if (files.length > 0) {
                    uploadFiles(files);
                }
            });
        }

        function uploadFiles(files) {
            const formData = new FormData();

            // Append all files to FormData
            for (let i = 0; i < files.length; i++) {
                formData.append('files[]', files[i]);
            }

            // Get current folder ID from sidebar
            const sidebar = document.querySelector('.iq-sidebar');
            const currentFolderId = sidebar ? sidebar.getAttribute('data-current-folder-id') : '';
            if (currentFolderId) {
                formData.append('folder_id', currentFolderId);
            }

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');

            // Show progress modal
            Swal.fire({
                title: 'Uploading Files...',
                html: `
                    <div class="upload-progress-container">
                        <div class="progress" style="height: 30px; margin: 20px 0;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                                 role="progressbar" 
                                 id="uploadProgressBar" 
                                 style="width: 0%; font-size: 14px; font-weight: bold;">
                                0%
                            </div>
                        </div>
                        <p id="uploadStatus" class="text-muted">Preparing upload...</p>
                    </div>
                `,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Create XMLHttpRequest
            const xhr = new XMLHttpRequest();

            // Track upload progress
            xhr.upload.addEventListener('progress', function (e) {
                if (e.lengthComputable) {
                    const percentComplete = Math.round((e.loaded / e.total) * 100);
                    const progressBar = document.getElementById('uploadProgressBar');
                    const uploadStatus = document.getElementById('uploadStatus');

                    if (progressBar) {
                        progressBar.style.width = percentComplete + '%';
                        progressBar.textContent = percentComplete + '%';
                    }

                    if (uploadStatus) {
                        const uploadedMB = (e.loaded / 1024 / 1024).toFixed(2);
                        const totalMB = (e.total / 1024 / 1024).toFixed(2);
                        uploadStatus.textContent = `Uploading... ${uploadedMB} MB / ${totalMB} MB`;
                    }
                }
            });

            // Handle upload completion
            xhr.addEventListener('load', function () {
                if (xhr.status === 200) {
                    try {
                        const data = JSON.parse(xhr.responseText);

                        // Reset file input
                        fileInput.value = '';

                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.message || 'Files uploaded successfully!',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } catch (error) {
                        console.error('Parse error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Upload Failed',
                            text: 'Error processing server response.',
                            confirmButtonText: 'OK'
                        });
                    }
                } else {
                    // Handle HTTP errors
                    Swal.fire({
                        icon: 'error',
                        title: 'Upload Failed',
                        text: `Server error: ${xhr.status}. Please try again.`,
                        confirmButtonText: 'OK'
                    });
                }
            });

            // Handle network errors
            xhr.addEventListener('error', function () {
                console.error('Upload error');
                Swal.fire({
                    icon: 'error',
                    title: 'Upload Failed',
                    text: 'Network error. Please check your connection and try again.',
                    confirmButtonText: 'OK'
                });
            });

            // Handle upload abort
            xhr.addEventListener('abort', function () {
                Swal.fire({
                    icon: 'warning',
                    title: 'Upload Cancelled',
                    text: 'File upload was cancelled.',
                    confirmButtonText: 'OK'
                });
            });

            // Send the request
            xhr.open('POST', '{{ route("files.upload") }}', true);
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken ? csrfToken.content : '');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.send(formData);
        }

        // Handle New Folder button click
        const newFolderBtn = document.getElementById('newFolderBtn');
        if (newFolderBtn) {
            newFolderBtn.addEventListener('click', function (e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Create New Folder',
                    html: `
                        <div class="form-group text-left">
                            <label for="folderName" class="form-label">Folder Name</label>
                            <input type="text" id="folderName" class="form-control" placeholder="Enter folder name" autocomplete="off">
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Create',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    focusConfirm: false,
                    preConfirm: () => {
                        const folderName = document.getElementById('folderName').value.trim();

                        // Allow empty names (will default to 'New Folder' on backend)
                        if (folderName && folderName.length > 255) {
                            Swal.showValidationMessage('Folder name is too long (max 255 characters)');
                            return false;
                        }

                        return folderName || ''; // Return empty string if no name provided
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const folderName = result.value;

                        // Show loading
                        Swal.fire({
                            title: 'Creating Folder...',
                            text: 'Please wait',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Get CSRF token
                        const csrfToken = document.querySelector('meta[name="csrf-token"]');

                        // Get current folder ID from sidebar
                        const sidebar = document.querySelector('.iq-sidebar');
                        const currentFolderId = sidebar ? sidebar.getAttribute('data-current-folder-id') : '';

                        // Send AJAX request to create folder
                        fetch('{{ route("folders.create") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken ? csrfToken.content : '',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                name: folderName,
                                parent_id: currentFolderId || null // Use current folder or null for root
                            })
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        text: data.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Failed',
                                        text: data.message || 'Failed to create folder',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Folder creation error:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'An error occurred while creating the folder.',
                                    confirmButtonText: 'OK'
                                });
                            });
                    }
                });
            });
        }
    });
</script>