<?php

namespace App\Http\Controllers\ReportarBug;

use App\Http\Controllers\Controller;
use App\Models\ReportarBug;
use App\Repositories\ReportarBugRepository;
use Illuminate\Http\Response;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;
use Illuminate\Support\Facades\Storage;
use Throwable;

class BuscarReportarBugController extends Controller
{
    public function __construct(ReportarBug $reportarBug){
        $this->reportarBug = $reportarBug;
    }

    // Busca metodoPagamento por id - somente admin e root tem acesso
    public function __invoke(int $id){

        try {

            $user = auth()->userOrFail();

            if($user->isAdmin()) {

                $repository = new ReportarBugRepository($this->reportarBug);

                $result = $repository->buscarPorId($id);

                if($result->foto){
                    $files = Storage::get($result->foto);

                    $base64 = base64_encode($files);
                    $result->foto = 'data:image/jpeg;base64,'.$base64;
                }    
                $retorno = [
                    'result' => $result
                ];

                return response()->json($retorno, 200);
            } else {
                $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Você não tem acesso a esta funcionalidade!', ];
                return response()->json($retorno, Response::HTTP_BAD_REQUEST);
            }

        } catch (UserNotDefinedException | Throwable $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!', 'Error' => $e->getMessage() ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);

        }
    }

}
