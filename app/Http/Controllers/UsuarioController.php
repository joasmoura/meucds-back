<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuarioFormRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class UsuarioController extends Controller
{
    public function  registrar(UsuarioFormRequest $request){
        $criado = User::create([
            'name' => $request->name,
            'tipo' => $request->tipo,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'url' => time()
        ]);
        
        if($criado) {
            if($request->file('foto')):
                $extensao = '.'.$request->file('foto')->getClientOriginalExtension();
                $nome = Str::slug($request->name).$extensao;
                $upload = $request->file('foto')->storeAs('usuarios/perfil',$nome,'public');
                if($upload){
                    $criado->foto_usuario = $nome;
                    $criado->save();
                }
            endif;

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

    public function alterarBanner(Request $request){
        $user = auth()->user();

        if($request->file('banner')):
            $extensao = '.'.$request->file('banner')->getClientOriginalExtension();
            $nome = Str::slug($user->name).$extensao;

            if(Storage::disk('public')->exists('usuarios/banner/'.$user->banner_usuario)):
                Storage::delete('usuarios/banner/'.$user->banner_usuario);
            endif;
            $upload = $request->file('banner')->storeAs('usuarios/banner',$nome,'public');
            if($upload){
                $user->banner_usuario = $nome;
                $user->save();
            }

            return response()->json([
                'status' => true
            ],Response::HTTP_OK);
        endif;
    }

    public function alterarFoto(Request $request){
        $user = auth()->user();

        if($request->file('foto')):
            $extensao = '.'.$request->file('foto')->getClientOriginalExtension();
            $nome = Str::slug($user->name).$extensao;

            if(Storage::disk('public')->exists('usuarios/perfil/'.$user->foto_usuario)):
                Storage::delete('usuarios/perfil/'.$user->banner_usuario);
            endif;
            $upload = $request->file('foto')->storeAs('usuarios/perfil',$nome,'public');
            if($upload){
                $user->foto_usuario = $nome;
                $user->save();
            }
        endif;
    }

    public function update(UsuarioFormRequest $request) {
        $user = auth()->user();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->url = Str::slug($request->url);
        $user->telefone_usuario = $request->telefone_usuario;
        $user->sobre_usuario = $request->sobre_usuario;

        if (isset($request->password)){
            $user->password = Hash::make($request->password);
        }

        $salvo = $user->save();

        if ($salvo){
            return response()->json([
                'status' => true
            ],Response::HTTP_OK);
        }
    }

    public function cds () {
        $user = auth()->user();
        $cds = $user->load('cds');
        return $cds;
    }
}
