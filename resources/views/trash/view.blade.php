@extends('layout')
@section('title', 'Trash')
@section('body-page', 'trash-page')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Page Header -->
            <div class="col-lg-12">
                <div class="card card-block card-stretch card-transparent">
                    <div class="card-header d-flex justify-content-between pb-0">
                        <div class="header-title">
                            <h4 class="card-title"><i class="ri-delete-bin-line mr-2"></i>Trash</h4>
                            <p class="text-muted">Items in trash will be permanently deleted after 30 days</p>
                        </div>
                        <div class="card-header-toolbar d-flex align-items-center">
                            <span class="text-muted">{{ $files->count() + $folders->count() }} item(s)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Folders Section -->
            @if($folders->count() > 0)
                <div class="col-lg-12">
                    <div class="card card-block card-stretch card-transparent">
                        <div class="card-header d-flex justify-content-between pb-0">
                            <div class="header-title">
                                <h5 class="card-title">Folders</h5>
                            </div>
                            <div class="card-header-toolbar d-flex align-items-center">
                                <span class="text-muted">{{ $folders->count() }} folder(s)</span>
                            </div>
                        </div>
                    </div>
                </div>

                @foreach($folders as $folder)
                    <div class="col-md-6 col-sm-6 col-lg-3 folder-item" data-folder-id="{{ $folder->id }}">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="folder">
                                        <div class="icon-small bg-secondary rounded mb-4 opacity-50">
                                            <i class="ri-folder-fill"></i>
                                        </div>
                                    </div>
                                    <div class="card-header-toolbar">
                                        <div class="dropdown">
                                            <span class="dropdown-toggle" id="folderMenu{{ $folder->id }}" data-toggle="dropdown">
                                                <i class="ri-more-2-fill"></i>
                                            </span>
                                            <div class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="folderMenu{{ $folder->id }}">
                                                <a class="dropdown-item restore-folder" href="#" data-folder-id="{{ $folder->id }}">
                                                    <i class="ri-restart-line mr-2"></i>Restore
                                                </a>
                                                <a class="dropdown-item force-delete-folder" href="#"
                                                    data-folder-id="{{ $folder->id }}">
                                                    <i class="ri-delete-bin-6-fill mr-2 text-danger"></i>Delete Forever
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="folder">
                                    <h5 class="mb-2 text-truncate text-muted" title="{{ $folder->name }}">{{ $folder->name }}</h5>
                                    <p class="mb-2"><i class="lar la-clock text-secondary mr-2 font-size-20"></i>
                                        Deleted {{ $folder->deleted_at->diffForHumans() }}</p>
                                    @if(Auth::user()->user_type === 'admin')
                                        <p class="mb-0"><i class="las la-user text-secondary mr-2 font-size-20"></i>
                                            {{ $folder->user->name ?? 'Unknown' }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif

            <!-- Files Section -->
            @if($files->count() > 0)
                <div class="col-lg-12">
                    <div class="card card-block card-stretch card-transparent">
                        <div class="card-header d-flex justify-content-between pb-0">
                            <div class="header-title">
                                <h5 class="card-title">Files</h5>
                            </div>
                            <div class="card-header-toolbar d-flex align-items-center">
                                <span class="text-muted">{{ $files->count() }} file(s)</span>
                            </div>
                        </div>
                    </div>
                </div>

                @foreach($files as $file)
                    <div class="col-lg-3 col-md-6 col-sm-6 file-item" data-file-id="{{ $file->id }}">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-body image-thumb">
                                <div>
                                    <div class="mb-4 text-center p-3 rounded iq-thumb opacity-50">
                                        <div class="iq-image-overlay"></div>
                                        <img src="{{ $file->icon }}" class="img-fluid" alt="{{ $file->original_name }}">
                                    </div>
                                    <h6 class="text-truncate text-muted" title="{{ $file->original_name }}">
                                        {{ $file->original_name }}</h6>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="text-muted small mb-0">{{ $file->formatted_size }}</p>
                                        <div class="card-header-toolbar">
                                            <div class="dropdown">
                                                <span class="dropdown-toggle" id="fileMenu{{ $file->id }}" data-toggle="dropdown">
                                                    <i class="ri-more-2-fill"></i>
                                                </span>
                                                <div class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="fileMenu{{ $file->id }}">
                                                    <a class="dropdown-item restore-file" href="#" data-file-id="{{ $file->id }}">
                                                        <i class="ri-restart-line mr-2"></i>Restore
                                                    </a>
                                                    <a class="dropdown-item force-delete-file" href="#"
                                                        data-file-id="{{ $file->id }}">
                                                        <i class="ri-delete-bin-6-fill mr-2 text-danger"></i>Delete Forever
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-muted small mb-0 mt-2">
                                        <i class="lar la-clock mr-1"></i>Deleted {{ $file->deleted_at->diffForHumans() }}
                                    </p>
                                    @if(Auth::user()->user_type === 'admin')
                                        <p class="text-muted small mb-0">
                                            <i class="las la-user mr-1"></i>{{ $file->user->name ?? 'Unknown' }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif

            <!-- Empty State -->
            @if($files->count() === 0 && $folders->count() === 0)
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="ri-delete-bin-line" style="font-size: 4rem; color: #ccc;"></i>
                            <h4 class="mt-3">Trash is empty</h4>
                            <p class="text-muted">Items you delete will appear here</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Restore file
            $('.restore-file').on('click', function (e) {
                e.preventDefault();
                const fileId = $(this).data('file-id');
                const fileCard = $(this).closest('.file-item');

                Swal.fire({
                    title: 'Restore File?',
                    text: 'This file will be restored to its original location',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, restore it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/files/${fileId}/restore`,
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                if (response.success) {
                                    fileCard.fadeOut(300, function () {
                                        $(this).remove();
                                        updateItemCount();
                                    });

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Restored!',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                }
                            },
                            error: function (xhr) {
                                const message = xhr.responseJSON?.message || 'Error restoring file';
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

            // Force delete file
            $('.force-delete-file').on('click', function (e) {
                e.preventDefault();
                const fileId = $(this).data('file-id');
                const fileCard = $(this).closest('.file-item');

                Swal.fire({
                    title: 'Delete Forever?',
                    text: 'This action cannot be undone!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete forever!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/files/${fileId}/force-delete`,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                if (response.success) {
                                    fileCard.fadeOut(300, function () {
                                        $(this).remove();
                                        updateItemCount();
                                    });

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                }
                            },
                            error: function (xhr) {
                                const message = xhr.responseJSON?.message || 'Error deleting file';
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

            // Restore folder
            $('.restore-folder').on('click', function (e) {
                e.preventDefault();
                const folderId = $(this).data('folder-id');
                const folderCard = $(this).closest('.folder-item');

                Swal.fire({
                    title: 'Restore Folder?',
                    text: 'This folder will be restored to its original location',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, restore it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/folders/${folderId}/restore`,
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                if (response.success) {
                                    folderCard.fadeOut(300, function () {
                                        $(this).remove();
                                        updateItemCount();
                                    });

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Restored!',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                }
                            },
                            error: function (xhr) {
                                const message = xhr.responseJSON?.message || 'Error restoring folder';
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

            // Force delete folder
            $('.force-delete-folder').on('click', function (e) {
                e.preventDefault();
                const folderId = $(this).data('folder-id');
                const folderCard = $(this).closest('.folder-item');

                Swal.fire({
                    title: 'Delete Forever?',
                    text: 'This action cannot be undone!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete forever!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/folders/${folderId}/force-delete`,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                if (response.success) {
                                    folderCard.fadeOut(300, function () {
                                        $(this).remove();
                                        updateItemCount();
                                    });

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                }
                            },
                            error: function (xhr) {
                                const message = xhr.responseJSON?.message || 'Error deleting folder';
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

            // Update item count
            function updateItemCount() {
                const fileCount = $('.file-item').length;
                const folderCount = $('.folder-item').length;
                const totalCount = fileCount + folderCount;

                $('.card-header-toolbar span').first().text(`${totalCount} item(s)`);

                // Show empty state if no items left
                if (totalCount === 0) {
                    location.reload();
                }
            }
        });
    </script>
@endpush