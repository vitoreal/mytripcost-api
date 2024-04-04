<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\DespesaController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\EnderecoController;
use App\Http\Controllers\FotosViagemController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\MetodoPagamentoController;
use App\Http\Controllers\MoedaController;
use App\Http\Controllers\PlanosController;
use App\Http\Controllers\ReportarBugController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ViagemController;

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
    Route::get('/buscar-status/{id}', [StatusController::class, 'buscarPorId']);
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

// Endereco controller
Route::prefix('endereco')->middleware('jwt.auth')->group(function() {
    Route::get('/lista-estado', [EnderecoController::class, 'listaEstado']);
    Route::get('/lista-cidade/{idEstado}', [EnderecoController::class, 'listaCidade']);
});

// VIAGEM CONTROLLER | FOTOS VIAGEM CONTROLLER

Route::prefix('viagem')->middleware('jwt.auth')->group(function() {

    // VIAGEM CONTROLLER

    Route::get('/listar-viagem-total', [ViagemController::class, 'listarTotalPagination']);
    Route::get('/listar-viagem/{startRow}/{limit}/{sortBy}', [ViagemController::class, 'listarPagination']);
    Route::post('/salvar-viagem', [ViagemController::class, 'salvar']);
    Route::post('/excluir-viagem', [ViagemController::class, 'excluir']);
    Route::get('/buscar-viagem/{id}', [ViagemController::class, 'buscarPorId']);

    // FOTOS VIAGEM

    Route::post('/salvar-foto-viagem', [FotosViagemController::class, 'salvar']);
    Route::post('/excluir-foto-viagem', [FotosViagemController::class, 'excluir']);
    Route::get('/foto-viagem/{id}', [FotosViagemController::class, 'listar']);
    Route::get('/listar-foto-viagem-total/{idFoto}', [FotosViagemController::class, 'listarTotalPagination']);
    Route::get('/listar-foto-viagem/{idFoto}/{startRow}/{limit}/{sortBy}', [FotosViagemController::class, 'listarPagination']);
});

// VIAGEM CONTROLLER
Route::prefix('despesa')->middleware('jwt.auth')->group(function() {
    Route::get('/listar-despesa-total', [DespesaController::class, 'listarTotalPagination']);
    Route::get('/listar-despesa/{startRow}/{limit}/{sortBy}', [DespesaController::class, 'listarPagination']);
    Route::post('/salvar-despesa', [DespesaController::class, 'salvar']);
    Route::post('/excluir-despesa', [DespesaController::class, 'excluir']);
    Route::get('/buscar-despesa/{id}', [DespesaController::class, 'buscarPorId']);
});


// Configuracao controller
Route::prefix('config')->middleware('jwt.auth')->group(function() {

    // Categoria
    Route::get('/listar-categoria-total', [CategoriaController::class, 'listarTotalPagination']);
    Route::get('/listar-categoria/{startRow}/{limit}/{sortBy}', [CategoriaController::class, 'listarPagination']);
    Route::post('/salvar-categoria', [CategoriaController::class, 'salvar']);
    Route::post('/excluir-categoria', [CategoriaController::class, 'excluir']);
    Route::get('/buscar-categoria/{id}', [CategoriaController::class, 'buscarPorId']);

    // Metodos de Pagamento
    Route::get('/listar-metodo-pagamento-total', [MetodoPagamentoController::class, 'listarTotalPagination']);
    Route::get('/listar-metodo-pagamento/{startRow}/{limit}/{sortBy}', [MetodoPagamentoController::class, 'listarPagination']);
    Route::post('/salvar-metodo-pagamento', [MetodoPagamentoController::class, 'salvar']);
    Route::post('/excluir-metodo-pagamento', [MetodoPagamentoController::class, 'excluir']);
    Route::get('/buscar-metodo-pagamento/{id}', [MetodoPagamentoController::class, 'buscarPorId']);

    // Reportar BUG
    Route::get('/listar-reportar-bug-total', [ReportarBugController::class, 'listarTotalPagination']);
    Route::get('/listar-reportar-bug/{startRow}/{limit}/{sortBy}', [ReportarBugController::class, 'listarPagination']);
    Route::post('/salvar-reportar-bug', [ReportarBugController::class, 'salvar']);
    Route::post('/excluir-reportar-bug', [ReportarBugController::class, 'excluir']);
    Route::get('/buscar-reportar-bug/{id}', [ReportarBugController::class, 'buscarPorId']);

    // Moeda
    Route::get('/listar-moeda', [MoedaController::class, 'listarMoeda']);

});

// GOOGLE AUTH
Route::post('validaDadosLoginGoogle', [GoogleAuthController::class, 'validaDadosLoginGoogle']);
Route::get('redirect', [GoogleAuthController::class, 'redirect'])->name('google-redirect');
Route::get('callbackGoogle', [GoogleAuthController::class, 'callbackGoogle'])->name('google-callback');



