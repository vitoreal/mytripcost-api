<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Illuminate\Http\Response;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenBlacklistedException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
        /*
        $this->renderable(function (UnauthorizedHttpException $e, $request) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Você não está logado!' ];
            return response()->json($retorno, Response::HTTP_UNAUTHORIZED);
        });

        $this->renderable(function(TokenInvalidException $e){
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Token de autenticação inválido, faça o login novamente!' ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);
        });
        $this->renderable(function (TokenExpiredException $e) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Token expirado, faça o login novamente!' ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);
        });

        $this->renderable(function (JWTException $e) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Você não está logado! Error: JWTException' ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);
        });

        $this->renderable(function (TokenBlacklistedException $e) {
            $retorno = [ 'type' => 'ERROR', 'mensagem' => 'Você não está logado!' ];
            return response()->json($retorno, Response::HTTP_BAD_REQUEST);
        });

        */
    }

}
