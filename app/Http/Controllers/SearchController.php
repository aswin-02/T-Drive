<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    /**
     * Full search results page (GET /search?q=...)
     */
    public function index(Request $request)
    {
        $query = trim($request->get('q', ''));

        $files = collect();
        $folders = collect();

        if ($query !== '') {
            $files = File::where('user_id', Auth::id())
                ->where('original_name', 'LIKE', "%{$query}%")
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($file) {
                    $file->icon = $this->getFileIcon($file->mime_type);
                    return $file;
                });

            $folders = Folder::where('user_id', Auth::id())
                ->where('name', 'LIKE', "%{$query}%")
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('search.results', compact('query', 'files', 'folders'));
    }

    /**
     * Live AJAX suggestions (GET /search/suggest?q=...)
     * Returns JSON with up to 5 files + 5 folders.
     */
    public function suggest(Request $request)
    {
        $query = trim($request->get('q', ''));

        if (strlen($query) < 1) {
            return response()->json(['files' => [], 'folders' => []]);
        }

        $files = File::where('user_id', Auth::id())
            ->where('original_name', 'LIKE', "%{$query}%")
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'original_name', 'mime_type', 'size'])
            ->map(function ($file) {
                $bytes = $file->size;
                $units = ['B', 'KB', 'MB', 'GB'];
                for ($i = 0; $bytes > 1024 && $i < 3; $i++) {
                    $bytes /= 1024;
                }
                return [
                    'id' => $file->id,
                    'name' => $file->original_name,
                    'size' => round($bytes, 1) . ' ' . $units[$i],
                    'view_url' => route('files.view', $file->id),
                    'download_url' => route('files.download', $file->id),
                ];
            });

        $folders = Folder::where('user_id', Auth::id())
            ->where('name', 'LIKE', "%{$query}%")
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'name'])
            ->map(function ($folder) {
                return [
                    'id' => $folder->id,
                    'name' => $folder->name,
                    'url' => route('folders.show', $folder->id),
                ];
            });

        return response()->json([
            'files' => $files,
            'folders' => $folders,
            'search_url' => route('search.index', ['q' => request('q')]),
        ]);
    }

    /**
     * Return the appropriate icon asset for a given MIME type.
     */
    private function getFileIcon(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return asset('images/layouts/page-1/jpg.png');
        }
        if ($mimeType === 'application/pdf') {
            return asset('images/layouts/page-1/pdf.png');
        }
        if (
            in_array($mimeType, [
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ])
        ) {
            return asset('images/layouts/page-1/doc.png');
        }
        if (
            in_array($mimeType, [
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])
        ) {
            return asset('images/layouts/page-1/xlsx.png');
        }
        if (
            in_array($mimeType, [
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            ])
        ) {
            return asset('images/layouts/page-1/ppt.png');
        }
        if (in_array($mimeType, ['application/zip', 'application/x-zip-compressed'])) {
            return asset('images/layouts/page-1/zip.png');
        }
        return asset('images/layouts/page-1/file.png');
    }
}
