<?php

namespace App\Notifications;

use App\Helpers\BetterParsedown;
use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class NewPost extends Notification
{
    use Queueable;

    public User $user;

    public Post $post;

    public function __construct(User $user, Post $post)
    {
        $this->user = $user;
        $this->post = $post;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->subject($this->post->title.' - Een nieuw nieuws bericht op het Strepen Systeem')
            ->greeting('Beste '.$this->user->name.',')
            ->line('Er is een nieuw nieuws bericht op het Strepen Systeem geplaatst:')
            ->line(new HtmlString(BetterParsedown::instance()->text($this->post->body)))
            ->salutation('Groetjes, het stambestuur');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'post_id' => $this->post->id,
        ];
    }
}
