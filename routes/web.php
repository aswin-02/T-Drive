<?php

use App\Http\Controllers\Admin\ActionLogController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\UserAuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Admin unauthorized page
Route::get('admin/unauthorized', function () {
    return view('admin.unauthorized');
})->name('admin.unauthorized');
// User PWA Auth routes (no /user prefix)

Route::get('/sign-in', [UserAuthController::class, 'showSignIn'])->name('user.signin');
Route::get('/sign-up', [UserAuthController::class, 'showSignUp'])->name('user.signup');

Route::post('/sign-in', [UserAuthController::class, 'signin'])->name('user.signin.post');
Route::post('/sign-up', [UserAuthController::class, 'signup'])->name('user.signup.post');
Route::post('/logout', [UserAuthController::class, 'logout'])->name('user.logout');



// Admin Auth and Dashboard routes
Route::get('login', function () {
    if (Auth::check() && Auth::user()->user_type === 'admin') {
        return redirect()->route('dashboard');
    }
    return view('auth.login');
})->name('login');

Route::get('forget-password', function () {
    return view('auth.login');
})->name('admin.forget-password');

Route::post('login', [AdminAuthController::class, 'login'])->name('admin.login.post');


// Public share viewing routes (no auth required)
Route::get('/s/{token}', [\App\Http\Controllers\ShareController::class, 'view'])->name('shares.view');
Route::get('/s/{token}/download', [\App\Http\Controllers\ShareController::class, 'download'])->name('shares.download');


// Protected routes for authenticated users (both admin and user)
Route::middleware(['auth'])->group(function () {

    // Dashboard route
    Route::get('/', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // File routes
    Route::post('/files/upload', [\App\Http\Controllers\FileController::class, 'upload'])->name('files.upload');
    Route::get('/files/{id}/view', [\App\Http\Controllers\FileController::class, 'view'])->name('files.view');
    Route::get('/files/{id}/download', [\App\Http\Controllers\FileController::class, 'download'])->name('files.download');
    Route::delete('/files/{id}', [\App\Http\Controllers\FileController::class, 'destroy'])->name('files.destroy');
    Route::post('/files/{id}/restore', [\App\Http\Controllers\FileController::class, 'restore'])->name('files.restore');
    Route::delete('/files/{id}/force-delete', [\App\Http\Controllers\FileController::class, 'forceDelete'])->name('files.force-delete');
    Route::get('/files/trash/list', [\App\Http\Controllers\FileController::class, 'trashed'])->name('files.trashed');

    // Folder routes
    Route::get('/folders', [\App\Http\Controllers\FolderController::class, 'index'])->name('folders.index');
    Route::get('/folders/{id}', [\App\Http\Controllers\FolderController::class, 'show'])->name('folders.show');
    Route::post('/folders/create', [\App\Http\Controllers\FolderController::class, 'create'])->name('folders.create');
    Route::delete('/folders/{id}', [\App\Http\Controllers\FolderController::class, 'destroy'])->name('folders.destroy');
    Route::post('/folders/{id}/restore', [\App\Http\Controllers\FolderController::class, 'restore'])->name('folders.restore');
    Route::delete('/folders/{id}/force-delete', [\App\Http\Controllers\FolderController::class, 'forceDelete'])->name('folders.force-delete');

    // Trash route
    Route::get('/trash', [\App\Http\Controllers\TrashController::class, 'index'])->name('trash.index');

    // Share routes
    Route::post('/shares', [\App\Http\Controllers\ShareController::class, 'store'])->name('shares.store');
    Route::get('/shared-with-me', [\App\Http\Controllers\ShareController::class, 'sharedWithMe'])->name('shares.shared-with-me');
    Route::get('/shares', [\App\Http\Controllers\ShareController::class, 'index'])->name('shares.index');

    Route::get('/recent', [\App\Http\Controllers\RecentViewController::class, 'index'])->name('recent.index');
    Route::delete('/shares/{id}', [\App\Http\Controllers\ShareController::class, 'destroy'])->name('shares.destroy');

    // Admin-only routes
    Route::middleware(['admin'])->group(function () {
        Route::resource('roles', RoleController::class, [
            'as' => 'admin'
        ]);
        Route::post('roles/{id}/restore', [RoleController::class, 'restore'])->name('admin.roles.restore');

        Route::resource('users', UserController::class, [
            'as' => 'admin'
        ]);
        Route::post('users/{id}/restore', [UserController::class, 'restore'])->name('admin.users.restore');
        Route::post('users/{id}/approve-bank-details', [UserController::class, 'approveBankDetails'])->name('admin.users.approve-bank-details');
        Route::post('users/{id}/reject-bank-details', [UserController::class, 'rejectBankDetails'])->name('admin.users.reject-bank-details');

        Route::get('action-logs', [ActionLogController::class, 'index'])->name('admin.action-logs.index');
        Route::get('action-logs/{id}', [ActionLogController::class, 'show'])->name('admin.action-logs.show');

        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    });

    // User-only routes
    Route::middleware(['user'])->group(function () {
        Route::get('/forgot-password', function () {
            return view('user.forgot-password');
        })->name('user.forgot-password');

        Route::get('/home', function () {
            return view('user.dashboard');
        })->name('user.dashboard');

        // Profile routes
        Route::get('/profile', function () {
            return view('user.profile');
        })->name('user.profile');
        Route::get('/edit-profile', [\App\Http\Controllers\User\ProfileController::class, 'edit'])->name('user.edit-profile');
        Route::put('/profile', [\App\Http\Controllers\User\ProfileController::class, 'update'])->name('user.profile.update');
    });
});
