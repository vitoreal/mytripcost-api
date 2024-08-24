<?php

namespace App\Http\Controllers\FotosViagem;

use App\Http\Controllers\Controller;
use App\Models\FotoViagem;
use App\Models\Viagem;
use App\Repositories\FotoViagemRepository;
use App\Repositories\ViagemRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class ListarPaginationFotosViagemController extends Controller
{
    public function __construct(Viagem $viagem, FotoViagem $fotoViagem, string $diretorio = 'images/foto-viagem/'){
        $this->viagem = $viagem;
        $this->fotoViagem = $fotoViagem;
        $this->diretorio = $diretorio;
    }

    public function __invoke( int $idViagem, string $startRow, string $limit, string $sortBy){

        try {

            $user = auth()->userOrFail();

            $repoViagem = new ViagemRepository($this->viagem);
            $viagem = $repoViagem->buscarPorId($idViagem);

            $repository = new FotoViagemRepository($this->fotoViagem);

            if(($viagem->user_id == $user->id) || $user->isAdmin()){
                $lista = $repository->listarPaginationFotoViagem($idViagem, $startRow, $limit, $sortBy, 'id');

                foreach ($lista['lista'] as $key => $value) {
                        $file = Storage::get($value->foto);
                        $base64 = base64_encode($file);
                        $lista['lista'][$key]->foto = 'data:'.$value->mimetype.';base64,'.$base64;
                }

                $retorno = ['lista' => $lista ];
                return response()->json($retorno, Response::HTTP_OK);
            }

            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Você não tem permissão para essa ação!'];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);

        } catch (UserNotDefinedException | UnauthorizedHttpException | Throwable $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!', 'error' => $e->getMessage() ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);

        }

    }

}
