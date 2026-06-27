<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Password;

class BoasVindasDefinirSenha extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $nomeFirma,
        private readonly ?string $token = null,
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $token = $this->token ?? Password::createToken($notifiable);

        $url = route('password.reset', [
            'token' => $token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        $expira = (int) config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60);

        return (new MailMessage)
            ->subject('Bem-vindo(a) à plataforma Cesta Básica')
            ->greeting('Olá, '.$this->nomeFirma.'!')
            ->line('Sua firma foi cadastrada na plataforma Cesta Básica.')
            ->line('Para começar a usar sua conta, defina uma senha de acesso clicando no botão abaixo.')
            ->action('Definir minha senha', $url)
            ->line('Este link expira em '.$expira.' minutos.')
            ->line('Se você não esperava este e-mail, nenhuma ação é necessária.');
    }
}
