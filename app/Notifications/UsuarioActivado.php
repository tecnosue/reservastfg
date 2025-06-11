<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class UsuarioActivado extends Notification
{
    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('¡Tu cuenta ha sido activada!')
            ->greeting('Hola ' . $notifiable->name . ' 👋')
            ->line('Tu cuenta en la aplicación de reservas ya está activa.')
            ->action('Acceder ahora', url('/login'))
            ->line('¡Ya puedes acceder con tu correo y contraseña!');
    }
}
