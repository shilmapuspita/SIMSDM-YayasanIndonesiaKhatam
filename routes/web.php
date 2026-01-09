<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// Controller Dashboard per Role
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\HRDDashboardController;
use App\Http\Controllers\EmployeeDashboardController;

// Controller Fitur (CRUD)
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\LeaveController;

// Middleware (Panggil Class langsung biar anti-error)
use App\Http\Middleware\RoleMiddleware;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

/* =======================
| PUBLIC
| ======================= */

Route::get('/', function () {
    return view('welcome');
});

/* =======================
| AUTH DASHBOARD DEFAULT
| ======================= */
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


/* ===================================================
| 1. GROUP GABUNGAN (ADMIN & HRD)
| =================================================== */
Route::middleware(['auth', RoleMiddleware::class . ':admin,hrd'])->group(function () {

    // --- MANAJEMEN KARYAWAN ---
    Route::resource('karyawan', EmployeeController::class)
        ->middleware(RoleMiddleware::class . ':admin,hrd');

    // --- MONITORING ABSENSI ---
    // URL: /attendances
    Route::resource('absensi', AttendanceController::class)
     ->middleware(RoleMiddleware::class . ':admin,hrd');

    // --- APPROVAL CUTI ---
    // URL: /leaves
    Route::resource('cuti', LeaveController::class)
        ->middleware(RoleMiddleware::class . ':admin,hrd');
});


/* ===================================================
| 2. GROUP KHUSUS ADMIN (SUPER USER)
| ---------------------------------------------------
| Area Sensitif: Settings, User Accounts, Logs
| HRD TIDAK BISA akses ini.
| =================================================== */
Route::middleware(['auth', RoleMiddleware::class . ':admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        // MANAJEMEN USER (AKUN LOGIN)
        Route::get('/users', fn() => view('admin.users.index'))->name('users.index');
        Route::get('/roles', fn() => view('admin.roles.index'))->name('roles.index');

        // SYSTEM LOGS
        Route::get('/logs', fn() => view('admin.logs.index'))->name('logs.index');

        // SETTINGS
        Route::get('/settings', fn() => view('admin.settings'))->name('settings');
    });


/* ===================================================
| 3. GROUP KHUSUS HRD
| ---------------------------------------------------
| Dashboard Khusus HRD
| =================================================== */
Route::middleware(['auth', RoleMiddleware::class . ':hrd'])
    ->prefix('hrd')
    ->name('hrd.')
    ->group(function () {

        Route::get('/dashboard', [HRDDashboardController::class, 'index'])
            ->name('dashboard');
    });


/* ===================================================
| 4. GROUP KHUSUS EMPLOYEE (KARYAWAN BIASA)
| ---------------------------------------------------
| Absen Sendiri, Ajukan Cuti Sendiri
| =================================================== */
Route::middleware(['auth', RoleMiddleware::class . ':employee'])
    ->prefix('employee')
    ->name('employee.')
    ->group(function () {

        // --- DASHBOARD ---
        Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])
            ->name('dashboard');

        // --- ABSENSI (MANDIRI) ---
        Route::get('/attendance', [EmployeeDashboardController::class, 'absensi'])
            ->name('absensi');

        Route::post('/absensi', [EmployeeDashboardController::class, 'storeAbsensi'])
            ->name('absensi.store');

        Route::put('/absensi/pulang', [EmployeeDashboardController::class, 'updateAbsensi'])
            ->name('absensi.update');

        // --- CUTI (MANDIRI) ---
        Route::get('/leaves', [EmployeeDashboardController::class, 'cuti'])
            ->name('cuti');

        Route::post('/leaves', [EmployeeDashboardController::class, 'storeCuti'])
            ->name('cuti.store');

        // --- PROFIL ---
        Route::get('/profile', [EmployeeDashboardController::class, 'profile'])
            ->name('profile');

        Route::patch('/profile', [EmployeeDashboardController::class, 'updateProfile'])
            ->name('profile.update');
    });


/* =======================
| PROFILE (BREEZE DEFAULT)
| ======================= */
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/* =======================
| AUTH ROUTES
| ======================= */
require __DIR__ . '/auth.php';
