<?php

namespace App\Http\Controllers\ReportarBug;

use App\Http\Controllers\Controller;
use App\Models\ReportarBug;
use App\Repositories\ReportarBugRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;
use Throwable;

class SalvarReportarBugController extends Controller
{
    public function __construct(ReportarBug $reportarBug, string $diretorio = 'images/foto-reportar-bug/'){
        $this->reportarBug = $reportarBug;
        $this->diretorio = $diretorio;
    }

    /**
     * Alterar dados do usuario
     */
    public function __invoke(Request $request){

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

            if($request->foto){
                //$file = base64_encode(file_get_contents($request->foto->path()));

                $path = $request->file('foto')->store(
                    $this->diretorio.$user->id
                );

                $reportarBug->foto = $path;
            }

            $repository->salvar($reportarBug);
/*
            $file_extension = $file->getClientOriginalName();
            $destination_path = public_path() . '/folder/images/';
            $filename = $file_extension;
            $file->move($destination_path, $filename);
*/
            if($repository === null){
                $retorno = ['type' => 'ERROR', 'mensagem' => 'Não foi possível cadastrar o dado!'];
                return response()->json($retorno, Response::HTTP_BAD_REQUEST);
            } else {
                $retorno = ['type' => 'SUCESSO', 'mensagem' => 'Registro cadastrado com sucesso!'];
                return response()->json($retorno, Response::HTTP_OK);
            }

        } catch (UserNotDefinedException | Throwable $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!', 'Error' => $e->getMessage() ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);

        }
    }

}
