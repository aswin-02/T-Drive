<?php

use App\Http\Controllers\Admin\ActionLogController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\UserAuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\BroadcastAuthController;

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
Route::get('admin', function () {
    if (Auth::check() && Auth::user()->user_type === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('admin.login');
});
Route::get('admin/login', function () {
    return view('admin.auth.login');
})->name('admin.login');
Route::get('admin/forget-password', function () {
    return view('admin.auth.login');
})->name('admin.forget-password');
Route::post('admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');

Route::prefix('admin')->middleware(['admin'])->group(function () {
    // Support (chat) routes
    Route::get('support', [\App\Http\Controllers\Admin\SupportController::class, 'index'])->name('admin.support.index');
    Route::get('support/chat/{user}', [\App\Http\Controllers\Admin\SupportController::class, 'chat'])->name('admin.support.chat');
    Route::get('support/messages/{chat}', [\App\Http\Controllers\Admin\SupportController::class, 'getMessages'])->name('admin.support.messages');
    Route::get('support/messages/{chat}/more', [\App\Http\Controllers\Admin\SupportController::class, 'loadMoreMessages'])->name('admin.support.messages.more');
    Route::post('support/chat/{chat}/send', [\App\Http\Controllers\Admin\SupportController::class, 'sendMessage'])->name('admin.support.send');
    Route::post('support/chat/{chat}/close', [\App\Http\Controllers\Admin\SupportController::class, 'closeTicket'])->name('admin.support.close');
    Route::delete('support/message/{message}', [\App\Http\Controllers\Admin\SupportController::class, 'deleteMessage'])->name('admin.support.message.delete');

    // Reports routes
    Route::get('reports', function () {
        return view('admin.dashboard');
    })->name('admin.reports.dashboard');
    Route::get('reports/profit-collections', [\App\Http\Controllers\Admin\ReportController::class, 'profitCollections'])->name('admin.reports.profit-collections');
    Route::get('reports/plan-profitability', [\App\Http\Controllers\Admin\ReportController::class, 'planProfitability'])->name('admin.reports.plan-profitability');
    Route::get('reports/customer-profitability', [\App\Http\Controllers\Admin\ReportController::class, 'customerProfitability'])->name('admin.reports.customer-profitability');
    Route::get('reports/expenses', [\App\Http\Controllers\Admin\ReportController::class, 'expenses'])->name('admin.reports.expenses');

    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    Route::get('/dashboard', [
        \App\Http\Controllers\Admin\ReportController::class, 'dashboard'
    ])->name('admin.dashboard');

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

    Route::resource('plans', \App\Http\Controllers\Admin\PlanController::class, [
        'as' => 'admin'
    ]);
    // Purchases menu (index, show only)
    Route::resource('purchases', \App\Http\Controllers\Admin\PurchaseController::class, [
        'as' => 'admin',
        'only' => ['index', 'show']
    ]);
    Route::post('purchases/{id}/revert', [\App\Http\Controllers\Admin\PurchaseController::class, 'revert'])->name('admin.purchases.revert');
    Route::post('plans/{id}/restore', [\App\Http\Controllers\Admin\PlanController::class, 'restore'])->name('admin.plans.restore');

    Route::resource('customers', \App\Http\Controllers\Admin\CustomerController::class, [
        'as' => 'admin'
    ]);
    Route::post('customers/{id}/restore', [\App\Http\Controllers\Admin\CustomerController::class, 'restore'])->name('admin.customers.restore');
    Route::get('customers/{id}/bank-details', [\App\Http\Controllers\Admin\CustomerController::class, 'getBankDetails'])->name('admin.customers.bank-details');
    Route::post('customers/{id}/approve-bank-details', [\App\Http\Controllers\Admin\CustomerController::class, 'approveBankDetails'])->name('admin.customers.approve-bank-details');
    Route::post('customers/{id}/reject-bank-details', [\App\Http\Controllers\Admin\CustomerController::class, 'rejectBankDetails'])->name('admin.customers.reject-bank-details');

    Route::get('credit-days', [\App\Http\Controllers\Admin\CreditDayController::class, 'index'])->name('admin.credit-days.index');
    Route::get('credit-days/{year}/{month}/view', [\App\Http\Controllers\Admin\CreditDayController::class, 'show'])->name('admin.credit-days.view');
    Route::get('credit-days/{year}/{month}/edit', [\App\Http\Controllers\Admin\CreditDayController::class, 'edit'])->name('admin.credit-days.edit');
    Route::post('credit-days/{year}/{month}/update', [\App\Http\Controllers\Admin\CreditDayController::class, 'update'])->name('admin.credit-days.update');

    // Withdrawal Requests
    Route::get('withdrawals', [\App\Http\Controllers\Admin\WithdrawalRequestController::class, 'index'])->name('admin.withdrawals.index');
    Route::get('withdrawals/{id}/details', [\App\Http\Controllers\Admin\WithdrawalRequestController::class, 'details'])->name('admin.withdrawals.details');
    Route::post('withdrawals/{id}/update-status', [\App\Http\Controllers\Admin\WithdrawalRequestController::class, 'updateStatus'])->name('admin.withdrawals.update-status');
    Route::post('withdrawals/bulk-update', [\App\Http\Controllers\Admin\WithdrawalRequestController::class, 'bulkUpdateStatus'])->name('admin.withdrawals.bulk-update');
    Route::get('withdrawals-export', [\App\Http\Controllers\Admin\WithdrawalRequestController::class, 'export'])->name('admin.withdrawals.export');

    Route::get('action-logs', [ActionLogController::class, 'index'])->name('admin.action-logs.index');
    Route::get('action-logs/{id}', [ActionLogController::class, 'show'])->name('admin.action-logs.show');
});

Route::get('/', function () {
    return view('landing.home');
})->name('landing.home');

Route::get('/contact-us', function () {
    return view('landing.contact-us');
})->name('landing.contact-us');

Route::get('/privacy-policy', function () {
    return view('landing.privacy-policy');
})->name('landing.privacy-policy');

Route::get('/terms-and-conditions', function () {
    return view('landing.terms-and-conditions');
})->name('landing.terms-and-conditions');

Route::get('/refund-and-cancellation', function () {
    return view('landing.refund-and-cancellation');
})->name('landing.refund-and-cancellation');


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

    // Referral routes
    Route::get('/referrals', [\App\Http\Controllers\User\ReferralController::class, 'index'])->name('user.referrals');

    // Wallet routes
    Route::get('/wallet', [\App\Http\Controllers\User\WalletController::class, 'index'])->name('user.wallet');
    Route::get('/wallet/add-money', [\App\Http\Controllers\User\WalletController::class, 'addMoney'])->name('user.wallet.add');
    Route::post('/wallet/add-money', [\App\Http\Controllers\User\WalletController::class, 'storeAddMoney'])->name('user.wallet.add.store');
    Route::get('/wallet/transactions', [\App\Http\Controllers\User\WalletController::class, 'transactions'])->name('user.wallet.transactions');

    // Withdrawal routes
    Route::get('/withdrawals', [\App\Http\Controllers\User\WithdrawalController::class, 'index'])->name('user.withdrawals.index');
    Route::get('/withdrawals/create', [\App\Http\Controllers\User\WithdrawalController::class, 'create'])->name('user.withdrawals.create');
    Route::post('/withdrawals', [\App\Http\Controllers\User\WithdrawalController::class, 'store'])->name('user.withdrawals.store');
    Route::get('/withdrawals/{id}', [\App\Http\Controllers\User\WithdrawalController::class, 'show'])->name('user.withdrawals.show');

    // Plan routes
    Route::get('/plans', [\App\Http\Controllers\User\PlanController::class, 'index'])->name('user.plans');    
    // Notification routes
    Route::get('/notifications', [\App\Http\Controllers\User\NotificationController::class, 'index'])->name('user.notifications.index');
    Route::get('/notifications/settings', [\App\Http\Controllers\User\NotificationController::class, 'settings'])->name('user.notifications.settings');
    Route::get('/notifications/{id}', [\App\Http\Controllers\User\NotificationController::class, 'show'])->name('user.notifications.show');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\User\NotificationController::class, 'markAsRead'])->name('user.notifications.read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\User\NotificationController::class, 'markAllAsRead'])->name('user.notifications.mark-all-read');
    Route::delete('/notifications/{id}', [\App\Http\Controllers\User\NotificationController::class, 'destroy'])->name('user.notifications.destroy');
    Route::delete('/notifications/delete-all-read', [\App\Http\Controllers\User\NotificationController::class, 'deleteAllRead'])->name('user.notifications.delete-all-read');
    Route::get('/notifications/unread-count', [\App\Http\Controllers\User\NotificationController::class, 'getUnreadCount'])->name('user.notifications.unread-count');
        // Support routes
    Route::get('/support', [\App\Http\Controllers\User\SupportController::class, 'index'])->name('user.support.index');
    Route::get('/support/create', [\App\Http\Controllers\User\SupportController::class, 'create'])->name('user.support.create');
    Route::post('/support/create', [\App\Http\Controllers\User\SupportController::class, 'store'])->name('user.support.store');
    Route::get('/support/{ticket}', [\App\Http\Controllers\User\SupportController::class, 'show'])->name('user.support.show');
    Route::post('/support/{chat}/select-faq', [\App\Http\Controllers\User\SupportController::class, 'selectFaq'])->name('user.support.select-faq');
    Route::post('/support/{chat}/respond-faq', [\App\Http\Controllers\User\SupportController::class, 'respondToFaq'])->name('user.support.respond-faq');
    Route::post('/support/send/{chat}', [\App\Http\Controllers\User\SupportController::class, 'sendMessage'])->name('user.support.send');
    Route::get('/support/paginate/{chat}', [\App\Http\Controllers\User\SupportController::class, 'paginate'])->name('user.support.paginate');
    
    // Purchase routes
    Route::post('/purchase', [\App\Http\Controllers\User\PurchaseController::class, 'store'])->name('user.purchase');
    
    // Spin Wheel routes
    Route::post('/spin-wheel', [\App\Http\Controllers\User\SpinWheelController::class, 'store'])->name('user.spin-wheel.store');
    
    // // Withdrawal routes
    // Route::get('/withdrawals', [\App\Http\Controllers\User\WithdrawalController::class, 'index'])->name('user.withdrawals');
    // Route::post('/withdraw', [\App\Http\Controllers\User\WithdrawalController::class, 'request'])->name('user.withdraw.request');
    // Route::get('/withdrawal/{id}', [\App\Http\Controllers\User\WithdrawalController::class, 'show'])->name('user.withdrawal.show');

});

Route::get('/getStates', [GeneralController::class, 'getStates'])->name('getStates');
Route::get('/getDistricts', [GeneralController::class, 'getDistricts'])->name('getDistricts');
Route::get('/getCities', [GeneralController::class, 'getCities'])->name('getCities');
Route::get('/getPinCodes', [GeneralController::class, 'getPinCodes'])->name('getPinCodes');

