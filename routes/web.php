<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MessageController;

Route::get('/', [PortfolioController::class, 'index'])->name('home');
Route::get('/home', [PortfolioController::class, 'index']);
Route::get('/projects', [PortfolioController::class, 'projects'])->name('projects.index');
Route::get('/api/portfolio', [PortfolioController::class, 'data'])->name('portfolio.data');
Route::get('/api/projects', [PortfolioController::class, 'projectData'])->name('portfolio.projects.data');
Route::get('/api/skills', [PortfolioController::class, 'skillData'])->name('portfolio.skills.data');
Route::get('/api/experiences', [PortfolioController::class, 'experienceData'])->name('portfolio.experiences.data');
Route::post('/contact', [MessageController::class, 'store'])->name('contact.store');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard', function () {
        return redirect()->route('admin.dashboard');
    })->name('dashboard');

    // API-like routes for admin panel (since UI is single page)
    Route::post('/admin/projects', [AdminController::class, 'storeProject']);
    Route::patch('/admin/projects/{id}', [AdminController::class, 'updateProject']);
    Route::delete('/admin/projects/{id}', [AdminController::class, 'destroyProject']);

    Route::post('/admin/skills', [AdminController::class, 'storeSkill']);
    Route::delete('/admin/skills/{id}', [AdminController::class, 'destroySkill']);

    Route::post('/admin/experiences', [AdminController::class, 'storeExperience']);
    Route::delete('/admin/experiences/{id}', [AdminController::class, 'destroyExperience']);

    Route::post('/admin/certifications', [AdminController::class, 'storeCertification']);
    Route::delete('/admin/certifications/{id}', [AdminController::class, 'destroyCertification']);

    Route::patch('/admin/messages/{id}/read', [AdminController::class, 'markMessageRead']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
