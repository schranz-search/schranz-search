<?php

declare(strict_types=1);

use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [SearchController::class, 'home']);
Route::get('/algolia', [SearchController::class, 'algolia']);
Route::get('/elasticsearch', [SearchController::class, 'elasticsearch']);
Route::get('/meilisearch', [SearchController::class, 'meilisearch']);
Route::get('/memory', [SearchController::class, 'memory']);
Route::get('/opensearch', [SearchController::class, 'opensearch']);
Route::get('/redisearch', [SearchController::class, 'redisearch']);
Route::get('/solr', [SearchController::class, 'solr']);
Route::get('/typesense', [SearchController::class, 'typesense']);
Route::get('/multi', [SearchController::class, 'multi']);
Route::get('/read-write', [SearchController::class, 'readWrite']);
