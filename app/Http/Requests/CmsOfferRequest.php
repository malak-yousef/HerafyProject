<?php

namespace App\Http\Requests;

use App\Models\Craftman;
use App\Models\Offer;
use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CmsOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_id' => ['required', Rule::exists('orders', 'id')->whereNull('deleted_at')],
            'craftman_id' => ['required', Rule::exists('craftmen', 'id')->whereNull('deleted_at')],
            'price' => ['required', 'numeric', 'min:1'],
            'message' => ['required', 'string'],
            'status' => ['required', Rule::in(['pending', 'accepted', 'in_progress', 'completed', 'cancelled'])],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $order = Order::find($this->integer('order_id'));
            $craftman = Craftman::find($this->integer('craftman_id'));

            if (! $order || ! $craftman) {
                return;
            }

            if ($order->profession_id !== $craftman->profession_id) {
                $validator->errors()->add('craftman_id', 'مهنة الحرفي لا تطابق مهنة الطلب.');
            }

            $offer = $this->route('offer');
            $duplicate = Offer::withTrashed()
                ->where('order_id', $order->id)
                ->where('craftman_id', $craftman->id)
                ->when($offer, fn ($query) => $query->whereKeyNot($offer->id))
                ->exists();

            if ($duplicate) {
                $validator->errors()->add('craftman_id', 'يوجد عرض مسبق لهذا الحرفي على الطلب.');
            }

            $status = $this->string('status')->toString();

            if ($order->status !== $status) {
                $validator->errors()->add('status', 'حالة العرض غير متوافقة مع حالة الطلب.');
            }

            if ($offer?->serviceJob) {
                if ($offer->serviceJob->order_id !== $order->id) {
                    $validator->errors()->add('order_id', 'لا يمكن تغيير طلب عرض مرتبط بمهمة خدمة.');
                }

                if ($offer->serviceJob->craftman_id !== $craftman->id) {
                    $validator->errors()->add('craftman_id', 'لا يمكن تغيير حرفي عرض مرتبط بمهمة خدمة.');
                }

                $requiredOfferStatus = $offer->serviceJob->status === 'pending'
                    ? 'accepted'
                    : $offer->serviceJob->status;

                if ($status !== $requiredOfferStatus) {
                    $validator->errors()->add('status', 'حالة العرض غير متوافقة مع مهمة الخدمة المرتبطة.');
                }
            }
        }];
    }
}
