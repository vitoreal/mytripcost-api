<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\EnderecoController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\UsuarioController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Area de login e registro do site
Route::post('/login', [AuthController::class, 'login']);
Route::post('/registrar', [AuthController::class, 'registrar'])->name('registrar');
Route::post('/lembrar-senha', [AuthController::class, 'lembrarSenha'])->name('lembrarSenha');
Route::post('/reset-senha', [AuthController::class, 'resetSenha'])->name('resetSenha');
Route::post('/refreshToken', [AuthController::class, 'refresh']);


Route::group(['middleware' => ['jwt.auth']], function() {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/me', [AuthController::class, 'me']);
});


// STATUS CONTROLLER
Route::prefix('status')->middleware('jwt.auth')->group(function() {
    Route::get('/listar-status-total', [StatusController::class, 'listarTotalPagination']);
    Route::get('/listar-status/{startRow}/{limit}/{sortBy}', [StatusController::class, 'listarPagination']);
    Route::post('/salvar-status', [StatusController::class, 'salvar']);
    Route::post('/excluir-status', [StatusController::class, 'excluir']);
    Route::get('/buscar-status/{idStatus}', [StatusController::class, 'buscarStatus']);
});

// USUARIO CONTROLLER
Route::prefix('usuario')->middleware('jwt.auth')->group(function() {
    Route::get('/buscar-meus-dados', [UsuarioController::class, 'buscarMeusDados']);
    Route::post('/alterar-meus-dados', [UsuarioController::class, 'alterarDados']);
    Route::get('/buscar-dados-usuario/{idUser}', [UsuarioController::class, 'buscarUsuario']);
    Route::post('/alterar-senha', [UsuarioController::class, 'alterarSenha']);
    Route::get('/listar-usuario-total', [UsuarioController::class, 'listarTotalPagination']);
    Route::get('/listar-usuario/{startRow}/{limit}/{sortBy}', [UsuarioController::class, 'listarPagination']);
    Route::post('/excluir-usuario', [UsuarioController::class, 'excluir']);
});

Route::prefix('endereco')->middleware('jwt.auth')->group(function() {
    Route::get('/lista-estado', [EnderecoController::class, 'listaEstado']);
    Route::get('/lista-cidade/{idEstado}', [EnderecoController::class, 'listaCidade']);
});

// GOOGLE AUTH
Route::post('validaDadosLoginGoogle', [GoogleAuthController::class, 'validaDadosLoginGoogle']);
Route::get('redirect', [GoogleAuthController::class, 'redirect'])->name('google-redirect');
Route::get('callbackGoogle', [GoogleAuthController::class, 'callbackGoogle'])->name('google-callback');



