<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class recuperarSenha extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

     protected $usuario;
     protected $token;
    public function __construct($usuario, $token)
    {
        $this->usuario = $usuario;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $usuario = $this->usuario;
        $url = env('APP_URL_FRONT').'login/'.$this->token;

        $this->subject('Alteração de senha');
        $this->to($usuario->email, $usuario->name);
        return $this->markdown('mail.emailRecuperacao', compact('usuario','url'));
    }
}
