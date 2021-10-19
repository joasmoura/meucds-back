<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuarioFormRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class UsuarioController extends Controller
{
    public function  registrar(UsuarioFormRequest $request){
        $criado = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        if($criado) {
            if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
                $user = auth()->user();
                return response()->json([
                    'status' => true,
                    'user' => $user,
                    'authenticationToken' => $user->createToken($request->email)->accessToken
                ],Response::HTTP_OK);
            }
            
            return response()->json([
                'status' => true
            ],Response::HTTP_OK);
        } else {
            return response()->json([
                'status' => false
            ],Response::HTTP_OK);
        }
    }

    public function recuperarSenha(Request $request) {
        $usuario = User::where('email', $request->email)->first();
        if($usuario){
            return response()->json([
                'status' => true
             ],Response::HTTP_OK);
        }else{
            return response()->json([
                'status' => false
             ],Response::HTTP_OK);
        }
    }
}
