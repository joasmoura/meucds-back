@component('mail::message')

Olá <b>{{(isset($usuario) ? $usuario->name : 'Fulano')}}</b>, sua senha foi atualizada com sucesso para o email <b>{{(isset($usuario) ? $usuario->email : 'Email')}}</b>. 
Clique no link abaixo para acessar sua área.

@component('mail::button', ['url' => 'https://meucds.vercel.app/login'])
Acessar minha área
@endcomponent

Se não foi você que fez essa solicitação, não se preocupe, desconsidere essa mensagem. Se preferir, entre em contato conosco!
@endcomponent