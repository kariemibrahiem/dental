<?php

use App\Http\Controllers\Web\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard-analytics'))->name('front.home');

// Optional pages mapped to controller methods
Route::get('/about', [HomeController::class, 'about'])->name('front.about');
Route::get('/projects', function () {
	// If a named admin route `projects.index` exists and its URI is different
	// from the public `/projects`, redirect to it to reach the admin controller.
	if (\Illuminate\Support\Facades\Route::has('projects.index')) {
		$named = \Illuminate\Support\Facades\Route::getRoutes()->getByName('projects.index');
		if ($named) {
			$adminUri = method_exists($named, 'uri') ? $named->uri() : null;
			if ($adminUri && $adminUri !== 'projects') {
				return redirect('/' . ltrim($adminUri, '/'));
			}
		}
	}

	// Fallback to original front controller method when no redirect is needed
	return app()->call([new HomeController, 'projects']);
})->name('front.projects');
Route::get('/blog', [HomeController::class, 'blog'])->name('front.blog');
Route::get('/contact', [HomeController::class, 'contact'])->name('front.contact');
