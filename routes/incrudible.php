<?php

use Illuminate\Support\Facades\Route;
use Incrudible\Incrudible\Facades\Incrudible;
use App\Incrudible\Http\Controllers\ProfileController;
use App\Incrudible\Http\Controllers\DashboardController;
use App\Incrudible\Http\Controllers\SettingsController;
use App\Incrudible\Http\Controllers\Auth\PasswordController;
use App\Incrudible\Http\Controllers\Auth\NewPasswordController;
use App\Incrudible\Http\Controllers\Auth\PasswordResetLinkController;
use App\Incrudible\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Incrudible\Http\Controllers\Auth\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| Incrudible Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix(Incrudible::routePrefix())
    ->name(Incrudible::routePrefix() . '.')
    ->middleware([
        // TODO: order seems to matter :/
        Incrudible::middleware(),
        'must-authenticate',
        // 'verified'
    ])
    ->group(function () {
        // Dashboard
        Route::get('/', [DashboardController::class, 'redirect']);
        Route::get('/dashboard', [DashboardController::class, 'dashboard'])
            ->name('dashboard');

        // Admin profile
        Route::get('/profile', [ProfileController::class, 'edit'])
            ->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])
            ->name('profile.update');

        // Route::get('verify-email', EmailVerificationPromptController::class)
        //     ->name('verification.notice');

        // Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        //     ->middleware(['signed', 'throttle:6,1'])
        //     ->name('verification.verify');

        // Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        //     ->middleware('throttle:6,1')
        //     ->name('verification.send');

        // Extra secured routes
        Route::get('settings', [SettingsController::class, 'settings'])
            ->middleware('must-confirm-password')
            ->name('settings');
        Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
            ->name('password.confirm');
        Route::post('confirm-password', [ConfirmablePasswordController::class, 'store'])
            ->name('password.confirm.post');

        // Update password (when authenticated)
        Route::put('/profile/password', [PasswordController::class, 'update'])
            ->name('password.update');

        // Logout
        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
            ->name('logout');
    });

// AUTH

Route::prefix(Incrudible::routePrefix())
    ->name(Incrudible::routePrefix() . '.auth.')
    ->middleware([
        Incrudible::middleware(),
        'guest'
    ])
    ->group(function () {

        // Login
        Route::get('login', [AuthenticatedSessionController::class, 'create'])
            ->name('login');
        Route::post('login', [AuthenticatedSessionController::class, 'store']);

        // Request password reset link
        Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
            ->name('password.request');
        Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
            ->name('password.email');

        // Reset password
        Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
            ->name('password.reset');
        Route::post('reset-password', [NewPasswordController::class, 'store'])
            ->name('password.store');
    });
