<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class sucessoRecupercaoSenha extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $usuario;

    public function __construct($usuario)
    {
        $this->usuario = $usuario;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $usuario = $this->usuario;
        $this->subject('Alteração de senha');
        $this->to($usuario->email, $usuario->name);
        return $this->markdown('mail.sucessoRecuperacaoSenha', compact('usuario'));
    }
}
