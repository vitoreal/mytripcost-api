<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class GoogleAuthController extends Controller
{
    //
    public function redirect(){
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function callbackGoogle(){

        try {

            $googleUser = Socialite::driver('google')->stateless()->user();
           
            $user = User::where('google_id', $googleUser->id)->first();

            if(!$user){

                // Verifica se o email ja foi cadastrado
                $userCheckEmail = User::where('email', $googleUser->email)->first();

                if(!$userCheckEmail){

                    $user = User::create([
                        'name' => $googleUser->name,
                        'email' => $googleUser->email,
                        'status_id' => 1,
                        'google_id' => $googleUser->id,
                        'google_token' => $googleUser->token,
                        'google_refresh_token' => $googleUser->refreshToken,
                    ]);

                    $roleUser = array(
                        ['role_id' => 3, 'user_id' => $user->id]
                    );
            
                    DB::table('role_user')->insert($roleUser);

                } else {
                    return response()->json(['type' => 'ERROR', 'mensagem' => 'Já existe um usuário cadastrado com esse email! Utilizar o formulário abaixo para acessar o sistema'], 403);
                }

            } else {
                User::where('google_id', $googleUser->id)->update([
                    'google_id' => $googleUser->id,
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken,
                ]);
                
                $user = User::where('google_id', $googleUser->id)->first();

            }

            $user->role = $user->roles[0]; // Pegando as roles do usuário

            return redirect('http://localhost:5173/home/'.$user->google_id);

        } catch (\Throwable $th){
            dd($th);
        }

    }

    public function validaDadosLoginGoogle(Request $request){

        if($request->idUser){

            //$user = Auth::user();
            
            $user = User::where('google_id', $request->idUser)->first();

            $user->role = $user->roles[0]; // Pegando as roles do usuário

            $retorno = [
                'type' => 'SUCESSO',
                'mensagem' => 'Login efetuado com sucesso!',
                'user' => $user,
                'token' => $user->google_token
            ];
    
            return response()->json($retorno, 200);
            
        } else {
            return response()->json(['type' => 'ERROR', 'mensagem' => 'Não foi possível efetuar o login !'], 403);
        }

    }
}
