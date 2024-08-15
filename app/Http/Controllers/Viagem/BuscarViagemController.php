<?php
namespace App\Http\Controllers\Viagem;

use App\Http\Controllers\Controller;
use App\Models\FotoViagem;
use App\Models\Viagem;
use App\Repositories\ViagemRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;
use Throwable;

class BuscarViagemController extends Controller
{

    public function __construct(Viagem $viagem, FotoViagem $fotoViagem){
        $this->viagem = $viagem;
        $this->fotoViagem = $fotoViagem;
    }

    public function __invoke(int $id){

        try {

            $user = auth()->userOrFail();

            $repository = new ViagemRepository($this->viagem);

            if($user->isAdmin()) {
                $result = $repository->buscarPorId($id);
            } else {
                $result = $repository->buscarViagemPorId($id);
            }

            if($result->foto){
                $files = Storage::get($result->foto);

                $base64 = base64_encode($files);
                $result->foto = 'data:image/jpeg;base64,'.$base64;
            }
            $retorno = [
                'result' => $result
            ];

            return response()->json($retorno, 200);

        }  catch (UserNotDefinedException | Throwable $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!', 'error' => $result ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);
        }
    }

}
