<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FolderController extends Controller
{
    /**
     * Get all folders for the authenticated user
     */
    public function index(Request $request)
    {
        $parentId = $request->query('parent_id');

        $query = Folder::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        if ($parentId !== null) {
            $query->where('parent_id', $parentId);
        } else {
            $query->whereNull('parent_id'); // Root folders only
        }

        $folders = $query->get();

        // Add file count for each folder
        $folders->map(function ($folder) {
            $folder->file_count = $folder->files()->count();
            return $folder;
        });

        return response()->json([
            'success' => true,
            'folders' => $folders,
        ]);
    }

    /**
     * Show a specific folder with its contents
     */
    public function show($id)
    {
        $folder = Folder::with(['files', 'children'])->findOrFail($id);

        // Check if user owns this folder
        if ($folder->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this folder');
        }

        // Get sub-folders in this folder
        $subFolders = $folder->children()->with('files')->orderBy('name', 'asc')->get();

        // Get files in this folder
        $files = $folder->files()->orderBy('created_at', 'desc')->get();

        // Add icon information to each file
        $files->map(function ($file) {
            $file->icon = $this->getFileIcon($file->mime_type);
            return $file;
        });

        // Build breadcrumb navigation
        $breadcrumbs = [];
        $currentFolder = $folder->parent;
        while ($currentFolder) {
            array_unshift($breadcrumbs, $currentFolder);
            $currentFolder = $currentFolder->parent;
        }

        // Set current folder ID for sidebar context
        $currentFolderId = $folder->id;

        return view('folders.view', compact('folder', 'subFolders', 'files', 'breadcrumbs', 'currentFolderId'));
    }

    /**
     * Create a new folder
     */
    public function create(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'name' => 'nullable|string|max:255',
                'parent_id' => 'nullable|exists:folders,id'
            ]);

            $user = Auth::user();

            // Set default folder name if empty
            $folderName = $request->name && trim($request->name) !== ''
                ? trim($request->name)
                : 'New Folder';

            $parentId = $request->parent_id;

            // Check if folder with same name already exists in the same parent for this user
            $existingFolder = Folder::where('user_id', $user->id)
                ->where('name', $folderName)
                ->where('parent_id', $parentId)
                ->first();

            if ($existingFolder) {
                return response()->json([
                    'success' => false,
                    'message' => 'A folder with this name already exists in this location.',
                ], 422);
            }

            // Generate folder path
            $path = 'folders/' . $user->id;
            if ($parentId) {
                $parentFolder = Folder::findOrFail($parentId);
                // Ensure user owns the parent folder
                if ($parentFolder->user_id !== $user->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized access to parent folder.',
                    ], 403);
                }
                $path = $parentFolder->path . '/' . Str::slug($folderName);
            } else {
                $path = $path . '/' . Str::slug($folderName);
            }

            // Create folder in database
            $folder = Folder::create([
                'user_id' => $user->id,
                'parent_id' => $parentId,
                'name' => $folderName,
                'path' => $path,
            ]);

            // Create physical directory
            $fullPath = storage_path('app/public/' . $path);
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0755, true);
            }

            return response()->json([
                'success' => true,
                'message' => 'Folder created successfully!',
                'folder' => [
                    'id' => $folder->id,
                    'name' => $folder->name,
                    'path' => $folder->path,
                    'created_at' => $folder->created_at ? $folder->created_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
                ],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Folder creation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create folder: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Rename a folder
     */
    public function rename(Request $request, $id)
    {
        try {
            $folder = Folder::findOrFail($id);

            // Check if user owns the folder or is admin
            if ($folder->user_id !== Auth::id() && Auth::user()->user_type !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $newName = trim($request->name);

            // Check for duplicate folder name in the same parent (excluding this folder)
            $duplicate = Folder::where('user_id', Auth::id())
                ->where('parent_id', $folder->parent_id)
                ->where('name', $newName)
                ->where('id', '!=', $folder->id)
                ->whereNull('deleted_at')
                ->first();

            if ($duplicate) {
                return response()->json([
                    'success' => false,
                    'message' => 'A folder with this name already exists in this location.',
                ], 422);
            }

            $folder->name = $newName;
            $folder->save();

            return response()->json([
                'success' => true,
                'message' => 'Folder renamed successfully.',
                'new_name' => $newName,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Folder rename failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to rename folder: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get appropriate icon for file type
     */
    private function getFileIcon($mimeType)
    {
        // Image files
        if (strpos($mimeType, 'image/') === 0) {
            return asset('images/layouts/page-1/jpg.png');
        }

        // PDF files
        if ($mimeType === 'application/pdf') {
            return asset('images/layouts/page-1/pdf.png');
        }

        // Word documents
        if (in_array($mimeType, ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])) {
            return asset('images/layouts/page-1/doc.png');
        }

        // Excel spreadsheets
        if (in_array($mimeType, ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])) {
            return asset('images/layouts/page-1/xlsx.png');
        }

        // PowerPoint presentations
        if (in_array($mimeType, ['application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation'])) {
            return asset('images/layouts/page-1/ppt.png');
        }

        // ZIP files
        if (in_array($mimeType, ['application/zip', 'application/x-zip-compressed'])) {
            return asset('images/layouts/page-1/zip.png');
        }

        // Video files
        if (strpos($mimeType, 'video/') === 0) {
            return asset('images/layouts/page-1/video.png');
        }

        // Default icon for unknown types
        return asset('images/layouts/page-1/file.png');
    }

    /**
     * Soft delete a folder
     */
    public function destroy($id)
    {
        try {
            $folder = Folder::findOrFail($id);

            // Check if user owns the folder or is admin
            if ($folder->user_id !== Auth::id() && Auth::user()->user_type !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            // Soft delete the folder
            $folder->delete();

            return response()->json([
                'success' => true,
                'message' => 'Folder moved to trash successfully',
            ]);

        } catch (\Exception $e) {
            \Log::error('Folder delete failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete folder: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Restore a soft-deleted folder
     */
    public function restore($id)
    {
        try {
            $folder = Folder::withTrashed()->findOrFail($id);

            // Check if user owns the folder or is admin
            if ($folder->user_id !== Auth::id() && Auth::user()->user_type !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            // Restore the folder
            $folder->restore();

            return response()->json([
                'success' => true,
                'message' => 'Folder restored successfully',
            ]);

        } catch (\Exception $e) {
            \Log::error('Folder restore failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore folder: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Permanently delete a folder
     */
    public function forceDelete($id)
    {
        try {
            $folder = Folder::withTrashed()->findOrFail($id);

            // Check if user owns the folder or is admin
            if ($folder->user_id !== Auth::id() && Auth::user()->user_type !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            // Permanently delete the database record
            $folder->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Folder permanently deleted',
            ]);

        } catch (\Exception $e) {
            \Log::error('Folder force delete failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete folder: ' . $e->getMessage(),
            ], 500);
        }
    }
}
