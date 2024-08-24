<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ResetSenhaAuthController extends Controller
{

    public function __invoke(Request $request)
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

}
