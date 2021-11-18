<?php

namespace App\Http\Controllers;

use App\Http\Requests\CdFormRequest;
use App\Models\Cd;
use App\Models\Musica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $cds = $user->cds()->with('downloads')->paginate(10);
        $cds->append('capa_mini');
        if($cds->first()){
            foreach($cds as $key => $cd){
                $cds[$key]['num_download'] += $cd->downloads()->sum('num_download');
                $cds[$key]['num_play'] += $cd->reproducoes()->sum('num_play');
                $cds[$key]['data_cadastro'] = date('d/m/Y H:i', strtotime($cd->created_at));
            }
        }

        return $cds;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CdFormRequest $request)
    {
        $user = auth()->user();
        $salvo = $user->cds()->create([
            'artista' => ($user->tipo === 'D' ? $request->artista : null),
            'titulo' => $request->titulo,
            'youtube' => $request->youtube,
            'categoria_id' => $request->categoria,
            'texto' => $request->descricao,
            'url' => Str::slug($request->titulo, '-'),
            'publicacao' => $request->publicacao,
            'data_publicacao' => ($request->publicacao == 'P' ? $request->data_publicacao : null),
            'hora_publicacao' => ($request->publicacao == 'P' ? $request->hora_publicacao : null),
            'tipo_publicacao' => ($request->publicacao == 'S' ? $request->tipo_publicacao : null),
        ]);

        if($salvo){
            if($request->file('capa')){
                $extensao = '.'.$request->file('capa')->getClientOriginalExtension();
                $nome = Str::slug($request->titulo, '-').$extensao;
                $upload = $request->file('capa')->storeAs('cds',$nome, 'public');
                if($upload){
                    $salvo->img = $nome;
                    $salvo->save();
                }
            }
            return response()->json([
                'status' => true,
                'cd' => $salvo
            ],Response::HTTP_OK);
        }else{
            return response()->json([
                'Não foi possível cadastrar seu cd!'
            ],Response::HTTP_NOT_FOUND);
        }

    }

    public function upload($id, Request $request){
        $cd = Cd::find($id);
        if($cd){
            $usuario = $cd->user;
            $musica = $request->file('musica');
            if($musica){
                $extensao = '.'.$musica->getClientOriginalExtension();
                $nome = Str::slug($request->titulo);
                $upload = $musica->storeAs('musicas', $cd->url.'/'.$nome.$extensao, 'public');
                if($upload){
                    $cd->musicas()->create([
                        'nome' => $musica->getClientOriginalName(),
                        'url' => $nome,
                        'link' => $cd->url.'/'.$nome.$extensao,
                        'ordem' => $cd->musicas()->count()+1
                    ]);
                }
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $cd = Cd::with('musicas')->find($id);
        $musicas = [];

        if($cd){
            $musicas = $cd->musicas()->get();
            $musicas->append('link_musica');
            $cd->append('capa_mini');

            if($musicas->first()){
                foreach($musicas as $key => $m){
                    $musicas[$key]['link_musica'] = $m->link_musica;
                }
            }
        }

        return compact('cd', 'musicas');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cd = Cd::find($id);

        if($cd){
            Storage::deleteDirectory('musicas/'.$cd->url);
            
            $excluido = $cd->delete();
            if($excluido){
                return response()->json([
                    'status' => true
                ],Response::HTTP_OK);
            }
        }
    }

    public function removerMusica($id){
        $musica = Musica::find($id);

        if($musica) {
            if(Storage::disk('public')->exists('musicas/'.$musica->link)):
                $removida = Storage::delete('musicas/'.$musica->link);
            endif;

            $excluida = $musica->delete();
            if($excluida){
                return response()->json([
                    'status' => true
                ],Response::HTTP_OK);
            }
        }
    }

    public function removerCapa($id){
        $cd = Cd::find($id);
        if($cd){
            if(Storage::disk('public')->exists('cds/'.$cd->img)):
                $excluida = Storage::delete('cds/'.$cd->img);
                if($excluida){
                    return response()->json([
                        'status' => true
                    ],Response::HTTP_OK);
                }
            endif;
        }
    }
}
