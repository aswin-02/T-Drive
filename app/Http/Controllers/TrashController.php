<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrashController extends Controller
{
    /**
     * Display the trash view with deleted files and folders
     */
    public function index()
    {
        $user = Auth::user();

        // Get trashed files
        if ($user->user_type === 'admin') {
            $files = File::onlyTrashed()->with('user')->orderBy('deleted_at', 'desc')->get();
            $folders = Folder::onlyTrashed()->with('user')->orderBy('deleted_at', 'desc')->get();
        } else {
            $files = File::onlyTrashed()->where('user_id', $user->id)->orderBy('deleted_at', 'desc')->get();
            $folders = Folder::onlyTrashed()->where('user_id', $user->id)->orderBy('deleted_at', 'desc')->get();
        }

        // Add icon information to each file
        $files->map(function ($file) {
            $file->icon = $this->getFileIcon($file->mime_type);
            return $file;
        });

        return view('trash.view', compact('files', 'folders'));
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

        // Default icon for unknown types
        return asset('images/layouts/page-1/file.png');
    }
}
