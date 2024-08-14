<?php

namespace App\Http\Controllers\ReportarBug;

use App\Http\Controllers\Controller;
use App\Models\ReportarBug;
use App\Repositories\ReportarBugRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ExcluirReportarBugController extends Controller
{
    public function __construct(ReportarBug $reportarBug, string $diretorio = 'images/foto-reportar-bug/'){
        $this->reportarBug = $reportarBug;
        $this->diretorio = $diretorio;
    }

     /**
     * Metodo para excluir
     *
     * @return response()
     */
    public function __invoke(Request $request)
    {

        try {

            $user = Auth::userOrFail();

            if($user->isAdmin()) {

                $repository = new ReportarBugRepository($this->reportarBug);

                $reportarBug = $repository->buscarPorId($request->id);

                if($reportarBug->foto != ''){
                    Storage::delete($reportarBug->foto);

                    $diretorio = $this->diretorio.'/'.$user->id;

                    $files = Storage::allFiles($diretorio);

                    if(count($files) == 0){
                        Storage::deleteDirectory($diretorio);
                    }

                }

                $repository->excluir($request->id);

                return response()->json(['type' => 'SUCESSO', 'mensagem' => 'Registro deletado com sucesso!'], Response::HTTP_OK);

            } else {
                $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Você não tem acesso a esta funcionalidade!', ];
                return response()->json($retorno, Response::HTTP_BAD_REQUEST);
            }

        }  catch (UserNotDefinedException | Throwable $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!', 'Error' => $e->getMessage() ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);
        }


    }
}
