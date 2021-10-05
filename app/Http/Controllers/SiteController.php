<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Artistas;
use App\Models\Categoria;
use App\Models\Musica;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

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
        return $categorias;
    }

    public function artistas(Request $request, Artistas $model){
        if(isset($request->categoria) && !empty($request->categoria)){
            $categoria = Categoria::where('url',$request->categoria)->first();

            if($categoria){
                $artistas = $categoria->artistas()->paginate(32);
            }
        }else{
            $artistas = $model->where('bloqueio','0')->paginate(32);
        }

        if($artistas->first()){
            foreach($artistas as $key => $artista){
                $artistas[$key]['foto'] = $artista->foto('img200');
            }
        }

        return $artistas;
    }

    public function artista($url, Artistas $model){
        $artista = $model->with('cds')->where('url',$url)->first();
        $musicas = [];
        if($artista){
            $artista->foto = $artista->foto('img700');
            $cds = $artista->cds()->with('musicas')->get();
            if($cds->first()){
                foreach($cds as $cd){
                    $mus = $cd->musicas()->get();
                    if($mus->first()){
                        foreach($mus as $musica){
                            if($musica->link_musica){
                                $musica->link_musica = $musica->link_musica;
                                array_push($musicas, $musica);
                            }
                        }
                    }
                }
                $artista->musicas = $musicas;
            }
            return compact('artista');
        }else{
            return response()->json([
                'status' => false,
            ],Response::HTTP_NOT_FOUND);
        }
    }

    public function musicas_artista(Request $request){

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
}