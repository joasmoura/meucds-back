@component('mail::message')

Olá <b>{{(isset($usuario) ? $usuario->name : 'Fulano')}}</b>, foi solicitado uma alteração de senha para acesso à nossa plataforma com o email <b>{{(isset($usuario) ? $usuario->email : 'Email')}}</b>. 
Clique no link abaixo para poder trocar sua senha.

@component('mail::button', ['url' => (isset($url) ? $url : '')])
Trocar minha senha!
@endcomponent

Se não foi você que fez essa solicitação, não se preocupe, desconsidere essa mensagem. Se preferir, entre em contato conosco!
@endcomponent