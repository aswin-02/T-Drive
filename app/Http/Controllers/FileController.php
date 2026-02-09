<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    /**
     * Upload files
     */
    public function upload(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'files' => 'required|array',
                'files.*' => 'required|file|max:102400|mimes:jpg,jpeg,png,gif,svg,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,zip', // max 100MB
            ]);

            $uploadedFiles = [];
            $user = Auth::user();

            foreach ($request->file('files') as $uploadedFile) {
                // Generate unique filename
                $originalName = $uploadedFile->getClientOriginalName();
                $extension = $uploadedFile->getClientOriginalExtension();
                $storedName = Str::uuid() . '.' . $extension;

                // Store file in user's directory
                $path = $uploadedFile->storeAs(
                    'files/' . $user->id,
                    $storedName,
                    'public'
                );

                // Create database record
                $file = File::create([
                    'user_id' => $user->id,
                    'folder_id' => $request->folder_id ?? null,
                    'original_name' => $originalName,
                    'stored_name' => $storedName,
                    'path' => $path,
                    'size' => $uploadedFile->getSize(),
                    'mime_type' => $uploadedFile->getMimeType(),
                ]);

                $uploadedFiles[] = [
                    'id' => $file->id,
                    'name' => $file->original_name,
                    'size' => $file->formatted_size,
                    'type' => $file->mime_type,
                ];
            }

            return response()->json([
                'success' => true,
                'message' => count($uploadedFiles) . ' file(s) uploaded successfully',
                'files' => $uploadedFiles,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('File upload failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'File upload failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * View a file
     */
    public function view($id)
    {
        try {
            $file = File::findOrFail($id);

            // Check if user owns the file or is admin
            if ($file->user_id !== Auth::id() && Auth::user()->user_type !== 'admin') {
                abort(403, 'Unauthorized access');
            }

            $filePath = storage_path('app/public/' . $file->path);

            if (!file_exists($filePath)) {
                abort(404, 'File not found');
            }

            // Get file extension and determine viewer type
            $extension = strtolower($file->extension);
            $fileUrl = Storage::url($file->path);

            // Determine viewer type based on extension
            $viewerType = $this->getViewerType($extension);

            // Record recent view
            if (Auth::check()) {
                \App\Models\RecentView::updateOrCreate(
                    [
                        'user_id' => Auth::id(),
                        'file_id' => $file->id
                    ],
                    [
                        'viewed_at' => now()
                    ]
                );
            }

            return view('files.view', compact('file', 'fileUrl', 'viewerType', 'extension'));
        } catch (\Exception $e) {
            \Log::error('File view failed: ' . $e->getMessage());
            abort(404, 'File not found');
        }
    }

    /**
     * Determine viewer type based on file extension
     */
    private function getViewerType($extension)
    {
        $viewerTypes = [
            'pdf' => 'pdf',
            'doc' => 'doc',
            'docx' => 'doc',
            'xls' => 'spreadsheet',
            'xlsx' => 'spreadsheet',
            'csv' => 'spreadsheet',
            'ppt' => 'presentation',
            'pptx' => 'presentation',
            'jpg' => 'image',
            'jpeg' => 'image',
            'png' => 'image',
            'gif' => 'image',
            'svg' => 'image',
            'webp' => 'image',
            'zip' => 'archive',
            'rar' => 'archive',
            '7z' => 'archive',
            'txt' => 'text',
        ];

        return $viewerTypes[$extension] ?? 'unknown';
    }

    /**
     * Download a file
     */
    public function download($id)
    {
        try {
            $file = File::findOrFail($id);

            // Check if user owns the file or is admin
            if ($file->user_id !== Auth::id() && Auth::user()->user_type !== 'admin') {
                abort(403, 'Unauthorized access');
            }

            $filePath = storage_path('app/public/' . $file->path);

            if (!file_exists($filePath)) {
                abort(404, 'File not found');
            }

            return response()->download($filePath, $file->original_name);
        } catch (\Exception $e) {
            \Log::error('File download failed: ' . $e->getMessage());
            abort(404, 'File not found');
        }
    }

    /**
     * Soft delete a file
     */
    public function destroy($id)
    {
        try {
            $file = File::findOrFail($id);

            // Check if user owns the file or is admin
            if ($file->user_id !== Auth::id() && Auth::user()->user_type !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            // Soft delete the file
            $file->delete();

            return response()->json([
                'success' => true,
                'message' => 'File moved to trash successfully',
            ]);

        } catch (\Exception $e) {
            \Log::error('File delete failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete file: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Restore a soft-deleted file
     */
    public function restore($id)
    {
        try {
            $file = File::withTrashed()->findOrFail($id);

            // Check if user owns the file or is admin
            if ($file->user_id !== Auth::id() && Auth::user()->user_type !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            // Restore the file
            $file->restore();

            return response()->json([
                'success' => true,
                'message' => 'File restored successfully',
            ]);

        } catch (\Exception $e) {
            \Log::error('File restore failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore file: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Permanently delete a file
     */
    public function forceDelete($id)
    {
        try {
            $file = File::withTrashed()->findOrFail($id);

            // Check if user owns the file or is admin
            if ($file->user_id !== Auth::id() && Auth::user()->user_type !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            // Delete the physical file from storage
            if (Storage::disk('public')->exists($file->path)) {
                Storage::disk('public')->delete($file->path);
            }

            // Permanently delete the database record
            $file->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'File permanently deleted',
            ]);

        } catch (\Exception $e) {
            \Log::error('File force delete failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete file: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all trashed files for the authenticated user
     */
    public function trashed()
    {
        try {
            $user = Auth::user();

            // If admin, show all trashed files, otherwise only user's files
            if ($user->user_type === 'admin') {
                $files = File::onlyTrashed()->with('user')->get();
            } else {
                $files = File::onlyTrashed()->where('user_id', $user->id)->get();
            }

            return response()->json([
                'success' => true,
                'files' => $files,
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to fetch trashed files: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch trashed files: ' . $e->getMessage(),
            ], 500);
        }
    }
}

