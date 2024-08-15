<?php

namespace App\Http\Controllers\Viagem;

use App\Http\Controllers\Controller;
use App\Models\FotoViagem;
use Illuminate\Http\Request;
use App\Models\Viagem;
use App\Repositories\FotoViagemRepository;
use App\Repositories\ViagemRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;
use Throwable;

class ExcluirViagemController extends Controller
{

    public function __construct(Viagem $viagem, FotoViagem $fotoViagem, string $diretorio = 'images/foto-viagem/'){
        $this->viagem = $viagem;
        $this->fotoViagem = $fotoViagem;
        $this->diretorio = $diretorio;
    }

    public function __invoke(Request $request)
    {

        try {

            $user = auth()->userOrFail();

            $repository = new ViagemRepository($this->viagem);

            $idViagem = $request->id;

            if($user->isAdmin()) {
                $viagem = $repository->buscarPorId($idViagem);
            } else {
                $viagem = $repository->buscarViagemPorUser($user->id, $idViagem);

            }

            if($viagem){

                $repoFotoViagem = new FotoViagemRepository($this->fotoViagem);

                if($viagem->foto != ''){

                    $fotoViagem = $repoFotoViagem->buscarPorIdViagem($idViagem);

                    if($fotoViagem){
                        foreach ($fotoViagem as $key => $value) {

                            $repoFotoViagem->excluir($value->id);
                            Storage::delete($value->foto);
                        }
                    }

                    Storage::delete($viagem->foto);

                    $diretorio = $this->diretorio.'/'.$user->id;

                    $files = Storage::allFiles($diretorio);

                    if(count($files) == 0){
                        Storage::deleteDirectory($diretorio);
                    }

                }

                $repository->excluir($idViagem);

                return response()->json(['type' => 'SUCESSO', 'mensagem' => 'Registro deletado com sucesso!'], Response::HTTP_OK);

            }

            return response()->json(['type' => 'ERROR', 'mensagem' => 'Você não tem permissão para deletar esse registro!'], Response::HTTP_BAD_REQUEST);

        }  catch (UserNotDefinedException | Throwable $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!', 'error' => $e->getMessage() ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);
        }

    }

}
