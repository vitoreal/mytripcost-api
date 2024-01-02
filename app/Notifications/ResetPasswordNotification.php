<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    private $urlRetorno;
    private $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($urlRetorno, $user)
    {
        $this->urlRetorno = $urlRetorno;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
   
        return (new MailMessage)
                    //->from('no-reply@espinolaadvogados.com.br', env('APP_NAME'))
                    ->subject('Recuperação de senha')
                    ->greeting('Olá ' . $this->user->name)
                    ->line('Clique no link abaixo para criar uma nova senha.')
                    ->action('Criar nova senha', url($this->urlRetorno))
                    ->line('Qualquer dúvida é só entrar em contato!')
                    ->replyTo($this->user->email)
                    ->salutation('Obrigado!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
