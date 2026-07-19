<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
// Import kelompok Controller untuk USER/ASN/GUEST
use App\Http\Controllers\User\BerandaController;
use App\Http\Controllers\User\ArtikelController;
use App\Http\Controllers\User\ForumController;
use App\Http\Controllers\User\RegulasiController;

// Import kelompok Controller untuk ADMIN
use App\Http\Controllers\Admin\AdminArtikelController;
use App\Http\Controllers\Admin\AdminForumController;
use App\Http\Controllers\Admin\AuditLogController as AdminAuditLogController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\TaxonomyController as AdminTaxonomyController;
use App\Http\Controllers\Admin\UserManagementController as AdminUserManagementController;

Route::get('/dashboard', function () {
    if (auth()->user()?->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('portal.dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::delete('/profile/avatar', [ProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');
    Route::post('/profile/documents', [ProfileController::class, 'storeDocument'])->name('profile.documents.store');
    Route::get('/profile/documents/{document}/download', [ProfileController::class, 'downloadDocument'])->name('profile.documents.download');
    Route::delete('/profile/documents/{document}', [ProfileController::class, 'destroyDocument'])->name('profile.documents.destroy');
    Route::post('/profile/deactivation-request', [ProfileController::class, 'requestDeactivation'])->name('profile.deactivation-request.store');
    Route::delete('/profile/deactivation-request', [ProfileController::class, 'cancelDeactivationRequest'])->name('profile.deactivation-request.cancel');
});

// Halaman yang bisa diakses tanpa login (Guest / Publik)
Route::get('/', [BerandaController::class, 'index'])->name('beranda');
Route::get('/artikel', [ArtikelController::class, 'index'])->name('artikel.index');
Route::get('/artikel/{artikel}', [ArtikelController::class, 'show'])->name('artikel.show');
Route::get('/regulasi', [RegulasiController::class, 'index'])->name('regulasi.index');
Route::get('/basis-pengetahuan', [BerandaController::class, 'knowledge'])->name('knowledge.index');
Route::get('/dashboard-publik', [BerandaController::class, 'publicDashboard'])->name('public-dashboard.index');
Route::get('/forum', [ForumController::class, 'index'])->name('forum.index');
Route::get('/forum/detail/{slug}', [ForumController::class, 'show'])->name('forum.show');

// Halaman yang HANYA bisa diakses setelah Login (ASN dan Admin)
Route::middleware(['auth'])->group(function () {
    
    // Fitur Forum & Tulis Artikel (Hanya boleh diakses oleh Admin dan ASN)
    Route::middleware(['role:admin,asn'])->group(function () {
        Route::get('/portal/dashboard', [ArtikelController::class, 'dashboard'])->name('portal.dashboard');

        Route::prefix('/portal/artikel')->name('portal.artikel.')->group(function () {
            Route::get('/', [ArtikelController::class, 'internalIndex'])->name('index');
            Route::get('/buat', [ArtikelController::class, 'create'])->name('create');
            Route::post('/', [ArtikelController::class, 'store'])->name('store');
            Route::post('/media', [ArtikelController::class, 'uploadMedia'])->name('media.store');
            Route::get('/{artikel}/edit', [ArtikelController::class, 'edit'])->name('edit');
            Route::put('/{artikel}', [ArtikelController::class, 'update'])->name('update');
            Route::delete('/{artikel}', [ArtikelController::class, 'destroy'])->name('destroy');
            Route::post('/{artikel}/submit', [ArtikelController::class, 'submit'])->name('submit');
        });

        Route::post('/forum/store', [ForumController::class, 'store'])->name('forum.store');
        Route::get('/forum/buat-topik', [ForumController::class, 'create'])->name('forum.create');
        Route::get('/forum/topik-saya', [ForumController::class, 'myTopics'])->name('forum.my-topics');
        Route::get('/forum/edit/{id}', [ForumController::class, 'edit'])->name('forum.edit');
        Route::put('/forum/edit/{id}', [ForumController::class, 'update'])->name('forum.update');
        Route::delete('/forum/hapus/{id}', [ForumController::class, 'destroy'])->name('forum.destroy');
        Route::patch('/forum/{topic}/toggle-active', [ForumController::class, 'toggleActive'])->name('forum.toggle-active');
    });

    // Fitur Khusus Admin (Validasi Artikel & Manajemen Sistem)
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminArtikelController::class, 'dashboard'])->name('dashboard');
        Route::get('/artikel/validasi', [AdminArtikelController::class, 'indexValidasi'])->name('artikel.validasi');
        Route::get('/artikel/{artikel}/review', [AdminArtikelController::class, 'show'])->name('artikel.show');
        Route::post('/artikel/{artikel}/approve', [AdminArtikelController::class, 'approve'])->name('artikel.approve');
        Route::post('/artikel/{artikel}/reject', [AdminArtikelController::class, 'reject'])->name('artikel.reject');
        Route::post('/artikel/{artikel}/publish', [AdminArtikelController::class, 'publish'])->name('artikel.publish');
        Route::post('/artikel/{artikel}/unpublish', [AdminArtikelController::class, 'unpublish'])->name('artikel.unpublish');
        Route::delete('/artikel/{artikel}', [AdminArtikelController::class, 'destroy'])->name('artikel.destroy');
        Route::get('/forum', [AdminForumController::class, 'index'])->name('forum.index');
        Route::get('/forum/{topic}', [AdminForumController::class, 'show'])->name('forum.show');
        Route::patch('/forum/{topic}/toggle-active', [AdminForumController::class, 'toggleActive'])->name('forum.toggle-active');
        Route::delete('/forum/{topic}', [AdminForumController::class, 'destroy'])->name('forum.destroy');
        Route::patch('/forum/balasan/{reply}/toggle-active', [AdminForumController::class, 'toggleReplyActive'])->name('forum.reply.toggle-active');
        Route::delete('/forum/balasan/{reply}', [AdminForumController::class, 'destroyReply'])->name('forum.reply.destroy');
        Route::get('/taksonomi', [AdminTaxonomyController::class, 'index'])->name('taxonomy.index');
        Route::post('/kategori', [AdminTaxonomyController::class, 'storeCategory'])->name('categories.store');
        Route::put('/kategori/{category}', [AdminTaxonomyController::class, 'updateCategory'])->name('categories.update');
        Route::post('/tag', [AdminTaxonomyController::class, 'storeTag'])->name('tags.store');
        Route::put('/tag/{tag}', [AdminTaxonomyController::class, 'updateTag'])->name('tags.update');
        Route::get('/pengguna', [AdminUserManagementController::class, 'index'])->name('users.index');
        Route::post('/pengguna', [AdminUserManagementController::class, 'store'])->name('users.store');
        Route::put('/pengguna/{user}', [AdminUserManagementController::class, 'update'])->name('users.update');
        Route::patch('/pengguna/{user}/toggle-active', [AdminUserManagementController::class, 'toggleActive'])->name('users.toggle-active');
        Route::delete('/pengguna/{user}', [AdminUserManagementController::class, 'destroy'])->name('users.destroy');
        Route::post('/pengajuan-penonaktifan/{deactivationRequest}/approve', [AdminUserManagementController::class, 'approveDeactivation'])->name('deactivation-requests.approve');
        Route::post('/pengajuan-penonaktifan/{deactivationRequest}/reject', [AdminUserManagementController::class, 'rejectDeactivation'])->name('deactivation-requests.reject');
        Route::get('/audit-log', [AdminAuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/pengaturan', [AdminSettingsController::class, 'index'])->name('settings.index');
        Route::put('/pengaturan', [AdminSettingsController::class, 'update'])->name('settings.update');
    });
});

Route::middleware(['web', 'auth', 'role:admin,asn'])->group(function () {
    // Alur Interaksi Komunitas (Komentar & Suka)
    Route::post('/forum/detail/{id}/balas', [ForumController::class, 'storeReply'])->name('forum.reply.store');
    Route::post('/forum/detail/{id}/like', [ForumController::class, 'toggleLike'])->name('forum.like.toggle');
    Route::post('/forum/balasan/{reply}/like', [ForumController::class, 'toggleReplyLike'])->name('forum.reply.like');

});

require __DIR__.'/auth.php';
