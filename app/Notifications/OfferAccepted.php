<?php

namespace App\Notifications;

use App\Models\Offer;
use App\Models\ServiceJob;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OfferAccepted extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Offer $offer,
        private readonly ServiceJob $serviceJob,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'event' => 'offer_accepted',
            'title' => 'تم قبول عرضك',
            'message' => 'تم قبول عرضك بنجاح. يمكنك الآن متابعة العمل المسند إليك.',
            'order_id' => $this->offer->order_id,
            'offer_id' => $this->offer->id,
            'service_job_id' => $this->serviceJob->id,
            'order_title' => $this->offer->order->title,
            'agreed_price' => number_format((float) $this->offer->price, 2, '.', ''),
            'destination' => 'herafy.craftman.jobs.show',
        ];
    }
}
