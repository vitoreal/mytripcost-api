<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Notifications\ResetPasswordNotification;
use App\Models\User;
use Carbon\Carbon;

class LembrarSenhaAuthController extends Controller
{

    
    /**
     * Envia link para o usuario resetar a senha
     */
    public function __invoke(Request $request)
    {

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

}
