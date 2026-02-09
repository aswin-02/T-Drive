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

            // Create the share
            $share = Share::create([
                'owner_id' => Auth::id(),
                'shareable_type' => $data['shareable_type'],
                'shareable_id' => $data['shareable_id'],
                'access_type' => $data['access_type'],
                'permission' => $data['permission'],
                'expires_at' => $data['expires_at'] ?? null,
                'token' => $data['access_type'] === 'link' ? Str::random(32) : null,
            ]);

            // If email sharing, create share_users records
            if ($data['access_type'] === 'email' && !empty($data['emails'])) {
                foreach ($data['emails'] as $email) {
                    ShareUser::create([
                        'share_id' => $share->id,
                        'email' => $email,
                    ]);
                }
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
            ->orderBy('created_at', 'desc')
            ->get();

        return view('shares.shared-with-me', compact('shares'));
    }
}
