<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewEligibleOrder extends Notification
{
    use Queueable;

    public function __construct(private readonly Order $order) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'event' => 'new_eligible_order',
            'title' => 'طلب جديد متاح',
            'message' => 'تم نشر طلب جديد ضمن مهنتك: "'.$this->order->title.'".',
            'order_id' => $this->order->id,
            'order_title' => $this->order->title,
            'profession_name' => $this->order->profession->name,
            'budget' => $this->order->budget,
            'service_date' => $this->order->service_date?->toDateString(),
            'destination' => 'herafy.order.show',
        ];
    }
}
