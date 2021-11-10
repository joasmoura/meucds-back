<?php

namespace App\Http\Controllers;

use App\Models\Cd;
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
    public function index()
    {
        $user = auth()->user();

        $cds = $user->cds()->paginate(10);
        $cds->append('capa_mini');

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
    public function store(Request $request)
    {
        $user = auth()->user();
        $salvo = $user->cds()->create([
            'artista' => $request->artista,
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
                        'link' => $cd->link.'/'.$nome.$extensao,
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
            $remover = Storage::deleteDirectory('musicas/'.$cd->url);
            
            if($remover){
                $excluido = $cd->delete();
                if($excluido){
                    return response()->json([
                        'status' => true
                    ],Response::HTTP_OK);
                }
            } else {
                return response()->json([
                    'msg' => 'Não foi possível excluir os arquivos do cd!'
                ],Response::HTTP_NOT_FOUND);
            }
        }
    }
}
