<?php

namespace App\Notifications;

use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewDeposit extends Notification
{
    use Queueable;

    public Transaction $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->subject('Nieuwe storting op het Strepen Systeem')
            ->greeting('Beste '.$this->transaction->user->name.',')
            ->line('Er is een storting van '.Setting::get('currency_symbol').' '.number_format($this->transaction->price, 2, ',', '.').' op uw account gezet!')
            ->line('Uw balans is op dit moment nu '.Setting::get('currency_symbol').' '.number_format($this->transaction->user->balance, 2, ',', '.').'.')
            ->salutation('Groetjes, het stambestuur');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'transaction_id' => $this->transaction->id,
            'amount' => $this->transaction->price,
        ];
    }
}
