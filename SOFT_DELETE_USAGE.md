# Soft Delete Functionality - Usage Guide

## Overview

The soft delete functionality has been implemented for files in the T-Drive application. This allows files to be moved to trash instead of being permanently deleted, giving users the ability to restore them if needed.

## Features Implemented

### 1. Database Migration

- Added `deleted_at` column to the `files` table
- Migration file: `database/migrations/2026_02_09_071205_add_soft_deletes_to_files_table.php`

### 2. Model Updates

- Added `SoftDeletes` trait to the `File` model
- Files with a `deleted_at` timestamp are considered "trashed"

### 3. Controller Methods

#### Soft Delete (Move to Trash)

```php
public function destroy($id)
```

- **Route**: `DELETE /files/{id}`
- **Route Name**: `files.destroy`
- **Description**: Moves a file to trash (soft delete)
- **Authorization**: User must own the file or be an admin
- **Response**: JSON with success status and message

#### Restore File

```php
public function restore($id)
```

- **Route**: `POST /files/{id}/restore`
- **Route Name**: `files.restore`
- **Description**: Restores a trashed file
- **Authorization**: User must own the file or be an admin
- **Response**: JSON with success status and message

#### Force Delete (Permanent)

```php
public function forceDelete($id)
```

- **Route**: `DELETE /files/{id}/force-delete`
- **Route Name**: `files.force-delete`
- **Description**: Permanently deletes a file (removes from database and storage)
- **Authorization**: User must own the file or be an admin
- **Response**: JSON with success status and message

#### Get Trashed Files

```php
public function trashed()
```

- **Route**: `GET /files/trash/list`
- **Route Name**: `files.trashed`
- **Description**: Returns all trashed files for the authenticated user (admins see all trashed files)
- **Response**: JSON with array of trashed files

## Frontend Implementation

### Delete Button

The delete button in the dashboard has been updated with:

- Class: `delete-file`
- Data attribute: `data-file-id="{{ $file->id }}"`

### JavaScript Handler

A jQuery event handler has been added to handle file deletion:

```javascript
$(".delete-file").on("click", function (e) {
    e.preventDefault();
    const fileId = $(this).data("file-id");

    if (confirm("Are you sure you want to move this file to trash?")) {
        $.ajax({
            url: `/files/${fileId}`,
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                // Handle success - remove file card from UI
            },
            error: function (xhr) {
                // Handle error
            },
        });
    }
});
```

## Usage Examples

### 1. Delete a File (Move to Trash)

```javascript
// Using jQuery AJAX
$.ajax({
    url: "/files/123",
    method: "DELETE",
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    success: function (response) {
        console.log(response.message); // "File moved to trash successfully"
    },
});
```

### 2. Restore a File

```javascript
$.ajax({
    url: "/files/123/restore",
    method: "POST",
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    success: function (response) {
        console.log(response.message); // "File restored successfully"
    },
});
```

### 3. Permanently Delete a File

```javascript
$.ajax({
    url: "/files/123/force-delete",
    method: "DELETE",
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    success: function (response) {
        console.log(response.message); // "File permanently deleted"
    },
});
```

### 4. Get All Trashed Files

```javascript
$.ajax({
    url: "/files/trash/list",
    method: "GET",
    success: function (response) {
        console.log(response.files); // Array of trashed files
    },
});
```

## Next Steps (Optional Enhancements)

### 1. Create a Trash View Page

Create a dedicated page to view all trashed files with options to restore or permanently delete them.

### 2. Auto-Delete After X Days

Implement a scheduled task to automatically permanently delete files that have been in trash for more than X days:

```php
// In app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        File::onlyTrashed()
            ->where('deleted_at', '<', now()->subDays(30))
            ->each(function ($file) {
                Storage::disk('public')->delete($file->path);
                $file->forceDelete();
            });
    })->daily();
}
```

### 3. Bulk Operations

Add endpoints for bulk delete, restore, and force delete operations:

```php
public function bulkDestroy(Request $request)
{
    $fileIds = $request->input('file_ids');
    File::whereIn('id', $fileIds)->delete();
    return response()->json(['success' => true]);
}
```

## Testing

### Test Soft Delete

1. Upload a file
2. Click the delete button on the file
3. Confirm the deletion
4. File should disappear from the dashboard
5. Check database: `deleted_at` column should have a timestamp

### Test Restore

1. Use the trashed files endpoint to get a trashed file ID
2. Call the restore endpoint
3. File should reappear in the dashboard

### Test Force Delete

1. Soft delete a file first
2. Call the force delete endpoint
3. File should be permanently removed from database and storage

## Security Notes

- All endpoints check user authorization (owner or admin)
- CSRF token is required for all POST/DELETE requests
- File paths are validated before deletion from storage
- Proper error handling is implemented for all operations
