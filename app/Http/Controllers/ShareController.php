<?php

namespace App\Http\Controllers;

use App\Models\Share;
use App\Models\ShareUser;
use App\Models\File;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ShareController extends Controller
{
    /**
     * Store a newly created share
     */
    public function store(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'shareable_id' => 'required_without_all:file_id,folder_id|integer|nullable',
                'file_id' => 'required_without_all:shareable_id,folder_id|integer|nullable',
                'folder_id' => 'required_without_all:shareable_id,file_id|integer|nullable',
                'shareable_type' => 'sometimes|in:file,folder',
                'access_type' => 'required|in:link,email',
                'permission' => 'required|in:view,download,edit',
                'expires_at' => 'nullable|date|after:now',
                'emails' => 'nullable|array',
                'emails.*' => 'email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $validator->validated();

            // Determine shareable_id and shareable_type from file_id or folder_id
            if (isset($data['file_id'])) {
                $data['shareable_id'] = $data['file_id'];
                $data['shareable_type'] = 'file';
            } elseif (isset($data['folder_id'])) {
                $data['shareable_id'] = $data['folder_id'];
                $data['shareable_type'] = 'folder';
            }

            // Validate email requirement for email sharing
            if ($data['access_type'] === 'email' && empty($data['emails'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email addresses are required for email sharing',
                ], 422);
            }

            // Get the shareable model
            $shareableModel = $data['shareable_type'] === 'file' ? File::class : Folder::class;
            $shareable = $shareableModel::findOrFail($data['shareable_id']);

            // Check if user owns the file/folder
            if ($shareable->user_id !== Auth::id() && Auth::user()->user_type !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            // Create or update the share
            // For email shares: check if this exact file/folder is already shared
            // with any of the provided emails — update instead of duplicating.
            if ($data['access_type'] === 'email' && !empty($data['emails'])) {
                foreach ($data['emails'] as $email) {
                    // Find existing email share for this shareable + this email
                    $existingShareUser = ShareUser::whereHas('share', function ($q) use ($data) {
                        $q->where('shareable_type', $data['shareable_type'])
                            ->where('shareable_id', $data['shareable_id'])
                            ->where('access_type', 'email');
                    })->where('email', $email)->first();

                    if ($existingShareUser) {
                        // Update permission & expiry on the existing share
                        $existingShareUser->share->update([
                            'permission' => $data['permission'],
                            'expires_at' => $data['expires_at'] ?? null,
                        ]);
                        $share = $existingShareUser->share; // use the updated share for response
                    } else {
                        // No existing share — create a fresh one
                        $share = Share::create([
                            'owner_id' => Auth::id(),
                            'shareable_type' => $data['shareable_type'],
                            'shareable_id' => $data['shareable_id'],
                            'access_type' => 'email',
                            'permission' => $data['permission'],
                            'expires_at' => $data['expires_at'] ?? null,
                            'token' => null,
                        ]);

                        ShareUser::create([
                            'share_id' => $share->id,
                            'email' => $email,
                        ]);
                    }
                }
            } else {
                // Link share — always create new (links are unique by design)
                $share = Share::create([
                    'owner_id' => Auth::id(),
                    'shareable_type' => $data['shareable_type'],
                    'shareable_id' => $data['shareable_id'],
                    'access_type' => $data['access_type'],
                    'permission' => $data['permission'],
                    'expires_at' => $data['expires_at'] ?? null,
                    'token' => Str::random(32),
                ]);
            }

            // Prepare response
            $response = [
                'success' => true,
                'message' => 'Share created successfully',
                'share' => [
                    'id' => $share->id,
                    'access_type' => $share->access_type,
                    'permission' => $share->permission,
                    'expires_at' => $share->expires_at?->format('Y-m-d H:i:s'),
                ],
            ];

            // Add share URL for link type
            if ($share->access_type === 'link') {
                $response['share_url'] = route('shares.view', $share->token);
                $response['share']['url'] = route('shares.view', $share->token);
                $response['share']['token'] = $share->token;
            }

            // Add emails for email type
            if ($share->access_type === 'email') {
                $response['share']['emails'] = $data['emails'];
            }

            return response()->json($response);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'File or folder not found',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Share creation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create share: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * View a shared file/folder by token
     */
    public function view($token)
    {
        try {
            $share = Share::with(['shareable', 'owner', 'shareUsers'])
                ->byToken($token)
                ->active()
                ->firstOrFail();

            // Check if expired
            if ($share->isExpired()) {
                abort(403, 'This share link has expired');
            }

            $shareable = $share->shareable;

            if (!$shareable) {
                abort(404, 'Shared item not found');
            }

            // Check access type
            if ($share->access_type === 'email') {
                // Email-based share - require authentication
                if (!Auth::check()) {
                    // Redirect to login with return URL
                    return redirect()->route('login')
                        ->with('message', 'Please login to access this shared file')
                        ->with('return_url', route('shares.view', $token));
                }

                // Check if user's email is in the share_users list
                $userEmail = Auth::user()->email;
                $hasAccess = $share->shareUsers->contains('email', $userEmail);

                if (!$hasAccess && $share->owner_id !== Auth::id()) {
                    abort(403, 'You do not have permission to access this shared file');
                }
            }

            // Determine view based on shareable type
            if ($share->shareable_type === 'file') {
                return $this->viewSharedFile($share, $shareable);
            } else {
                return $this->viewSharedFolder($share, $shareable);
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Share link not found or expired');
        } catch (\Exception $e) {
            \Log::error('Share view failed: ' . $e->getMessage());
            abort(500, 'Failed to load shared content');
        }
    }

    /**
     * View a shared file
     */
    private function viewSharedFile($share, $file)
    {
        $isAuthenticated = Auth::check();
        return view('shares.file', compact('share', 'file', 'isAuthenticated'));
    }

    /**
     * View a shared folder
     */
    private function viewSharedFolder($share, $folder)
    {
        // Get folder contents
        $files = File::where('folder_id', $folder->id)->get();
        $subfolders = Folder::where('parent_id', $folder->id)->get();

        $isAuthenticated = Auth::check();
        return view('shares.folder', compact('share', 'folder', 'files', 'subfolders', 'isAuthenticated'));
    }

    /**
     * Download a shared file
     */
    public function download($token)
    {
        try {
            $share = Share::with(['shareable', 'shareUsers'])
                ->byToken($token)
                ->active()
                ->firstOrFail();

            // Check if expired
            if ($share->isExpired()) {
                abort(403, 'This share link has expired');
            }

            // Check access type
            if ($share->access_type === 'email') {
                // Email-based share - require authentication
                if (!Auth::check()) {
                    abort(403, 'Please login to download this file');
                }

                // Check if user's email is in the share_users list
                $userEmail = Auth::user()->email;
                $hasAccess = $share->shareUsers->contains('email', $userEmail);

                if (!$hasAccess && $share->owner_id !== Auth::id()) {
                    abort(403, 'You do not have permission to download this file');
                }
            }

            // Check if download is allowed
            if (!in_array($share->permission, ['download', 'edit'])) {
                abort(403, 'Download not allowed for this share');
            }

            $file = $share->shareable;

            if (!$file || $share->shareable_type !== 'file') {
                abort(404, 'File not found');
            }

            $filePath = storage_path('app/public/' . $file->path);

            if (!file_exists($filePath)) {
                abort(404, 'File not found');
            }

            return response()->download($filePath, $file->original_name);

        } catch (\Exception $e) {
            \Log::error('Share download failed: ' . $e->getMessage());
            abort(404, 'File not found');
        }
    }

    /**
     * Convert a shared PPT/PPTX to PDF via LibreOffice and stream inline.
     * Public — accessible via share token (no auth needed for link shares).
     */
    public function previewPdf($token)
    {
        try {
            $share = Share::with(['shareable', 'shareUsers'])
                ->byToken($token)
                ->active()
                ->firstOrFail();

            if ($share->isExpired()) {
                abort(403, 'This share link has expired');
            }

            // Email-based share needs auth check
            if ($share->access_type === 'email') {
                if (!Auth::check()) {
                    abort(403, 'Please login to access this shared file');
                }
                $hasAccess = $share->shareUsers->contains('email', Auth::user()->email);
                if (!$hasAccess && $share->owner_id !== Auth::id()) {
                    abort(403, 'You do not have permission to access this file');
                }
            }

            $file = $share->shareable;
            if (!$file || $share->shareable_type !== 'file') {
                abort(404, 'File not found');
            }

            $sourcePath = storage_path('app/public/' . $file->path);
            if (!file_exists($sourcePath)) {
                abort(404, 'File not found on disk');
            }

            // Reuse same cache as the authenticated preview
            $cacheKey = md5($file->path . filemtime($sourcePath));
            $previewDir = storage_path('app/public/previews');
            $pdfPath = $previewDir . '/' . $cacheKey . '.pdf';

            if (!file_exists($pdfPath)) {
                if (!is_dir($previewDir)) {
                    mkdir($previewDir, 0755, true);
                }

                $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
                if ($isWindows) {
                    $libreoffice = trim(shell_exec('where soffice 2>NUL') ?? '');
                    if (empty($libreoffice)) {
                        foreach ([
                            'C:\\Program Files\\LibreOffice\\program\\soffice.exe',
                            'C:\\Program Files (x86)\\LibreOffice\\program\\soffice.exe'
                        ] as $c) {
                            if (file_exists($c)) {
                                $libreoffice = escapeshellarg($c);
                                break;
                            }
                        }
                    } else {
                        $libreoffice = escapeshellarg(strtok($libreoffice, "\n"));
                    }
                } else {
                    $libreoffice = trim(shell_exec('which libreoffice 2>/dev/null || which soffice 2>/dev/null') ?? '');
                }
                if (empty($libreoffice)) {
                    abort(500, 'LibreOffice is not installed on this server.');
                }

                $cmd = $libreoffice
                    . ' --headless --norestore --nofirststartwizard'
                    . ' --convert-to pdf ' . escapeshellarg($sourcePath)
                    . ' --outdir ' . escapeshellarg($previewDir) . ' 2>&1';

                exec($cmd, $output, $exitCode);

                $generatedPdf = $previewDir . '/' . pathinfo($file->stored_name, PATHINFO_FILENAME) . '.pdf';

                if ($exitCode !== 0 || !file_exists($generatedPdf)) {
                    \Log::error('LibreOffice share conversion failed', [
                        'cmd' => $cmd,
                        'output' => implode("\n", $output),
                        'exit' => $exitCode,
                    ]);
                    abort(500, 'Failed to convert presentation to PDF.');
                }

                rename($generatedPdf, $pdfPath);
            }

            return response()->file($pdfPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'
                    . pathinfo($file->original_name, PATHINFO_FILENAME) . '.pdf"',
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Share link not found or expired');
        } catch (\Exception $e) {
            \Log::error('Share PPT preview failed: ' . $e->getMessage());
            abort(500, $e->getMessage());
        }
    }

    /**
     * Delete a share
     */
    public function destroy($id)
    {
        try {
            $share = Share::findOrFail($id);

            // Check if user owns the share
            if ($share->owner_id !== Auth::id() && Auth::user()->user_type !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            $share->delete();

            return response()->json([
                'success' => true,
                'message' => 'Share deleted successfully',
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Share not found',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Share deletion failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete share',
            ], 500);
        }
    }

    /**
     * List all shares for the authenticated user
     */
    public function index()
    {
        $shares = Share::with(['shareable', 'shareUsers'])
            ->where('owner_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('shares.index', compact('shares'));
    }

    /**
     * Show files shared with the authenticated user
     */
    public function sharedWithMe()
    {
        $userEmail = Auth::user()->email;

        // Get all shares where user's email is in share_users
        $shareUserIds = ShareUser::where('email', $userEmail)
            ->pluck('share_id')
            ->toArray();

        $shares = Share::with(['shareable', 'owner'])
            ->whereIn('id', $shareUserIds)
            ->active()
            ->orderBy('updated_at', 'desc') // latest first so unique() keeps most recent
            ->get()
            ->filter(fn($share) => $share->shareable !== null)       // skip deleted shareables
            ->unique(fn($share) => $share->shareable_type . ':' . $share->shareable_id); // one per file/folder

        return view('shares.shared-with-me', compact('shares'));
    }
}
