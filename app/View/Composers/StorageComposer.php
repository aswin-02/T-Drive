<?php

namespace App\View\Composers;

use App\Models\File;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class StorageComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        if (Auth::check()) {
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

            $view->with('storage', $storage);
        }
    }
}
