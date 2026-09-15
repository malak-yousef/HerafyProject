<?php

namespace App\Notifications;

use App\Models\ServiceJob;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ServiceJobCompleted extends Notification
{
    use Queueable;

    public function __construct(private readonly ServiceJob $serviceJob) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'event' => 'service_job_completed',
            'title' => 'تم إكمال العمل',
            'message' => 'تم إكمال العمل على طلبك "'.$this->serviceJob->order->title.'". يمكنك الآن إضافة تقييم.',
            'service_job_id' => $this->serviceJob->id,
            'order_id' => $this->serviceJob->order_id,
            'order_title' => $this->serviceJob->order->title,
            'craftman_name' => $this->serviceJob->craftman->user->name,
            'completed_at' => $this->serviceJob->completed_at->toIso8601String(),
            'destination' => 'herafy.customer.orders.show',
        ];
    }
}
