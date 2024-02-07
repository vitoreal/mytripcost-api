<?php

namespace App\Http\Controllers;

use App\Models\ReportarBug;
use App\Repositories\ReportarBugRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;
use Throwable;

class ReportarBugController extends Controller
{
    public function __construct(ReportarBug $reportarBug){
        $this->reportarBug = $reportarBug;
    }

    /**
     * Alterar dados do usuario
     */
    public function salvar(Request $request){

        try {

            $user = auth()->userOrFail();


            $validator = Validator::make($request->all(), [
                'titulo' => 'required|string|between:5,500',
                'descricao' => 'required|string|between:5,8000',
            ]);

            if ($validator->fails()) {
                return response()->json(['type' => 'ERROR', 'mensagem' => 'Não foi possível validar os campos!'], Response::HTTP_BAD_REQUEST);
            }

            // Alterando os dados do usuario
            $repository = new ReportarBugRepository($this->reportarBug);

            $reportarBug = new ReportarBug();

            $reportarBug->titulo = $request->titulo;
            $reportarBug->descricao = $request->descricao;
            $reportarBug->user_id = $user->id;

            $file = $request->foto;
            $base64 = base64_encode($file);
            $reportarBug->foto = $base64;

            $repository->salvar($reportarBug);

            if($repository === null){
                $retorno = ['type' => 'ERROR', 'mensagem' => 'Não foi possível cadastrar o dado!'];
                return response()->json($retorno, Response::HTTP_BAD_REQUEST);
            } else {

                $retorno = ['type' => 'SUCESSO', 'mensagem' => 'Registro cadastrado com sucesso!'];
                return response()->json($file, Response::HTTP_OK);
            }


        } catch (UserNotDefinedException | Throwable $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!', 'Error' => $e->getMessage() ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);

        }
    }
}
