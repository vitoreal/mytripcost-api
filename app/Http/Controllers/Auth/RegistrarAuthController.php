<?php

namespace App\Http\Controllers\Auth;

use App\Enums\RolesEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use App\Notifications\ResetPasswordNotification;
use App\Models\Role;
use \App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Response;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Throwable;

class RegistrarAuthController extends Controller
{

    /**
     * Registrar um novo usuario
     */
    public function __invoke(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,200',
            'email' => 'required|string|email|between:2,200',
            'telefone' => 'required|string|min:14|max:15',
            'password' => 'required|string|confirmed|min:6',
            'password_confirmation' => 'required',
        ]);

        if ($validator->fails()) {

            //return response()->json($validator->errors(), 422);
            return response()->json(['type' => 'ERROR', 'mensagem' => 'Não foi possível validar os campos!'], 422);
        }

        $exception =  DB::transaction(function() use ($request) {

            $msgUserExiste = 'Este usuário já está cadastrado!';

            $checkUser = User::where('email', $request->email)->first();

            if($checkUser){
                $retorno = ['type' => 'ERROR', 'mensagem' => $msgUserExiste];
                return response()->json($retorno, 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'telefone' => $request->telefone,
                'password' => bcrypt($request->password),
                'status_id' => 1,
            ]);
            
            $checkRole = Role::where('name', RolesEnum::USER_BASICO)->first();

            $roleUser = array(
                ['role_id' => $checkRole->id, 'user_id' => $user->id],
            );

            DB::table('role_user')->insert($roleUser);

        });

        if($exception){
            return response()->json($exception->original, 422);
        } else {

            return $retorno = [
                'type' => 'SUCESSO',
                'mensagem' => 'Cadastro efetuado com sucesso!',
            ];

            return response()->json($retorno, 200);
        }


    }

}
