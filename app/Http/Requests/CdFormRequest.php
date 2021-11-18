<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CdFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'titulo' => 'required_with_all:artista',
            'artista' => 'filled'
        ];
    }

    public function messages()
    {
        return [
            'titulo.required_with_all' => 'Digite o título deste CD!',
            'artista.filled' => 'Digite o nome do artista!'
        ];
    }
}
