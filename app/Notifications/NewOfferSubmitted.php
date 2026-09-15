<?php

namespace App\Notifications;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewOfferSubmitted extends Notification
{
    use Queueable;

    public function __construct(private readonly Offer $offer) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'event' => 'new_offer_submitted',
            'title' => 'عرض جديد على طلبك',
            'message' => 'قدّم '.$this->offer->craftman->user->name.' عرضاً جديداً على طلبك "'.$this->offer->order->title.'".',
            'order_id' => $this->offer->order_id,
            'offer_id' => $this->offer->id,
            'craftman_name' => $this->offer->craftman->user->name,
            'offered_price' => number_format((float) $this->offer->price, 2, '.', ''),
            'destination' => 'herafy.customer.orders.show',
        ];
    }
}
