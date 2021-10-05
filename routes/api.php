<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\SiteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::get('/banners',[SiteController::class, 'banners']);
Route::get('/categorias',[SiteController::class, 'categorias']);
Route::get('/artistas',[SiteController::class, 'artistas']);
Route::get('/artista/{url}',[SiteController::class, 'artista']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//rotas de atualizações temporárias de implantação


// Route::group(function(){
    // Route::resource('/categorias',CategoriaController::class);

    // Route::resource('/banners',BannerController::class);
// });
