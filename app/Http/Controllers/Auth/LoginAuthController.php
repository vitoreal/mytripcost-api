<?php

namespace App\Http\Controllers\Auth;

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

class LoginAuthController extends Controller
{

    public function __invoke(Request $request){
        
        $credencials = $request->only(['email', 'password']);
        $credencials['status_id'] = 1;

        $messages = array(
            'required' => 'O campo :attribute é obrigatório.',
            'email' => 'O campo :attribute é inválido.',
        );

        $validator = Validator::make($credencials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ], $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $token = Auth::attempt($credencials);

        if($token){

            $user = Auth::user();
            $user->roles[0];

            $retorno = [
                'type' => 'SUCESSO',
                'mensagem' => 'Login efetuado com sucesso!',
                'user' => $user,
                'authorization' => [
                    'token' => $token,
                    'type' => 'Bearer'
                ]
            ];

            return response()->json($retorno, Response::HTTP_OK);

        } else {
            return response()->json(['type' => 'ERROR', 'mensagem' => 'Não foi possível efetuar o login !'], Response::HTTP_FORBIDDEN);
        }

    }

}
