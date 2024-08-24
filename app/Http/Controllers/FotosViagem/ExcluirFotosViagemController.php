<?php

namespace App\Http\Controllers\FotosViagem;

use App\Http\Controllers\Controller;
use App\Models\FotoViagem;
use App\Models\Viagem;
use App\Repositories\FotoViagemRepository;
use App\Repositories\ViagemRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;
use Throwable;

class ExcluirFotosViagemController extends Controller
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

            $repoViagem = new ViagemRepository($this->viagem);

            $repository = new FotoViagemRepository($this->fotoViagem);

            $idFoto = $request->id;
            $idViagem = $request->idViagem;

            if($user->isAdmin()) {
                $viagem = $repoViagem->buscarPorId($idViagem);
            } else {
                $viagem = $repoViagem->buscarViagemPorUser($user->id, $idViagem);
            }

            if($viagem){

                $fotoViagem = $repository->buscarPorId($idFoto);

                Storage::delete($fotoViagem->foto);

                $diretorio = $this->diretorio.'/'.$user->id;

                $files = Storage::allFiles($diretorio);

                if(count($files) == 0){
                    Storage::deleteDirectory($diretorio);
                }

                $repository->excluir($idFoto);

                return response()->json(['type' => 'SUCESSO', 'mensagem' => 'Registro deletado com sucesso!'], Response::HTTP_OK);

            }

            return response()->json(['type' => 'ERROR', 'mensagem' => 'Você não tem permissão para deletar esse registro!'], Response::HTTP_BAD_REQUEST);

        }  catch (UserNotDefinedException | Throwable $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!', 'error' => $e->getMessage() ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);
        }

    }
}
