<?php

namespace App\Providers;

use App\Models\Purchase;
use App\Observers\PurchaseObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        require_once app_path('Helpers/helper.php');

        // Register morph map for polymorphic relationships
        \Illuminate\Database\Eloquent\Relations\Relation::morphMap([
            'file' => \App\Models\File::class,
            'folder' => \App\Models\Folder::class,
        ]);

        // Register view composer for storage data in sidebar
        View::composer(
            'components.sidebar',
            \App\View\Composers\StorageComposer::class
        );
    }

}
