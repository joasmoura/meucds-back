<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UsuarioFormRequest extends FormRequest
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
            'password' => 'required_with_all: email, name|min: 6',
            'email' => [
                'required_with_all:name',
                Rule::unique('users', 'email')->ignore($this->id),
                'email'
            ],
            'name' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'password.min' => 'A senha precisa ter no mínimo 6 carácteres!',
            'password.required_with_all' => 'Digite a senha!',
            'email.required_with_all' => 'Digite o seu endereço de email!',
            'name.required' => 'Digite o seu nome!',
        ];
    }
}
