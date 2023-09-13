<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Site\RegisterController;
use App\Http\Controllers\Site\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('site.home');
})->name('site.home');

Route::middleware('auth:sanctum')->post('/logout', [RegisterController::class, 'logout'])->name('site.logout');
Route::post('/login', [RegisterController::class, 'login'])->name('site.loginprocess');
Route::get('/logout', [RegisterController::class, 'logutIndex'])->name('site.logout');

Route::get('/register', [RegisterController::class, 'getRegisterIndex'])->name('site.register');
Route::get('/login', [RegisterController::class, 'getLoginIndex'])->name('site.login');
Route::middleware('auth:sanctum')->get('/get-user', [RegisterController::class, 'getUser'])->name('site.get-user');
Route::post('/register', [RegisterController::class, 'register'])->name('site.register');
Route::get('/term/{name}/{id}', [HomeController::class, 'getTermIndex'])->name('term.get');
Route::get('/article/{id}', [HomeController::class, 'getArticleIndex'])->name('article.get');
Route::post('/term', [HomeController::class, 'getTerm'])->name('site.getterm');
Route::post('/article', [HomeController::class, 'getArticle'])->name('site.getarticle');
Route::post('/latest-terms', [HomeController::class, 'getLatestTerms'])->name('term.getlatest');
Route::post('/latest-articles', [HomeController::class, 'getLatestLatest'])->name('article.getlatest');
