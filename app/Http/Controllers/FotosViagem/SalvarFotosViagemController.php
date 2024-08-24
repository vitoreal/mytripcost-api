<?php

namespace App\Http\Controllers\FotosViagem;

use App\Http\Controllers\Controller;
use App\Models\FotoViagem;
use App\Models\Viagem;
use App\Repositories\FotoViagemRepository;
use App\Repositories\ViagemRepository;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;

class SalvarFotosViagemController extends Controller
{
    public function __construct(Viagem $viagem, FotoViagem $fotoViagem, string $diretorio = 'images/foto-viagem/'){
        $this->viagem = $viagem;
        $this->fotoViagem = $fotoViagem;
        $this->diretorio = $diretorio;
    }

    public function __invoke(Request $request){

        try {

            $user = auth()->userOrFail();

            $rules = [
                'foto' => 'required|mimes:jpeg,jpg,png,gif,webp|max:8000',
                'idViagem' => 'required',
            ];

            $messages = [
                'foto.required' => 'Campo foto é o obrigatório',
                'foto.max' => 'Campo foto tem que ter no máximo 8MB',
                'foto.mimes' => 'Campo foto tem estar no formato: jpeg, jpg, png, gif.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {

                $errors = $validator->errors();

                $retorno = [
                            'type' => 'ERROR',
                            'mensagem' => $errors->all()[0],
                            ];
                return response()->json($retorno, Response::HTTP_BAD_REQUEST);
            }

            // Verifica se o id do usuario que esta salvando a foto é o mesmo do usuario da viagem

            $viagemRepo = new ViagemRepository($this->viagem);
            $viagem = $viagemRepo->buscarPorId($request->idViagem);

            if ($viagem->user_id == $user->id){

                $repository = new FotoViagemRepository($this->fotoViagem);

                $fotoViagem = new FotoViagem();

                if($request->foto){

                    $path = $request->file('foto')->store(
                        $this->diretorio.$user->id
                    );

                    $fotoViagem->foto = $path;

                }

                $fotoViagem->id_viagem = $request->idViagem;
                $fotoViagem->mimetype = Storage::mimeType($path);
                $fotoViagem->extension = $request->file('foto')->getClientOriginalExtension();

                $repository->salvar($fotoViagem);

                if($repository === null){
                    $retorno = ['type' => 'ERROR', 'mensagem' => 'Não foi possível cadastrar o dado!' ];
                    return response()->json($retorno, Response::HTTP_BAD_REQUEST);
                } else {

                    $retorno = ['type' => 'SUCESSO', 'mensagem' => 'Registro cadastrado com sucesso!'];
                    return response()->json($retorno, Response::HTTP_OK);
                }
            } else {
                $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Você não tem autorização para realizar essa operação!'];
                return response()->json($retorno, Response::HTTP_BAD_REQUEST);
            }


        } catch (UserNotDefinedException | QueryException | Exception $e ) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Não foi possível realizar a sua solicitação!', 'error' => $fotoViagem];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);

        }
    }

}
