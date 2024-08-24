<?php

namespace App\Http\Controllers\Contato;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use App\Notifications\EmailContatoNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class EnviarEmailContatoController extends Controller
{

    /**
     * Envia link para o usuario resetar a senha
     */
    public function __invoke(Request $request)
    {

        $rules = [
            'email' => 'required|email',
            'nome' => 'required',
            'assunto' => 'required',
            'mensagem' => 'required',
        ];

        $messages = [
            'email.required' => 'Campo nome é o obrigatório',
            'email.email' => 'Campo email inválido',
            'nome.required' => 'Campo nome é o obrigatório',
            'assunto.required' => 'Campo assunto é o obrigatório',
            'mensagem.required' => 'Campo mensagem é o obrigatório',
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
        try {
            Notification::route('mail', env('APP_EMAIL'))->notify(new EmailContatoNotification($request));
            return response()->json(['type' => 'SUCESSO', 'mensagem' => 'Seu email foi enviado com sucesso!'], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(['type' => 'ERROR', 'mensagem' => 'Seu email não foi enviado!'], Response::HTTP_BAD_REQUEST);
    
        }
   
    }

}
