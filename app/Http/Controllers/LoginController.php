<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginFormRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function  entrar(LoginFormRequest $request){
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = auth()->user();           

            return response()->json([
                'status' => true,
                // 'usuario' => $user,
                'authenticationToken' => $user->createToken($request->email)->accessToken
            ],Response::HTTP_OK);
         }else{
             return response()->json([
                'status' => false
             ],Response::HTTP_OK);
         }
    }
}
