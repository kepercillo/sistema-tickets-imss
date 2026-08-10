<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BienvenidaUsuarioNotification extends Notification
{
    use Queueable;
    public $user;

    /**
     * Create a new notification instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('ALTA DE CUENTA - SISTEMA DE TICKETS IMSS CHIAPAS')
            ->greeting('¡HOLA, ' . $this->user->name . '!')
            
            ->line('TUS CREDENCIALES DE ACCESO OFICIALES SON:')
            ->line('USUARIO: ' . $this->user->username)
            ->line('UNIDAD MÉDICA (CLUES): ' . $this->user->clues)
            ->line('DEPARTAMENTO: ' . $this->user->department)
            ->action('INGRESAR AL SISTEMA', route('login'))
            ->line('POR MOTIVOS DE SEGURIDAD, RECUERDA NO COMPARTIR TU CONTRASEÑA. EL USO INDEBIDO DE TU CUENTA QUEDARÁ REGISTRADO Y ASOCIADO A TUS DATOS.')
            ->salutation('ATENTAMENTE, DEPARTAMENTO DE TECNOLOGÍAS DE LA INFORMACIÓN - IMSS CHIAPAS');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
