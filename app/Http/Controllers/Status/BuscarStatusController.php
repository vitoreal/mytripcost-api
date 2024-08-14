<?php

namespace App\Http\Controllers\Status;

use App\Http\Controllers\Controller;
use App\Models\Status;
use App\Repositories\StatusRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class BuscarStatusController extends Controller
{

    public function __construct(Status $status){
        $this->status = $status;
    }

    // Busca por id - somente admin e root tem acesso
    public function __invoke(int $id){

        $user = Auth::user();

        if($user->isAdmin()) {

            $statusRepo = new StatusRepository($this->status);

            $result = $statusRepo->buscarPorId($id);

            $retorno = [
                'result' => $result
            ];

            return response()->json($retorno, Response::HTTP_OK);
        } else {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Você não tem acesso a esta funcionalidade!', ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);
        }
    }

}
