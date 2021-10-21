<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginFormRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function  entrar(LoginFormRequest $request){
        $regras = [
            'password' => 'required|string',
            'email' => 'required|string|email',
         ];

         $mensagens = [
          'password.string' => 'Senha incorreta!',
          'password.required' => 'Digite sua senha!',
          'email.email' => 'Endereço de email incorreto!',
          'email.string' => 'Endereço de email incorreto!',
          'email.required' => 'Digite o seu email!',
         ];
         $request->validate($regras,$mensagens);

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = auth()->user();           

            return response()->json([
                'status' => true,
                'authenticationToken' => $user->createToken($request->email)->accessToken
            ],Response::HTTP_OK);
         }else{
             return response()->json([
                'status' => false
             ],Response::HTTP_NOT_FOUND);
         }
    }
}
