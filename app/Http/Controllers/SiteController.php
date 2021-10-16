<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Artistas;
use App\Models\Categoria;
use App\Models\Cd;
use App\Models\Musica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
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
        $artista = $model->where('url',$url)->first();
        $musicas = [];
        $publicidade = [];

        if($artista){
            $artista->foto = $artista->foto('img700');
            $cds = $artista->cds()->with('publicidade')->get();

            if($cds){
                foreach($cds as $key => $cd){
                    $cd->capa_mini = $cd->capa_mini;
                    $cd->num_download += $cd->downloads()->sum('num_download');
                    $cd->num_play += $cd->reproducoes()->sum('num_play');
                    $mus = $cd->musicas()->get();

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
                                $musica->link_musica = $musica->link_musica;
                                $mus[$keym] = $musica;
                            }else{
                                unset($mus[$keym]);
                            }
                        }
                        $cds[$key]->musicas = $mus;
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

    public function artistas_letra($letra, Artistas $model){
        $artistas = $model->where([
            ['bloqueio','0'],
            ['nome' ,'like', $letra.'%']
        ])->paginate(32);

        if($artistas->first()){
            foreach($artistas as $key => $artista){
                $artistas[$key]['foto'] = $artista->foto('img200');
            }
        }

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

        $artista = Artistas::where('url',$request->artista)->first();
        
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
}
