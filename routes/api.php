<?php

use App\Http\Controllers\CdController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\UsuarioController;
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
Route::get('/artistas-letra/{letra}',[SiteController::class, 'artistas_letra']);
Route::post('/recuperar-senha', [SiteController::class, 'recuperarSenha']);
Route::post('/verifica-token-recupera-senha', [SiteController::class, 'verificaTokenRecuperaSenha']);
Route::post('/confirmar-alteracao-senha', [SiteController::class, 'confirmarResetSenha']);

Route::get('/baixar-cd',[SiteController::class, 'baixarCd']);
Route::get('/conta-download-cd/{id}',[SiteController::class, 'contaDownloadCd']);
Route::get('/conta-play-cd/{id}',[SiteController::class, 'contaPlayCd']);

Route::post('/login', [LoginController::class, 'entrar']);
Route::post('/registrar', [UsuarioController::class, 'registrar']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    $user = $request->user();
    $user->append('foto', 'banner');
    return $user;
});

Route::middleware('auth:api')->group(function(){
    Route::post('/alterar-banner', [UsuarioController::class, 'alterarBanner']);
    Route::post('/alterar-foto', [UsuarioController::class, 'alterarFoto']);
    Route::post('/usuario-update', [UsuarioController::class, 'update']);
    Route::get('/usuario/cds', [UsuarioController::class, 'cds']);

    
    Route::post('/cds/upload/{cd_id}', [CdController::class, 'upload']);
    Route::resource('/cds', CdController::class);
});

//rotas de atualizações temporárias de implantação


// Route::group(function(){
    // Route::resource('/categorias',CategoriaController::class);

    // Route::resource('/banners',BannerController::class);
// });
