<?php

namespace App\Http\Controllers;

use App\Mail\recuperarSenha;
use App\Mail\sucessoRecupercaoSenha;
use App\Models\Banner;
use App\Models\Artistas;
use App\Models\User;
use App\Models\Categoria;
use App\Models\Cd;
use App\Models\Musica;
use App\Models\PasswordReset;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use ZipArchive;

class SiteController extends Controller
{
    public function banners(Request $request){
        $categoria = $request->categoria;

        $banners = [];

        if(!empty($categoria)){
            $categoria = Categoria::where('url',$categoria)->where('bloqueio','0')->first();
            if($categoria){
                $banners = $categoria->banners()->where('bloquear','0')->get();
                if($banners->first()){
                    foreach($banners as $b) {
                        $b->url_categoria = $categoria->url;
                    }
                }
            }
        }else{
            $banners = Banner::whereNull('categoria_id')->where('bloquear','0')->get();
        }

        if(!empty($banners) && $banners->first()){
            foreach($banners as $key => $banner){
                $banners[$key]['imagem'] = $banner->imagem;
            }
        }
        return $banners;
    }

    public function categorias(){
        $categorias = Categoria::where('bloqueio',0)->get();
        if($categorias){
            foreach($categorias as $key => $categoria){
                $categorias[$key]['img'] = $categoria->img;
            }
        }

        return $categorias;
    }

    public function artistas(Request $request){
        if(!empty($request->categoria)){
            $artistas = User::leftJoin('cds', 'users.id', 'cds.user_id')
            ->leftJoin('categorias', 'cds.categoria_id', 'categorias.id')
            ->where([
                ['categorias.url', $request->categoria],
                ['users.tipo', 'A']
            ])
            ->select('users.name', 'users.url', 'users.foto_usuario')->groupBy('users.id')->paginate(32);
            $artistas->append('foto');
        }else{
            $artistas = User::where([
                ['bloqueia_usuario','0'],
                ['tipo', 'A']
            ])->select('name', 'url', 'foto_usuario')->paginate(32);
            $artistas->append('foto');
        }
        return $artistas;
    }

    public function artista($url){
        $artista = User::where('url',$url)->first();
        $musicas = [];
        $publicidade = [];

        if($artista){
            $artista->append('foto');
            $artista->append('banner');
            $cds = $artista->cds()->with('publicidade')->get();
            $cds->append('capa_mini');
            if($cds){
                foreach($cds as $key => $cd){
                    $cd->num_download += $cd->downloads()->sum('num_download');
                    $cd->num_play += $cd->reproducoes()->sum('num_play');
                    $mus = $cd->musicas()->get();
                    $mus->append('link_musica');

                    $publicidades= $cd->publicidade()->get();
                    if($publicidades->first()){
                        foreach($publicidades as $pub){
                            array_push($publicidade, $pub);
                        }
                    }

                    if($mus->first()){
                        foreach($mus as $keym => $musica){
                            if($musica->link_musica){
                                $musica->url = $artista->url.'/'.$cd->url.'/'.$musica->url;
                                $mus[$keym] = $musica;
                            }else{
                                unset($mus[$keym]);
                            }
                        }
                        $cds[$key]['musicas'] = $mus;
                    }

                    $artista->cds = $cds;
                }
            }
            return compact('artista','publicidade');
        }else{
            return response()->json([
                'status' => false,
            ],Response::HTTP_NOT_FOUND);
        }
    }

    public function divulgadores(Request $request){
        if(!empty($request->categoria)){
            $artistas = User::leftJoin('cds', 'users.id', 'cds.user_id')
            ->leftJoin('categorias', 'cds.categoria_id', 'categorias.id')
            ->where([
                ['categorias.url', $request->categoria],
                ['users.tipo', 'D']
            ])
            ->select('users.name', 'users.url', 'users.foto_usuario')->groupBy('users.id')->paginate(32);
            $artistas->append('foto');
        }else{
            $artistas = User::where([
                ['bloqueia_usuario','0'],
                ['tipo', 'D']
            ])->select('name', 'url', 'foto_usuario')->paginate(32);
            $artistas->append('foto');
        }
        return $artistas;
    }

    public function artistas_letra($letra, User $model){
        $artistas = $model->where([
            ['bloqueia_usuario','0'],
            ['name' ,'like', $letra.'%']
        ])->paginate(32);
        $artistas->append('foto');
        return $artistas;
    }

