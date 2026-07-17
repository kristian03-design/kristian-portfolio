<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MessageController;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

$statelessPublic = [
    EncryptCookies::class,
    AddQueuedCookiesToResponse::class,
    StartSession::class,
    ShareErrorsFromSession::class,
    VerifyCsrfToken::class,
];

RateLimiter::for('contact', function (Request $request) {
    $key = $request->ip() ?: 'guest';

    return [
        Limit::perMinute(3)->by($key),
        Limit::perDay(20)->by($key),
    ];
});

Route::get('/', [PortfolioController::class, 'index'])->name('home')->withoutMiddleware($statelessPublic);
Route::get('/home', [PortfolioController::class, 'index'])->withoutMiddleware($statelessPublic);
Route::get('/resume', [PortfolioController::class, 'viewResume'])->name('resume.view')->withoutMiddleware($statelessPublic);
Route::get('/resume/download', [PortfolioController::class, 'downloadResume'])->name('resume.download')->withoutMiddleware($statelessPublic);
Route::get('/projects', [PortfolioController::class, 'projects'])->name('projects.index')->withoutMiddleware($statelessPublic);
Route::get('/projects/{project:slug}', [PortfolioController::class, 'show'])->name('projects.show')->withoutMiddleware($statelessPublic);
Route::get('/projects/{project:slug}/coming-soon', [PortfolioController::class, 'comingSoon'])->name('projects.coming-soon')->withoutMiddleware($statelessPublic);
Route::get('/api/portfolio', [PortfolioController::class, 'data'])->name('portfolio.data')->withoutMiddleware($statelessPublic);
Route::get('/api/projects', [PortfolioController::class, 'projectData'])->name('portfolio.projects.data')->withoutMiddleware($statelessPublic);
Route::get('/api/skills', [PortfolioController::class, 'skillData'])->name('portfolio.skills.data')->withoutMiddleware($statelessPublic);
Route::get('/api/experiences', [PortfolioController::class, 'experienceData'])->name('portfolio.experiences.data')->withoutMiddleware($statelessPublic);
Route::get('/media/{path}', [PortfolioController::class, 'media'])->where('path', '.*')->name('portfolio.media')->withoutMiddleware($statelessPublic);
Route::post('/contact', [MessageController::class, 'store'])->middleware('throttle:contact')->name('contact.store')->withoutMiddleware($statelessPublic);

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard', function () {
        return redirect()->route('admin.dashboard');
    })->name('dashboard');

    // API-like routes for admin panel (since UI is single page)
    Route::post('/admin/projects', [AdminController::class, 'storeProject'])->middleware('throttle:uploads');
    Route::patch('/admin/projects/{id}', [AdminController::class, 'updateProject'])->middleware('throttle:uploads');
    Route::patch('/admin/projects/{id}/details', [AdminController::class, 'updateProjectDetails']);
    Route::delete('/admin/projects/{id}', [AdminController::class, 'destroyProject']);

    Route::post('/admin/skills', [AdminController::class, 'storeSkill']);
    Route::delete('/admin/skills/{id}', [AdminController::class, 'destroySkill']);

    Route::post('/admin/experiences', [AdminController::class, 'storeExperience']);
    Route::delete('/admin/experiences/{id}', [AdminController::class, 'destroyExperience']);

    Route::post('/admin/certifications', [AdminController::class, 'storeCertification'])->middleware('throttle:uploads');
    Route::delete('/admin/certifications/{id}', [AdminController::class, 'destroyCertification']);

    Route::post('/admin/gallery', [AdminController::class, 'storeGalleryItem'])->middleware('throttle:uploads');
    Route::patch('/admin/gallery/reorder', [AdminController::class, 'reorderGalleryItems']);
    Route::patch('/admin/gallery/{id}', [AdminController::class, 'updateGalleryItem'])->middleware('throttle:uploads');
    Route::delete('/admin/gallery/{id}', [AdminController::class, 'destroyGalleryItem']);
    Route::post('/admin/gallery/{id}/toggle-published', [AdminController::class, 'toggleGalleryItemPublished']);
    Route::post('/admin/gallery/{id}/toggle-featured', [AdminController::class, 'toggleGalleryItemFeatured']);

    Route::patch('/admin/messages/{id}/read', [AdminController::class, 'markMessageRead']);
    Route::post('/admin/messages/{id}/reply', [AdminController::class, 'replyMessage']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/profile/sessions/{id}', [ProfileController::class, 'terminateSession'])->name('profile.sessions.terminate');
    Route::delete('/profile/sessions', [ProfileController::class, 'terminateOtherSessions'])->name('profile.sessions.terminate_others');
});

require __DIR__.'/auth.php';
