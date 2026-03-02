<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with files and folders
     */
    public function index()
    {
        // Get files in root (no folder)
        $files = File::where('user_id', Auth::id())
            ->whereNull('folder_id')
            ->orderBy('created_at', 'desc')
            ->get();

        // Add icon information to each file
        $files->map(function ($file) {
            $file->icon = $this->getFileIcon($file->mime_type);
            return $file;
        });

        // Get folders in root (no parent)
        $folders = Folder::where('user_id', Auth::id())
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate storage usage
        $maxStorageBytes = 5 * 1024 * 1024 * 1024; // 5 GB in bytes
        $usedStorageBytes = File::where('user_id', Auth::id())->sum('size');

        // Convert to GB for display
        $usedStorageGB = round($usedStorageBytes / (1024 * 1024 * 1024), 2);
        $maxStorageGB = 5;
        $freeStorageGB = round(($maxStorageBytes - $usedStorageBytes) / (1024 * 1024 * 1024), 2);
        $storagePercentage = $usedStorageBytes > 0 ? round(($usedStorageBytes / $maxStorageBytes) * 100, 1) : 0;

        // Storage info for view
        $storage = [
            'used_gb' => $usedStorageGB,
            'max_gb' => $maxStorageGB,
            'free_gb' => $freeStorageGB,
            'percentage' => $storagePercentage,
        ];

        // Set current folder ID as null for root/dashboard
        $currentFolderId = null;

        return view('dashboard', compact('files', 'folders', 'storage', 'currentFolderId'));
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
}