    public function atualizanomemusicas(){
        $musicas = Musica::get();

        if($musicas->first()){
            foreach($musicas as $m){
                $m->url = Str::slug($m->nome);
                $m->save();
            }
        }
    }

    public function baixarCd(Request $request){
        $zip = new ZipArchive();
        $name = $request->cd.'.zip';
        $zipPath = public_path('/downloads'.DIRECTORY_SEPARATOR.$name);

        $artista = User::where('url',$request->artista)->first();
        
        if($artista){
            $cd = $artista->cds()->where('url', $request->cd)->first();
            
            if($cd){
                $musica = $cd->musicas()->first();
                
                if($musica){
                    $explode = explode('/',$musica->link);
                    $pasta = public_path('storage'.DIRECTORY_SEPARATOR.'musicas'.DIRECTORY_SEPARATOR.$explode[0]);
                    
                    if ($zip->open($zipPath, ZipArchive::CREATE) > 0){
                        $files = File::files($pasta);
                        foreach ($files as $key => $value) {
                            $relativeNameInZipFile = basename($value);

                            // adicionar arquivo ao zip
                            $zip->addFile($value, $relativeNameInZipFile);
                        }

                        // concluir a operacao
                        $zip->close();
                    }

                    return response()->download($zipPath);
                }
            }
        }
    }

    public function contaDownloadCd($id) {
        $cd = Cd::find($id);
        $qtd = 0;
        if($cd){
            $download = $cd->downloads()->first();
            if($download){
                $qtd = $cd->downloads()->sum('num_download') + 1;
                $qtd += $cd->num_download;
                $download->num_download += 1;
                $download->save();
            }else{
                $qtd = $cd->num_download + 1;
                $cd->downloads()->create([
                    'num_download' => 1
                ]);
            }
        }
        return $qtd;
    }

    public function contaPlayCd($id){
        $cd = Cd::find($id);
        $qtd = 0;
        if($cd){
            $play = $cd->reproducoes()->first();
            if($play){
                $qtd = $cd->reproducoes()->sum('num_play') + 1;
                $qtd += $cd->num_play;
                $play->num_play += 1;
                $play->save();
            }else{
                $qtd = $cd->num_play + 1;
                $cd->reproducoes()->create([
                    'num_play' => 1
                ]);
            }
        }
        return $qtd;
    }

    public function recuperarSenha(Request $request) {
        $usuario = User::where('email', $request->email)->first();
        
        if($usuario){
            $reset = PasswordReset::create([
                'email' => $request->email,
                'token' => Str::random(60)
            ]);

            if($reset){
                Mail::send(new recuperarSenha($usuario, $reset->token));            
                return response()->json([
                    'status' => true
                 ],Response::HTTP_OK);
            }
        }

        return response()->json(Response::HTTP_NOT_FOUND);
    }

    public function verificaTokenRecuperaSenha(Request $request){
        $passwordReset = PasswordReset::where('token', $request->token)->first();

        if($passwordReset){
            if(Carbon::parse($passwordReset->created_at)->addMinutes(720)->isPast()){
                $passwordReset->delete();
                return response()->json([
                    'mensagem' => 'Parâmetro de redefinição incorreto! Tente recuperar sua senha novamente!'
                ],Response::HTTP_NOT_FOUND);
            }else{
                return response()->json([
                    'reset' => $passwordReset
                 ],Response::HTTP_OK);
            }
        }else{
            return response()->json([
                'mensagem' => 'Parâmetro de redefinição incorreto! Tente recuperar sua senha novamente!'
            ],Response::HTTP_NOT_FOUND);
        }
    }

    public function confirmarResetSenha(Request $request){
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|confirmed',
            'token' => 'required|string'
        ]);

        $passwordReset = PasswordReset::where([
            ['token', $request->token],
            ['email', $request->email]
        ])->first();

        if(!$passwordReset){
            return response()->json([
                'mensagem' => 'Parâmetro de redefinição incorreto! Tente recuperar sua senha novamente!'
            ],Response::HTTP_NOT_FOUND);
        }

        $usuario = User::where('email', $passwordReset->email)->first();
        if (!$usuario){
            return response()->json([
                'mensagem' => 'Não foi possível encontrar um usuário com este email!'
            ], Response::HTTP_NOT_FOUND);
        }

        $usuario->password = bcrypt($request->password);
        $usuario->save();

        $passwordReset->delete();

        Mail::send(new sucessoRecupercaoSenha($usuario));

        return response()->json([
            'status' => true
        ],Response::HTTP_OK);
    }
}
