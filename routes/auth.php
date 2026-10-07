<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\PrivacyAgreementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SessionLockController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Route::get('register', [RegisteredUserController::class, 'create'])
    //     ->name('register');

    // Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('session-locked', [SessionLockController::class, 'show'])->name('security.lock.show');
    Route::post('session-lock', [SessionLockController::class, 'lock'])->name('security.lock.store');
    Route::post('session-activity', [SessionLockController::class, 'touch'])->middleware('throttle:30,1')->name('security.activity');
    Route::post('session-unlock', [SessionLockController::class, 'unlock'])->middleware('throttle:10,1')->name('security.lock.unlock');

    Route::get('privacy-agreement', [PrivacyAgreementController::class, 'show'])
        ->name('privacy-agreement.show');

    Route::post('privacy-agreement/accept', [PrivacyAgreementController::class, 'accept'])
        ->name('privacy-agreement.accept');

    Route::post('privacy-agreement/decline', [PrivacyAgreementController::class, 'decline'])
        ->name('privacy-agreement.decline');

    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::get('profile', [ProfileController::class, 'edit'])->middleware('privacy.accepted')->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->middleware('privacy.accepted')->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->middleware('privacy.accepted')->name('profile.destroy');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
