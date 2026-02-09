@extends('layout')
@section('title', $folder->name)
@section('body-page', 'folder-view')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Breadcrumb Navigation -->
            <div class="col-lg-12">
                <div class="card card-block card-stretch card-transparent">
                    <div class="card-body p-0">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb bg-transparent mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i
                                            class="ri-home-4-line mr-1"></i>Home</a></li>
                                @if($breadcrumbs)
                                    @foreach($breadcrumbs as $crumb)
                                        <li class="breadcrumb-item">
                                            <a href="{{ route('folders.show', $crumb->id) }}">{{ $crumb->name }}</a>
                                        </li>
                                    @endforeach
                                @endif
                                <li class="breadcrumb-item active" aria-current="page">{{ $folder->name }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Folder Header -->
            <div class="col-lg-12">
                <div class="card card-block card-stretch card-transparent">
                    <div class="card-header d-flex justify-content-between pb-0">
                        <div class="header-title">
                            <h4 class="card-title"><i class="ri-folder-fill text-warning mr-2"></i>{{ $folder->name }}</h4>
                            <p class="text-muted mb-0">Created on {{ $folder->created_at->format('d M, Y') }}</p>
                        </div>
                        <div class="card-header-toolbar d-flex align-items-center">
                            <span class="text-muted mr-3">{{ $subFolders->count() }} folder(s), {{ $files->count() }}
                                file(s)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sub-folders -->
            @if($subFolders->count() > 0)
                <div class="col-lg-12">
                    <div class="card card-block card-stretch card-transparent">
                        <div class="card-header d-flex justify-content-between pb-0">
                            <div class="header-title">
                                <h5 class="card-title">Folders</h5>
                            </div>
                        </div>
                    </div>
                </div>

                @foreach($subFolders as $subFolder)
                    <div class="col-md-6 col-sm-6 col-lg-3">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('folders.show', $subFolder->id) }}" class="folder">
                                        <div class="icon-small bg-warning rounded mb-4">
                                            <i class="ri-folder-fill"></i>
                                        </div>
                                    </a>
                                    <div class="card-header-toolbar">
                                        <div class="dropdown">
                                            <span class="dropdown-toggle" id="folderMenu{{ $subFolder->id }}"
                                                data-toggle="dropdown">
                                                <i class="ri-more-2-fill"></i>
                                            </span>
                                            <div class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="folderMenu{{ $subFolder->id }}">
                                                <a class="dropdown-item" href="{{ route('folders.show', $subFolder->id) }}"><i
                                                        class="ri-eye-fill mr-2"></i>Open</a>
                                                <a class="dropdown-item" href="#"><i class="ri-pencil-fill mr-2"></i>Rename</a>
                                                <a class="dropdown-item" href="#"><i
                                                        class="ri-delete-bin-6-fill mr-2"></i>Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('folders.show', $subFolder->id) }}" class="folder">
                                    <h5 class="mb-2 text-truncate" title="{{ $subFolder->name }}">{{ $subFolder->name }}</h5>
                                    <p class="mb-2"><i class="lar la-clock text-warning mr-2 font-size-20"></i>
                                        {{ $subFolder->created_at->format('d M, Y') }}</p>
                                    <p class="mb-0"><i class="las la-file-alt text-warning mr-2 font-size-20"></i>
                                        {{ $subFolder->files->count() }} Files</p>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif

            <!-- Files in this folder -->
            @if($files->count() > 0)
                <div class="col-lg-12">
                    <div class="card card-block card-stretch card-transparent">
                        <div class="card-header d-flex justify-content-between pb-0">
                            <div class="header-title">
                                <h5 class="card-title">Files</h5>
                            </div>
                        </div>
                    </div>
                </div>

                @foreach($files as $file)
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
                                                        <a class="dropdown-item" href="#"><i
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
                @endforeach
            @endif

            <!-- Empty state -->
            @if($subFolders->count() === 0 && $files->count() === 0)
                <div class="col-12">
                    <div class="card card-block card-stretch card-height">
                        <div class="card-body text-center py-5">
                            <div class="mb-4">
                                <i class="ri-folder-open-line" style="font-size: 100px; color: #ccc;"></i>
                            </div>
                            <h4 class="text-muted">This folder is empty</h4>
                            <p class="text-muted">Upload files or create subfolders to get started.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Share Modal (same as dashboard) -->
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

                        <!-- Email Input Section -->
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

                        <!-- Link Display -->
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

@push('scripts')
    <script>
        $(document).ready(function () {
            let currentFileId = null;

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
                        alert('Please fill in all email addresses');
                        return;
                    }

                    // Check for duplicates
                    const uniqueEmails = [...new Set(emails)];
                    if (uniqueEmails.length !== emails.length) {
                        alert('Please remove duplicate email addresses');
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
                            alert('Share link created successfully!');
                        } else {
                            // Close modal and show success
                            $('#shareModal').modal('hide');
                            alert('File shared successfully!');
                        }
                    },
                    error: function (xhr) {
                        const message = xhr.responseJSON?.message || 'Error sharing file. Please try again.';
                        alert(message);
                    }
                });
            });

            // Copy link to clipboard
            $('#copyLinkBtn').on('click', function () {
                const linkInput = document.getElementById('generatedLink');
                linkInput.select();
                linkInput.setSelectionRange(0, 99999);

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
        });
    </script>
@endpush