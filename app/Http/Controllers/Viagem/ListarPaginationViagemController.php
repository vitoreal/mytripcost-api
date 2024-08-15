<?php

namespace App\Http\Controllers\Viagem;

use App\Http\Controllers\Controller;
use App\Models\FotoViagem;
use App\Models\Viagem;
use App\Repositories\ViagemRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class ListarPaginationViagemController extends Controller
{

    public function __construct(Viagem $viagem){
        $this->viagem = $viagem;
    }


    public function __invoke( string $startRow, string $limit, string $sortBy){

        try {

            $user = auth()->userOrFail();

            $repository = new ViagemRepository($this->viagem);

            if($user->isAdmin()) {
                $lista = $repository->listarPagination($startRow, $limit, $sortBy, 'id');
            } else {
                $lista = $repository->listarPaginationViagem($startRow, $limit, $sortBy, 'id', $user->id);
            }

            foreach ($lista['lista'] as $key => $value) {

                $lista['lista'][$key]->orcamento = number_format($value->orcamento,2,",",".");;
                $lista['lista'][$key]->data_inicio = date("d/m/Y", strtotime($value->data_inicio));
                $lista['lista'][$key]->data_fim = date("d/m/Y", strtotime($value->data_fim));

                if($lista['lista'][$key]->foto){

                    $files = Storage::get($lista['lista'][$key]->foto);

                    $base64 = base64_encode($files);
                    $lista['lista'][$key]->foto = 'data:image/jpeg;base64,'.$base64;
                }

            }

            $retorno = ['lista' => $lista ];
            return response()->json($retorno, Response::HTTP_OK);

        } catch (UserNotDefinedException | UnauthorizedHttpException | Throwable $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!', 'error' => $e->getMessage() ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);

        }

    }

}
