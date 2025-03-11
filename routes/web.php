<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserValidationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});



Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    // Catalog & Items
    Route::middleware(['permission:view-catalog'])->group(function () {
        Route::get('/items', [ItemController::class, 'index'])->name('items.index');

        // La route 'create' doit être AVANT la route avec paramètre
        Route::get('/items/create', [ItemController::class, 'create'])
            ->middleware(['permission:create-item'])
            ->name('items.create');

        Route::post('/items', [ItemController::class, 'store'])
            ->middleware(['permission:create-item'])
            ->name('items.store');

        Route::get('/items/{item}', [ItemController::class, 'show'])
            ->middleware(['permission:view-item-details'])
            ->name('items.show');
    });

    Route::middleware(['permission:create-item'])->group(function () {
        Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
        Route::post('/items', [ItemController::class, 'store'])->name('items.store');
    });

    Route::post('/items/{item}/duplicate', [ItemController::class, 'duplicate'])
        ->middleware(['permission:create-item'])
        ->name('items.duplicate');

    Route::middleware(['permission:edit-item'])->group(function () {
        Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
        Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
    });

    Route::post('/items/{item}/reserve', [ReservationController::class, 'reserve'])
        ->middleware(['permission:reserve-item'])
        ->name('items.reserve');

    Route::delete('/items/{item}', [ItemController::class, 'destroy'])
        ->middleware(['permission:delete-item'])
        ->name('items.destroy');

    // Loans
    Route::middleware(['permission:create-loan'])->group(function () {
        Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
        Route::get('/loans/create', [LoanController::class, 'create'])->name('loans.create');
        Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');
        Route::get('/loans/{loan}', [LoanController::class, 'show'])->name('loans.show');
    });

    Route::middleware(['permission:edit-loan'])->group(function () {
        Route::get('/loans/{loan}/edit', [LoanController::class, 'edit'])->name('loans.edit');
        Route::put('/loans/{loan}', [LoanController::class, 'update'])->name('loans.update');
    });

    Route::delete('/loans/{loan}', [LoanController::class, 'destroy'])
        ->middleware(['permission:delete-loan'])
        ->name('loans.destroy');

    Route::post('/loans/{loan}/return', [LoanController::class, 'returnItem'])
        ->middleware(['permission:return-loan'])
        ->name('loans.return');

    Route::post('/loans/{loan}/extend', [LoanController::class, 'extend'])
        ->middleware(['permission:extend-loan'])
        ->name('loans.extend');

    // Reservations
    Route::middleware(['permission:reserve-item'])->group(function () {
        Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
        Route::get('/reservations/create', [ReservationController::class, 'create'])->name('reservations.create');
        Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
        Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    });

    Route::middleware(['permission:edit-reservation'])->group(function () {
        Route::get('/reservations/{reservation}/edit', [ReservationController::class, 'edit'])->name('reservations.edit');
        Route::put('/reservations/{reservation}', [ReservationController::class, 'update'])->name('reservations.update');
    });

    Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy'])
        ->middleware(['permission:delete-reservation'])
        ->name('reservations.destroy');

    Route::post('/reservations/reorder', [ReservationController::class, 'reorder'])
        ->middleware(['permission:reorganize-queue'])
        ->name('reservations.reorder');

    // Payments
    Route::middleware(['permission:create-payment'])->group(function () {
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    });

    Route::middleware(['permission:edit-payment'])->group(function () {
        Route::get('/payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
        Route::put('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
    });

    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])
        ->middleware(['permission:delete-payment'])
        ->name('payments.destroy');

       Route::middleware(['permission:edit-user'])->group(function () {
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    });

    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->middleware(['permission:delete-user'])
        ->name('users.destroy');

    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])
        ->middleware(['permission:reset-user-password'])
        ->name('users.reset-password');

    // User validation
    Route::middleware(['permission:create-user'])->group(function () {
        Route::get('/users/validate', [UserValidationController::class, 'index'])->name('users.validate');
        Route::post('/users/{user}/validate', [UserValidationController::class, 'validateUser'])->name('users.validate.approve');
        Route::delete('/users/{user}/reject', [UserValidationController::class, 'reject'])->name('users.validate.reject');
    });
    // Users
    Route::middleware(['permission:create-user'])->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    });

    // Roles
    Route::middleware(['permission:create-role'])->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}', [RoleController::class, 'show'])->name('roles.show');
    });

    Route::middleware(['permission:edit-role-permissions'])->group(function () {
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    });

    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
        ->middleware(['permission:delete-role'])
        ->name('roles.destroy');

    Route::delete('/roles/{role}/replace/{replacement}', [RoleController::class, 'destroyAndReplace'])
        ->middleware(['permission:delete-role-with-replacement'])
        ->name('roles.destroy-and-replace');

    // Permissions
    Route::get('/permissions', [PermissionController::class, 'index'])
        ->middleware(['permission:edit-role-permissions'])
        ->name('permissions.index');

    // Activity Logs
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])
        ->middleware(['permission:view-all-activity-logs,view-own-activity-logs'])
        ->name('activity-logs.index');

    //Categories
    // Categories
    Route::middleware(['permission:edit-item'])->group(function () {
        Route::resource('categories', CategoryController::class);
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('api')->middleware('auth')->group(function () {
    Route::get('/users/search', [App\Http\Controllers\Api\UserController::class, 'search'])->name('api.users.search');
    Route::get('/items/search', [App\Http\Controllers\Api\ItemController::class, 'search'])->name('api.items.search');
});

require __DIR__.'/auth.php';
