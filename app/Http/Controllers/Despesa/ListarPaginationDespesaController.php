<?php

namespace App\Http\Controllers\Despesa;

use App\Http\Controllers\Controller;
use App\Models\Despesa;
use App\Models\Viagem;
use App\Repositories\DespesaRepository;
use App\Repositories\ViagemRepository;
use Illuminate\Http\Response;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class ListarPaginationDespesaController extends Controller
{

    public function __construct(Despesa $despesa, Viagem $viagem){
        $this->viagem = $viagem;
        $this->despesa = $despesa;
    }

    
    public function __invoke( int $idViagem, string $startRow, string $limit, string $sortBy){

        try {

            $user = auth()->userOrFail();

            $repository = new DespesaRepository($this->despesa);
            $repoViagem = new ViagemRepository($this->viagem);

            // Verificar se a viagem em questao eh a do usuario
            $viagem = $repoViagem->buscarPorId($idViagem);

            if($viagem->user_id == $user->id){
                $lista = $repository->listarPaginationDespesaViagem($idViagem, $startRow, $limit, $sortBy, 'id');

                foreach ($lista['lista'] as $key => $value) {

                    $lista['lista'][$key]->valor = number_format($value->valor,2,",",".");;
                    $lista['lista'][$key]->data_despesa = date("d/m/Y", strtotime($value->data_despesa));

                }

                $retorno = ['lista' => $lista ];
                return response()->json($retorno, Response::HTTP_OK);
            }

            $result = [
                'total' => 0,
                'lista' => []
            ];

            $retorno = [ 'lista' => $result, 'type' => 'ERROR', 'mensagem' => 'Você não tem permissão para essa ação!'];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);

        } catch (UserNotDefinedException | UnauthorizedHttpException | Throwable $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!', 'error' => $e->getMessage() ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);

        }

    }

}
