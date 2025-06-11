<?php
namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordEs extends Notification
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Restablece tu contraseña')
            ->greeting('¡Hola!')
            ->line('Recibes este correo porque solicitaste restablecer la contraseña de tu cuenta.')
            ->action('Restablecer contraseña', url(config('app.url').route('password.reset', ['token' => $this->token, 'email' => $notifiable->email], false)))
            ->line('Si no solicitaste el restablecimiento, ignora este correo.');
    }
}
