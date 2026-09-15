<?php

namespace App\Notifications;

use App\Models\Review;
use App\Models\ServiceJob;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ReviewSubmitted extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Review $review,
        private readonly ServiceJob $serviceJob,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'event' => 'review_submitted',
            'title' => 'وصلك تقييم جديد',
            'message' => 'تم نشر تقييم جديد بقيمة '.$this->review->rating.' من 5 على ملفك.',
            'review_id' => $this->review->id,
            'order_id' => $this->serviceJob->order_id,
            'service_job_id' => $this->serviceJob->id,
            'order_title' => $this->serviceJob->order->title,
            'rating' => $this->review->rating,
            'comment_preview' => $this->review->comment
                ? Str::limit(strip_tags($this->review->comment), 120)
                : null,
            'moderation_status' => 'approved',
            'destination' => 'herafy.craftman.jobs.show',
        ];
    }
}
