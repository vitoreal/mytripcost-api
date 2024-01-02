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
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Throwable;

class AuthController extends Controller
{

    /**
     * Registrar um novo usuario
     */
    public function registrar(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|between:2,100',
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

            $checkRole = Role::where('name', 'USER_PADRAO')->first();

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
            'password_confirmation' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['type' => 'ERROR', 'mensagem' => 'Não foi possível validar o email!'], 422);
        }

        $updatePassword = DB::table('password_reset_tokens')
                            ->where([
                            'email' => $request->email,
                            'token' => $request->token
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
    public function login(Request $request){

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

        //$token = auth('api')->attempt($credencials);
        $token = Auth::attempt($credencials);

        if($token){

            $user = Auth::user();
            $user->roles[0]; // Pegando as roles do usuário

            $retorno = [
                'type' => 'SUCESSO',
                'mensagem' => 'Login efetuado com sucesso!',
                'user' => $user,
                'authorization' => [
                    'token' => $token,
                    'type' => 'Bearer'
                ]
            ];

            return response()->json($retorno, 200);

        } else {
            return response()->json(['type' => 'ERROR', 'mensagem' => 'Não foi possível efetuar o login !'], 403);
        }

    }


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
