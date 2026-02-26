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
        $allowedExtensions = ['pdf', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'jpg', 'png'];

        // Expected MIME types per extension (double-check to block renamed files)
        $allowedMimes = [
            'pdf' => ['application/pdf'],
            'xls' => ['application/vnd.ms-excel', 'application/msexcel'],
            'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            'ppt' => ['application/vnd.ms-powerpoint'],
            'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation'],
            'zip' => ['application/zip', 'application/x-zip-compressed', 'multipart/x-zip'],
            'jpg' => ['image/jpeg'],
            'png' => ['image/png'],
        ];

        try {
            // Basic validation
            $request->validate([
                'files' => 'required|array',
                'files.*' => 'required|file|max:102400', // max 100 MB
            ]);

            // Strict extension + MIME check on every file
            foreach ($request->file('files') as $index => $uploadedFile) {
                $ext = strtolower($uploadedFile->getClientOriginalExtension());
                $mime = strtolower($uploadedFile->getMimeType());

                if (!in_array($ext, $allowedExtensions)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => [
                            "files.$index" => [
                                "'." . $ext . "' is not allowed. Accepted formats: "
                                . implode(', ', array_map('strtoupper', $allowedExtensions))
                            ],
                        ],
                    ], 422);
                }

                // MIME type must match the declared extension
                if (!in_array($mime, $allowedMimes[$ext])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => [
                            "files.$index" => [
                                "The file content does not match its extension '." . $ext . "'. Upload rejected."
                            ],
                        ],
                    ], 422);
                }
            }

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
     * Locate the LibreOffice executable cross-platform.
     * Works on Linux, macOS, and Windows.
     */
    private static function findLibreOffice(): string
    {
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        if ($isWindows) {
            // 1. Try PATH via 'where' (Windows equivalent of 'which')
            $path = trim(shell_exec('where soffice 2>NUL') ?? '');
            if (!empty($path)) {
                return escapeshellarg(strtok($path, "\n")); // first result only
            }
            // 2. Check common Windows install paths
            $candidates = [
                'C:\\Program Files\\LibreOffice\\program\\soffice.exe',
                'C:\\Program Files (x86)\\LibreOffice\\program\\soffice.exe',
            ];
            foreach ($candidates as $c) {
                if (file_exists($c)) {
                    return escapeshellarg($c);
                }
            }
            return '';
        }

        // Linux / macOS — use 'which'
        $path = trim(shell_exec('which libreoffice 2>/dev/null || which soffice 2>/dev/null') ?? '');
        return $path; // already a valid shell path, no escaping needed here
    }

    /**
     * Convert PPT/PPTX to PDF via LibreOffice and stream to browser.
     * The converted PDF is cached in storage/app/public/previews/{hash}.pdf
     * so subsequent views skip the conversion step.
     */
    public function previewAsPdf($id)
    {
        try {
            $file = File::findOrFail($id);

            // Auth check
            if ($file->user_id !== Auth::id() && Auth::user()->user_type !== 'admin') {
                abort(403, 'Unauthorized access');
            }

            $sourcePath = storage_path('app/public/' . $file->path);

            if (!file_exists($sourcePath)) {
                abort(404, 'File not found');
            }

            // Cache key: hash of file path + modification time so re-uploads invalidate cache
            $cacheKey = md5($file->path . filemtime($sourcePath));
            $previewDir = storage_path('app/public/previews');
            $pdfPath = $previewDir . '/' . $cacheKey . '.pdf';

            // Only convert if cached PDF doesn't already exist
            if (!file_exists($pdfPath)) {
                if (!is_dir($previewDir)) {
                    mkdir($previewDir, 0755, true);
                }

                // Cross-platform LibreOffice detection
                $libreoffice = self::findLibreOffice();
                if (empty($libreoffice)) {
                    abort(500, 'LibreOffice is not installed on this server.');
                }

                $escapedSource = escapeshellarg($sourcePath);
                $escapedDir = escapeshellarg($previewDir);
                $cmd = "$libreoffice --headless --norestore --nofirststartwizard"
                    . " --convert-to pdf $escapedSource --outdir $escapedDir 2>&1";

                exec($cmd, $output, $exitCode);

                // LibreOffice names the output after the source filename
                $generatedPdf = $previewDir . '/' . pathinfo($file->stored_name, PATHINFO_FILENAME) . '.pdf';

                if ($exitCode !== 0 || !file_exists($generatedPdf)) {
                    \Log::error('LibreOffice conversion failed', [
                        'cmd' => $cmd,
                        'output' => implode("\n", $output),
                        'exit' => $exitCode,
                    ]);
                    abort(500, 'Failed to convert presentation to PDF.');
                }

                // Rename to cache key so we can reuse it
                rename($generatedPdf, $pdfPath);
            }

            // Stream the cached PDF directly to the browser (inline)
            return response()->file($pdfPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . pathinfo($file->original_name, PATHINFO_FILENAME) . '.pdf"',
            ]);

        } catch (\Exception $e) {
            \Log::error('PPT preview failed: ' . $e->getMessage());
            abort(500, $e->getMessage());
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
     * Rename a file
     */
    public function rename(Request $request, $id)
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

            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $newName = trim($request->name);

            // Check for duplicate name in the same folder (excluding this file)
            $duplicate = File::where('user_id', Auth::id())
                ->where('folder_id', $file->folder_id)
                ->where('original_name', $newName)
                ->where('id', '!=', $file->id)
                ->whereNull('deleted_at')
                ->first();

            if ($duplicate) {
                return response()->json([
                    'success' => false,
                    'message' => 'A file with this name already exists in this location.',
                ], 422);
            }

            $file->original_name = $newName;
            $file->save();

            return response()->json([
                'success' => true,
                'message' => 'File renamed successfully.',
                'new_name' => $newName,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('File rename failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to rename file: ' . $e->getMessage(),
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

