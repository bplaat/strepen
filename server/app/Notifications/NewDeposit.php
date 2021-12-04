<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewDeposit extends Notification
{
    use Queueable;

    public $transaction;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->subject('Nieuwe storting op het Strepen Systeem')
            ->greeting('Beste ' . $this->transaction->user->name . ',')
            ->line('Er is een storting van ' . number_format($this->transaction->price, 2, ',', '.') . ' euro op uw account gezet!')
            ->line('Uw balans is op dit moment nu ' . number_format($this->transaction->user->balance, 2, ',', '.') . ' euro.')
            ->salutation('Groetjes, het stambestuur');
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
            'transaction_id' => $this->transaction->id,
            'amount' => $this->transaction->price
        ];
    }
}
