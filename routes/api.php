<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\LembrarSenhaAuthController;
use App\Http\Controllers\Auth\LoginAuthController;
use App\Http\Controllers\Auth\LogoutAuthController;
use App\Http\Controllers\Auth\RefreshTokenAuthController;
use App\http\Controllers\Auth\RegistrarAuthController;
use App\Http\Controllers\Auth\ResetSenhaAuthController;
use App\Http\Controllers\Categoria\BuscarCategoriaController;
use App\Http\Controllers\Categoria\ExcluirCategoriaController;
use App\Http\Controllers\Categoria\ListarCategoriaController;
use App\Http\Controllers\Categoria\ListarPaginationCategoriaController;
use App\Http\Controllers\Categoria\SalvarCategoriaController;
use App\Http\Controllers\DespesaController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\EnderecoController;
use App\Http\Controllers\FotosViagemController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\MetodoPagamento\BuscarMetodoPagamentoController;
use App\Http\Controllers\MetodoPagamento\ExcluirMetodoPagamentoController;
use App\Http\Controllers\MetodoPagamento\ListarMetodoPagamentoController;
use App\Http\Controllers\MetodoPagamento\ListarPaginationMetodoPagamentoController;
use App\Http\Controllers\MetodoPagamento\SalvarMetodoPagamentoController;
use App\Http\Controllers\MetodoPagamentoController;
use App\Http\Controllers\MoedaController;
use App\Http\Controllers\ReportarBugController;
use App\Http\Controllers\TipoPrivacidadeController;
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
Route::post('/login', LoginAuthController::class);
Route::post('/registrar', RegistrarAuthController::class);
Route::post('/lembrar-senha', LembrarSenhaAuthController::class);
Route::post('/reset-senha', ResetSenhaAuthController::class);
Route::post('/refreshToken', RefreshTokenAuthController::class);


Route::group(['middleware' => ['jwt.auth']], function() {
    Route::post('/logout', LogoutAuthController::class);
    Route::post('/me', [AuthController::class, 'me']);
});


// STATUS CONTROLLER
Route::prefix('status')->middleware('jwt.auth')->group(function() {
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

    Route::get('/listar-viagem/{startRow}/{limit}/{sortBy}', [ViagemController::class, 'listarPagination']);
    Route::post('/salvar-viagem', [ViagemController::class, 'salvar']);
    Route::post('/excluir-viagem', [ViagemController::class, 'excluir']);
    Route::get('/buscar-viagem/{id}', [ViagemController::class, 'buscarPorId']);

    // FOTOS VIAGEM

    Route::post('/salvar-foto-viagem', [FotosViagemController::class, 'salvar']);
    Route::post('/excluir-foto-viagem', [FotosViagemController::class, 'excluir']);
    Route::get('/foto-viagem/{id}', [FotosViagemController::class, 'listar']);
    Route::get('/listar-foto-viagem/{idFoto}/{startRow}/{limit}/{sortBy}', [FotosViagemController::class, 'listarPagination']);
});

// VIAGEM CONTROLLER
Route::prefix('despesa')->middleware('jwt.auth')->group(function() {
    Route::get('/listar-despesa/{idViagem}/{startRow}/{limit}/{sortBy}', [DespesaController::class, 'listarPagination']);
    Route::post('/salvar-despesa', [DespesaController::class, 'salvar']);
    Route::post('/excluir-despesa', [DespesaController::class, 'excluir']);
    Route::get('/buscar-despesa/{id}', [DespesaController::class, 'buscarPorId']);
});


// Configuracao controller
Route::prefix('config')->middleware('jwt.auth')->group(function() {

    // Categoria
    Route::get('/listar-categoria/{startRow}/{limit}/{sortBy}', ListarPaginationCategoriaController::class);
    Route::post('/salvar-categoria', SalvarCategoriaController::class);
    Route::post('/excluir-categoria', ExcluirCategoriaController::class);
    Route::get('/buscar-categoria/{id}', BuscarCategoriaController::class);
    Route::get('/listar-categoria', ListarCategoriaController::class);

    // Metodos de Pagamento
    Route::get('/listar-metodo-pagamento/{startRow}/{limit}/{sortBy}', ListarPaginationMetodoPagamentoController::class);
    Route::post('/salvar-metodo-pagamento', SalvarMetodoPagamentoController::class);
    Route::post('/excluir-metodo-pagamento', ExcluirMetodoPagamentoController::class);
    Route::get('/buscar-metodo-pagamento/{id}', BuscarMetodoPagamentoController::class);
    Route::get('/listar-metodo-pagamento', ListarMetodoPagamentoController::class);

    // Reportar BUG
    Route::get('/listar-reportar-bug/{startRow}/{limit}/{sortBy}', [ReportarBugController::class, 'listarPagination']);
    Route::post('/salvar-reportar-bug', [ReportarBugController::class, 'salvar']);
    Route::post('/excluir-reportar-bug', [ReportarBugController::class, 'excluir']);
    Route::get('/buscar-reportar-bug/{id}', [ReportarBugController::class, 'buscarPorId']);

    // Moeda
    Route::get('/listar-moeda', [MoedaController::class, 'listarMoeda']);

    // Tipo Privacidade
    Route::get('/listar-tipo-privacidade', [TipoPrivacidadeController::class, 'listar']);

});

// GOOGLE AUTH
Route::post('validaDadosLoginGoogle', [GoogleAuthController::class, 'validaDadosLoginGoogle']);
Route::get('redirect', [GoogleAuthController::class, 'redirect'])->name('google-redirect');
Route::get('callbackGoogle', [GoogleAuthController::class, 'callbackGoogle'])->name('google-callback');



