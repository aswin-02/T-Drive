@extends('layout')
@section('title', 'Reports Dashboard')
@section('body-page', 'admin-reports')
@section('content')
    <!-- Drag and Drop Overlay -->
    <div id="dropOverlay" style="display: none;">
        <div class="drop-content">
            <i class="ri-upload-cloud-2-line"></i>
            <h3>Drop files to upload</h3>
            <p>Release to start uploading</p>
        </div>
    </div>

    <div class="container-fluid" id="dashboardContainer">
        <div class="row">
            <!-- Folders -->
            <div class="col-lg-12">
                <div class="card card-block card-stretch card-transparent">
                    <div class="card-header d-flex justify-content-between pb-0">
                        <div class="header-title">
                            <h4 class="card-title">Folders</h4>
                        </div>
                        <div class="card-header-toolbar d-flex align-items-center">
                            <span class="text-muted">{{ $folders->count() }} folder(s)</span>
                        </div>
                    </div>
                </div>
            </div>

            @forelse($folders as $folder)
                <div class="col-md-6 col-sm-6 col-lg-3">
                    <div class="card card-block card-stretch card-height">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('folders.show', $folder->id) }}" class="folder">
                                    <div class="icon-small bg-warning rounded mb-4">
                                        <i class="ri-folder-fill"></i>
                                    </div>
                                </a>
                                <div class="card-header-toolbar">
                                    <div class="dropdown">
                                        <span class="dropdown-toggle" id="folderMenu{{ $folder->id }}" data-toggle="dropdown">
                                            <i class="ri-more-2-fill"></i>
                                        </span>
                                        <div class="dropdown-menu dropdown-menu-right"
                                            aria-labelledby="folderMenu{{ $folder->id }}">
                                            <a class="dropdown-item" href="{{ route('folders.show', $folder->id) }}"><i
                                                    class="ri-eye-fill mr-2"></i>Open</a>
                                            <a class="dropdown-item" href="#"><i class="ri-pencil-fill mr-2"></i>Rename</a>
                                            <a class="dropdown-item delete-folder" href="#"
                                                data-folder-id="{{ $folder->id }}"><i
                                                    class="ri-delete-bin-6-fill mr-2"></i>Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('folders.show', $folder->id) }}" class="folder">
                                <h5 class="mb-2 text-truncate" title="{{ $folder->name }}">{{ $folder->name }}</h5>
                                <p class="mb-2"><i class="lar la-clock text-warning mr-2 font-size-20"></i>
                                    {{ $folder->created_at->format('d M, Y') }}</p>
                                <p class="mb-0"><i class="las la-file-alt text-warning mr-2 font-size-20"></i>
                                    {{ $folder->files->count() }} Files</p>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="ri-information-line"></i> No folders created yet. Click "New Folder" in the sidebar to create
                        one.
                    </div>
                </div>
            @endforelse

            <!-- Documents -->
            <div class="col-lg-12">
                <div class="card card-block card-stretch card-transparent ">
                    <div class="card-header d-flex justify-content-between pb-0">
                        <div class="header-title">
                            <h4 class="card-title">Documents</h4>
                        </div>
                        <div class="card-header-toolbar d-flex align-items-center">
                            <span class="text-muted">{{ $files->count() }} file(s)</span>
                        </div>
                    </div>
                </div>
            </div>

            @forelse($files as $file)
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-block card-stretch card-height">
                        <div class="card-body image-thumb">
                            <div>
                                <div class="mb-4 text-center p-3 rounded iq-thumb">
                                    <div class="iq-image-overlay"></div>
                                    <img src="{{ $file->icon }}" class="img-fluid" alt="{{ $file->original_name }}">
                                </div>
                                <h6 class="text-truncate" title="{{ $file->original_name }}">{{ $file->original_name }}</h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="text-muted small mb-0">{{ $file->formatted_size }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="#" class="share-file d-flex justify-content-between align-items-center mx-2"
                                            data-file-id="{{ $file->id }}">
                                            <i class="ri-share-fill"></i>
                                        </a>
                                        <div class="card-header-toolbar">
                                            <div class="dropdown">
                                                <span class="dropdown-toggle" id="fileMenu{{ $file->id }}"
                                                    data-toggle="dropdown">
                                                    <i class="ri-more-2-fill"></i>
                                                </span>
                                                <div class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="fileMenu{{ $file->id }}">
                                                    <a class="dropdown-item" href="{{ route('files.view', $file->id) }}"><i
                                                            class="ri-eye-fill mr-2"></i>Open</a>
                                                    <a class="dropdown-item" href="#"><i
                                                            class="ri-pencil-fill mr-2"></i>Rename</a>
                                                    <a class="dropdown-item delete-file" href="#"
                                                        data-file-id="{{ $file->id }}"><i
                                                            class="ri-delete-bin-6-fill mr-2"></i>Delete</a>
                                                    <a class="dropdown-item" href="{{ $file->download_url }}" download><i
                                                            class="ri-download-line mr-2"></i>Download</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="ri-information-line"></i> No files uploaded yet. Click "Upload Files" in the sidebar to get
                        started.
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Share Modal -->
    <div class="modal fade" id="shareModal" tabindex="-1" role="dialog" aria-labelledby="shareModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="shareModalLabel"><i class="ri-share-fill mr-2"></i>Share File</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="shareForm">
                        <input type="hidden" id="shareFileId" name="file_id">
                        <input type="hidden" id="shareType" name="shareable_type" value="file">

                        <!-- Share Type Selection -->
                        <div class="form-group">
                            <label>Share with:</label>
                            <div class="custom-control custom-radio mb-2">
                                <input type="radio" id="shareLink" name="access_type" class="custom-control-input"
                                    value="link" checked>
                                <label class="custom-control-label" for="shareLink">
                                    <i class="ri-links-fill mr-1"></i> Anyone with the link
                                </label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="shareEmail" name="access_type" class="custom-control-input"
                                    value="email">
                                <label class="custom-control-label" for="shareEmail">
                                    <i class="ri-mail-fill mr-1"></i> Specific users
                                </label>
                            </div>
                        </div>

                        <!-- Email Input Section (hidden by default) -->
                        <div id="emailSection" class="form-group" style="display: none;">
                            <label>Email addresses:</label>
                            <div id="emailInputs">
                                <div class="input-group mb-2 email-input-group">
                                    <input type="email" class="form-control email-input" placeholder="Enter email address"
                                        required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-danger remove-email" type="button"
                                            style="display: none;">
                                            <i class="ri-close-line"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="addEmailBtn">
                                <i class="ri-add-line"></i> Add another email
                            </button>
                        </div>

                        <!-- Permission Selection -->
                        <div class="form-group">
                            <label for="permission">Permission:</label>
                            <select class="form-control" id="permission" name="permission" required>
                                <option value="view">View only</option>
                                <option value="download" selected>View and download</option>
                                <option value="edit">View, download and edit</option>
                            </select>
                        </div>

                        <!-- Expiration Date -->
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="setExpiration">
                                <label class="custom-control-label" for="setExpiration">Set expiration date</label>
                            </div>
                            <input type="datetime-local" class="form-control mt-2" id="expiresAt" name="expires_at"
                                style="display: none;">
                        </div>

                        <!-- Link Display (shown after creation for link type) -->
                        <div id="shareLinkDisplay" class="form-group" style="display: none;">
                            <label>Share link:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="generatedLink" readonly>
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="button" id="copyLinkBtn">
                                        <i class="ri-file-copy-line"></i> Copy
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="shareSubmitBtn">
                        <i class="ri-share-fill mr-1"></i> Share
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        #dropOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(10px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.2s ease-in-out;
        }

        .drop-content {
            text-align: center;
            color: white;
            padding: 60px;
            border: 3px dashed rgba(255, 255, 255, 0.5);
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            animation: scaleIn 0.3s ease-in-out;
            transition: all 0.3s ease;
        }

        .drop-content:hover {
            border-color: #3085d6;
            background: rgba(48, 133, 214, 0.2);
            transform: scale(1.05);
        }

        .drop-content i {
            font-size: 80px;
            color: #3085d6;
            margin-bottom: 20px;
            animation: bounce 1s infinite;
        }

        .drop-content h3 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
            color: white;
        }

        .drop-content p {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.8);
            margin: 0;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes scaleIn {
            from {
                transform: scale(0.8);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        #dashboardContainer.drag-over {
            opacity: 0.5;
            pointer-events: none;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function () {
            let currentFileId = null;
            let dragCounter = 0;

            // Drag and Drop functionality
            const dropOverlay = $('#dropOverlay');
            const dashboardContainer = $('#dashboardContainer');
            const body = $('body');

            // Prevent default drag behaviors on the entire document
            $(document).on('dragover drop', function (e) {
                e.preventDefault();
                e.stopPropagation();
            });

            // Show overlay when dragging files over the window
            $(document).on('dragenter', function (e) {
                e.preventDefault();
                e.stopPropagation();

                // Only show overlay if files are being dragged
                if (e.originalEvent.dataTransfer.types.includes('Files')) {
                    dragCounter++;
                    if (dragCounter === 1) {
                        dropOverlay.fadeIn(200);
                        dashboardContainer.addClass('drag-over');
                    }
                }
            });

            // Hide overlay when dragging leaves the window
            $(document).on('dragleave', function (e) {
                e.preventDefault();
                e.stopPropagation();

                dragCounter--;
                if (dragCounter === 0) {
                    dropOverlay.fadeOut(200);
                    dashboardContainer.removeClass('drag-over');
                }
            });

            // Handle file drop
            $(document).on('drop', function (e) {
                e.preventDefault();
                e.stopPropagation();

                dragCounter = 0;
                dropOverlay.fadeOut(200);
                dashboardContainer.removeClass('drag-over');

                const files = e.originalEvent.dataTransfer.files;

                if (files.length > 0) {
                    uploadDroppedFiles(files);
                }
            });

            // Function to upload dropped files
            function uploadDroppedFiles(files) {
                const formData = new FormData();

                // Validate and append files
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/svg+xml', 'image/webp',
                    'application/pdf', 'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-powerpoint',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'application/zip', 'application/x-zip-compressed'];

                const maxSize = 100 * 1024 * 1024; // 100MB
                let validFiles = [];
                let invalidFiles = [];

                for (let i = 0; i < files.length; i++) {
                    const file = files[i];

                    if (!allowedTypes.includes(file.type)) {
                        invalidFiles.push(file.name + ' (unsupported type)');
                    } else if (file.size > maxSize) {
                        invalidFiles.push(file.name + ' (file too large)');
                    } else {
                        validFiles.push(file);
                        formData.append('files[]', file);
                    }
                }

                // Show warning if there are invalid files
                if (invalidFiles.length > 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Some files cannot be uploaded',
                        html: '<p>The following files were skipped:</p><ul style="text-align: left;">' +
                            invalidFiles.map(f => '<li>' + f + '</li>').join('') + '</ul>',
                        confirmButtonText: validFiles.length > 0 ? 'Continue with valid files' : 'OK',
                        showCancelButton: validFiles.length > 0,
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed && validFiles.length > 0) {
                            performUpload(formData, validFiles.length);
                        }
                    });
                } else if (validFiles.length > 0) {
                    performUpload(formData, validFiles.length);
                }
            }

            // Perform the actual upload
            function performUpload(formData, fileCount) {
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
                            uploadStatus.textContent = `Uploading ${fileCount} file(s)... ${uploadedMB} MB / ${totalMB} MB`;
                        }
                    }
                });

                // Handle upload completion
                xhr.addEventListener('load', function () {
                    if (xhr.status === 200) {
                        try {
                            const data = JSON.parse(xhr.responseText);

                            Swal.fire({
                                icon: 'success',
                                title: 'Upload Complete!',
                                text: data.message || `${fileCount} file(s) uploaded successfully!`,
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
                    Swal.fire({
                        icon: 'error',
                        title: 'Upload Failed',
                        text: 'Network error. Please check your connection and try again.',
                        confirmButtonText: 'OK'
                    });
                });

                // Send the request
                xhr.open('POST', '{{ route("files.upload") }}', true);
                xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.send(formData);
            }

            // Open share modal
            $('.share-file').on('click', function (e) {
                e.preventDefault();
                currentFileId = $(this).data('file-id');
                $('#shareFileId').val(currentFileId);
                $('#shareModal').modal('show');

                // Reset form
                $('#shareForm')[0].reset();
                $('#shareLink').prop('checked', true);
                $('#emailSection').hide();
                $('#shareLinkDisplay').hide();
                resetEmailInputs();
            });

            // Toggle email section
            $('input[name="access_type"]').on('change', function () {
                if ($(this).val() === 'email') {
                    $('#emailSection').slideDown();
                    $('.email-input').prop('required', true);
                } else {
                    $('#emailSection').slideUp();
                    $('.email-input').prop('required', false);
                }
            });

            // Add email input
            $('#addEmailBtn').on('click', function () {
                const emailGroup = `
                                                                <div class="input-group mb-2 email-input-group">
                                                                    <input type="email" class="form-control email-input" placeholder="Enter email address" required>
                                                                    <div class="input-group-append">
                                                                        <button class="btn btn-outline-danger remove-email" type="button">
                                                                            <i class="ri-close-line"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            `;
                $('#emailInputs').append(emailGroup);
                updateRemoveButtons();
            });

            // Remove email input
            $(document).on('click', '.remove-email', function () {
                $(this).closest('.email-input-group').remove();
                updateRemoveButtons();
            });

            // Toggle expiration date
            $('#setExpiration').on('change', function () {
                if ($(this).is(':checked')) {
                    $('#expiresAt').slideDown().prop('required', true);

                    // Set minimum date to now
                    const now = new Date();
                    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                    $('#expiresAt').attr('min', now.toISOString().slice(0, 16));
                } else {
                    $('#expiresAt').slideUp().prop('required', false).val('');
                }
            });

            // Submit share form
            $('#shareSubmitBtn').on('click', function () {
                const accessType = $('input[name="access_type"]:checked').val();
                const permission = $('#permission').val();
                const expiresAt = $('#setExpiration').is(':checked') ? $('#expiresAt').val() : null;

                let emails = [];
                if (accessType === 'email') {
                    // Validate and collect emails
                    let isValid = true;
                    $('.email-input').each(function () {
                        const email = $(this).val().trim();
                        if (email === '') {
                            isValid = false;
                            $(this).addClass('is-invalid');
                        } else {
                            $(this).removeClass('is-invalid');
                            emails.push(email);
                        }
                    });

                    if (!isValid) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Missing Information',
                            text: 'Please fill in all email addresses'
                        });
                        return;
                    }

                    // Check for duplicates
                    const uniqueEmails = [...new Set(emails)];
                    if (uniqueEmails.length !== emails.length) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Duplicate Emails',
                            text: 'Please remove duplicate email addresses'
                        });
                        return;
                    }
                    emails = uniqueEmails;
                }

                // Prepare data
                const shareData = {
                    file_id: currentFileId,
                    shareable_type: 'file',
                    access_type: accessType,
                    permission: permission,
                    expires_at: expiresAt,
                    emails: emails
                };

                // Send AJAX request
                $.ajax({
                    url: '/shares',
                    method: 'POST',
                    data: shareData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (accessType === 'link' && response.share_url) {
                            // Show the generated link
                            $('#generatedLink').val(response.share_url);
                            $('#shareLinkDisplay').slideDown();
                            $('#shareSubmitBtn').hide();

                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Share link created successfully!',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            // Close modal and show success
                            $('#shareModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Shared!',
                                text: 'File shared successfully!',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function (xhr) {
                        const message = xhr.responseJSON?.message || 'Error sharing file. Please try again.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: message
                        });
                    }
                });
            });

            // Copy link to clipboard
            $('#copyLinkBtn').on('click', function () {
                const linkInput = document.getElementById('generatedLink');
                linkInput.select();
                linkInput.setSelectionRange(0, 99999); // For mobile devices

                document.execCommand('copy');

                // Visual feedback
                const originalText = $(this).html();
                $(this).html('<i class="ri-check-line"></i> Copied!');
                setTimeout(() => {
                    $(this).html(originalText);
                }, 2000);
            });

            // Helper function to update remove button visibility
            function updateRemoveButtons() {
                const emailGroups = $('.email-input-group');
                if (emailGroups.length === 1) {
                    emailGroups.find('.remove-email').hide();
                } else {
                    emailGroups.find('.remove-email').show();
                }
            }

            // Helper function to reset email inputs
            function resetEmailInputs() {
                $('#emailInputs').html(`
                                                                <div class="input-group mb-2 email-input-group">
                                                                    <input type="email" class="form-control email-input" placeholder="Enter email address" required>
                                                                    <div class="input-group-append">
                                                                        <button class="btn btn-outline-danger remove-email" type="button" style="display: none;">
                                                                            <i class="ri-close-line"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            `);
                $('#shareSubmitBtn').show();
            }

            // Delete file handler
            $('.delete-file').on('click', function (e) {
                e.preventDefault();
                const fileId = $(this).data('file-id');
                const fileCard = $(this).closest('.col-lg-3');

                Swal.fire({
                    title: 'Move to Trash?',
                    text: 'You can restore this file from the trash later',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, move to trash!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/files/${fileId}`,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                if (response.success) {
                                    // Remove the file card with animation
                                    fileCard.fadeOut(300, function () {
                                        $(this).remove();

                                        // Update file count
                                        const fileCount = $('.col-lg-3').length - 1;
                                        $('.card-header-toolbar span').text(`${fileCount} file(s)`);

                                        // Show empty state if no files left
                                        if (fileCount === 0) {
                                            $('.col-lg-12').last().after(`
                                                                            <div class="col-12">
                                                                                <div class="alert alert-info">
                                                                                    <i class="ri-information-line"></i> No files uploaded yet. Click "Upload Files" in the sidebar to get started.
                                                                                </div>
                                                                            </div>
                                                                        `);
                                        }
                                    });

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Moved to Trash!',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                }
                            },
                            error: function (xhr) {
                                const message = xhr.responseJSON?.message || 'Error deleting file. Please try again.';
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: message
                                });
                            }
                        });
                    }
                });
            });

            // Delete folder handler
            $('.delete-folder').on('click', function (e) {
                e.preventDefault();
                const folderId = $(this).data('folder-id');
                const folderCard = $(this).closest('.col-md-6');

                Swal.fire({
                    title: 'Move to Trash?',
                    text: 'You can restore this folder from the trash later',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, move to trash!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/folders/${folderId}`,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                if (response.success) {
                                    // Remove the folder card with animation
                                    folderCard.fadeOut(300, function () {
                                        $(this).remove();

                                        // Update folder count
                                        const folderCount = $('.col-md-6').length - 1;

                                        // Show empty state if no folders left
                                        if (folderCount === 0) {
                                            $('.col-lg-12').first().after(`
                                                                <div class="col-12">
                                                                    <div class="alert alert-info">
                                                                        <i class="ri-information-line"></i> No folders created yet. Click "New Folder" in the sidebar to create one.
                                                                    </div>
                                                                </div>
                                                            `);
                                        }
                                    });

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Moved to Trash!',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                }
                            },
                            error: function (xhr) {
                                const message = xhr.responseJSON?.message || 'Error deleting folder. Please try again.';
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: message
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush