<?php
use Illuminate\Support\Facades\Route;
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
use App\Http\Controllers\Contato\EnviarEmailContatoController;
use App\Http\Controllers\Despesa\BuscarDespesaController;
use App\Http\Controllers\Despesa\ExcluirDespesaController;
use App\Http\Controllers\Despesa\ListarPaginationDespesaController;
use App\Http\Controllers\Despesa\SalvarDespesaController;
use App\Http\Controllers\Endereco\ListaCidadeController;
use App\Http\Controllers\Endereco\ListaEstadoController;
use App\Http\Controllers\FotosViagem\ExcluirFotosViagemController;
use App\Http\Controllers\FotosViagem\ListarPaginationFotosViagemController;
use App\Http\Controllers\FotosViagem\SalvarFotosViagemController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\MetodoPagamento\BuscarMetodoPagamentoController;
use App\Http\Controllers\MetodoPagamento\ExcluirMetodoPagamentoController;
use App\Http\Controllers\MetodoPagamento\ListarMetodoPagamentoController;
use App\Http\Controllers\MetodoPagamento\ListarPaginationMetodoPagamentoController;
use App\Http\Controllers\MetodoPagamento\SalvarMetodoPagamentoController;
use App\Http\Controllers\Moeda\ListarMoedaController;
use App\Http\Controllers\ReportarBug\BuscarReportarBugController;
use App\Http\Controllers\ReportarBug\ExcluirReportarBugController;
use App\Http\Controllers\ReportarBug\ListarPaginationReportarBugController;
use App\Http\Controllers\ReportarBug\SalvarReportarBugController;
use App\Http\Controllers\Status\BuscarStatusController;
use App\Http\Controllers\Status\ExcluirStatusController;
use App\Http\Controllers\Status\ListarPaginationStatusController;
use App\Http\Controllers\Status\SalvarStatusController;
use App\Http\Controllers\TipoPrivacidade\ListarTipoPrivacidadeController;
use App\Http\Controllers\Usuario\AlterarSenhaUsuarioController;
use App\Http\Controllers\Usuario\AlterarUsuarioController;
use App\Http\Controllers\Usuario\BuscarMeusDadosUsuarioController;
use App\Http\Controllers\Usuario\BuscarUsuarioController;
use App\Http\Controllers\Usuario\ExcluirUsuarioController;
use App\Http\Controllers\Usuario\ListarPaginationUsuarioController;
use App\Http\Controllers\Viagem\BuscarViagemController;
use App\Http\Controllers\Viagem\ExcluirViagemController;
use App\Http\Controllers\Viagem\ListarPaginationViagemController;
use App\Http\Controllers\Viagem\SalvarViagemController;

// Area de login e registro do site
Route::post('/login', LoginAuthController::class);
Route::post('/registrar', RegistrarAuthController::class);
Route::post('/lembrar-senha', LembrarSenhaAuthController::class);
Route::post('/reset-senha', ResetSenhaAuthController::class);
Route::post('/refreshToken', RefreshTokenAuthController::class);
Route::post('/contato/enviar-email', EnviarEmailContatoController::class);

Route::group(['middleware' => ['jwt.auth']], function() {
    Route::post('/logout', LogoutAuthController::class);
});

// STATUS CONTROLLER
Route::prefix('status')->middleware('jwt.auth')->group(function() {
    Route::get('/listar-status/{startRow}/{limit}/{sortBy}', ListarPaginationStatusController::class);
    Route::post('/salvar-status', SalvarStatusController::class);
    Route::post('/excluir-status', ExcluirStatusController::class);
    Route::get('/buscar-status/{id}', BuscarStatusController::class);
});

// USUARIO CONTROLLER
Route::prefix('usuario')->middleware('jwt.auth')->group(function() {
    Route::get('/buscar-meus-dados', BuscarMeusDadosUsuarioController::class);
    Route::post('/alterar-meus-dados', AlterarUsuarioController::class);
    Route::get('/buscar-dados-usuario/{idUser}', BuscarUsuarioController::class);
    Route::post('/alterar-senha', AlterarSenhaUsuarioController::class);
    Route::get('/listar-usuario/{startRow}/{limit}/{sortBy}', ListarPaginationUsuarioController::class);
    Route::post('/excluir-usuario', ExcluirUsuarioController::class);
});

// Endereco controller
Route::prefix('endereco')->middleware('jwt.auth')->group(function() {
    Route::get('/lista-estado', ListaEstadoController::class);
    Route::get('/lista-cidade/{idEstado}', ListaCidadeController::class);
});

// VIAGEM CONTROLLER | FOTOS VIAGEM CONTROLLER

Route::prefix('viagem')->middleware('jwt.auth')->group(function() {

    // VIAGEM CONTROLLER

    Route::get('/listar-viagem/{startRow}/{limit}/{sortBy}', ListarPaginationViagemController::class);
    Route::post('/salvar-viagem', SalvarViagemController::class);
    Route::post('/excluir-viagem', ExcluirViagemController::class);
    Route::get('/buscar-viagem/{id}', BuscarViagemController::class);

    // FOTOS VIAGEM

    Route::post('/salvar-foto-viagem', SalvarFotosViagemController::class);
    Route::post('/excluir-foto-viagem', ExcluirFotosViagemController::class);
   // Route::get('/foto-viagem/{id}', [FotosViagemController::class, 'listar']);
    Route::get('/listar-foto-viagem/{idFoto}/{startRow}/{limit}/{sortBy}', ListarPaginationFotosViagemController::class);
    
});

// VIAGEM CONTROLLER
Route::prefix('despesa')->middleware('jwt.auth')->group(function() {
    Route::get('/listar-despesa/{idViagem}/{startRow}/{limit}/{sortBy}', ListarPaginationDespesaController::class);
    Route::post('/salvar-despesa', SalvarDespesaController::class);
    Route::post('/excluir-despesa', ExcluirDespesaController::class);
    Route::get('/buscar-despesa/{id}', BuscarDespesaController::class);
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
    Route::get('/listar-reportar-bug/{startRow}/{limit}/{sortBy}', ListarPaginationReportarBugController::class);
    Route::post('/salvar-reportar-bug', SalvarReportarBugController::class);
    Route::post('/excluir-reportar-bug', ExcluirReportarBugController::class);
    Route::get('/buscar-reportar-bug/{id}', BuscarReportarBugController::class);

    // Moeda
    Route::get('/listar-moeda', ListarMoedaController::class);

    // Tipo Privacidade
    Route::get('/listar-tipo-privacidade', ListarTipoPrivacidadeController::class);

});

// GOOGLE AUTH
Route::post('validaDadosLoginGoogle', [GoogleAuthController::class, 'validaDadosLoginGoogle']);
Route::get('redirect', [GoogleAuthController::class, 'redirect'])->name('google-redirect');
Route::get('callbackGoogle', [GoogleAuthController::class, 'callbackGoogle'])->name('google-callback');



