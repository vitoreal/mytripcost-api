<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use App\Notifications\ResetPasswordNotification;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Response;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Throwable;

class AuthController extends Controller
{

    
    /**
     * Envia link para o usuario resetar a senha
     */
    public function lembrarSenha(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'urlRetorno' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['type' => 'ERROR', 'mensagem' => 'Não foi possível validar o email!'], 422);
        }

        $token = Str::random(64);

        $urlRetornoToken = $request->urlRetorno ."/". $token;

        $user = User::where('email', $request->email)->first();

        if ($user){

            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]);


           $userSend = new User();
           $userSend->email = $user->email;
           $userSend->name = $user->name;

           $userSend->notify(new ResetPasswordNotification($urlRetornoToken, $userSend));

            return response()->json(['type' => 'SUCESSO', 'mensagem' => 'Foi enviado uma mensagem de recuperação de senha para o seu email!'], 200);

        } else {
            return response()->json(['type' => 'ERROR', 'mensagem' => 'Não foi possível localizar o email!'], 422);
        }

    }

    /**
     * Metodo para alterar a senha
     *
     * @return response()
     */
    public function resetSenha(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
            'tokenEmail' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['type' => 'ERROR', 'mensagem' => 'Não foi possível validar o email!'], 422);
        }

        $updatePassword = DB::table('password_reset_tokens')
                            ->where([
                            'email' => $request->email,
                            'token' => $request->tokenEmail
                            ])
                            ->first();
       
        if(!$updatePassword){
            return response()->json(['type' => 'ERROR', 'mensagem' => 'Não foi possível alterar a senha, favor enviar novamente o email de alteração!'], 422);
        }

        $user = User::where('email', $request->email)
                    ->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset_tokens')->where(['email'=> $request->email])->delete();

        return response()->json(['type' => 'SUCESSO', 'mensagem' => 'Sua senha foi modificada! Favor tentar efetuar o login novamente.'], 200);
    }

    //
    


    /**
     * Metodo para sair do sistema
     */
    public function logout(){

        //Auth::guard('api')->check();

        //auth('api')->logout();
        Auth::logout();
        /*
        Auth::user()->tokens->each(function($token, $key) {
            $token->delete();
        });
        */

        $retorno = [
            'type' => 'SUCESSO',
            'mensagem' => 'Logout efetuado com sucesso!',
        ];

        return response()->json($retorno, 200);

    }

    public function refresh(){

        $token = Auth::refresh();

        $retorno = [
            'type' => 'SUCESSO',
            'mensagem' => 'Token atualizado com sucesso!',
            'authorization' => [
                'token' => $token,
                'type' => 'Bearer'
            ]
        ];

        return response()->json($retorno, 200);
    }

    public function me(){
        return response()->json(Auth::user());
    }
}
