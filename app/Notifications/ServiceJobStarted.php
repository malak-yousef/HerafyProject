<?php

namespace App\Notifications;

use App\Models\ServiceJob;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ServiceJobStarted extends Notification
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
            'event' => 'service_job_started',
            'title' => 'بدأ العمل على طلبك',
            'message' => 'بدأ الحرفي العمل على طلبك "'.$this->serviceJob->order->title.'".',
            'service_job_id' => $this->serviceJob->id,
            'order_id' => $this->serviceJob->order_id,
            'order_title' => $this->serviceJob->order->title,
            'craftman_name' => $this->serviceJob->craftman->user->name,
            'started_at' => $this->serviceJob->started_at->toIso8601String(),
            'destination' => 'herafy.customer.orders.show',
        ];
    }
}
