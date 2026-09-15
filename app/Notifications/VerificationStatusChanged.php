<?php

namespace App\Notifications;

use App\Models\Verification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VerificationStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Verification $verification,
        private readonly string $status,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $approved = $this->status === 'approved';

        return [
            'event' => 'verification_status_changed',
            'title' => $approved ? 'تم قبول طلب التوثيق' : 'تم رفض طلب التوثيق',
            'message' => $approved
                ? 'تمت الموافقة على طلب توثيق حسابك بنجاح.'
                : 'تم رفض طلب التوثيق. يمكنك مراجعة التفاصيل وتحديث بياناتك.',
            'verification_id' => $this->verification->id,
            'status' => $this->status,
            'destination' => 'herafy.verification.show',
        ];
    }
}
