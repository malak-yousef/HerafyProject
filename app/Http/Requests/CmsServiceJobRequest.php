<?php

namespace App\Http\Requests;

use App\Models\Offer;
use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CmsServiceJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $serviceJob = $this->route('service_job') ?? $this->route('serviceJob');

        return [
            'order_id' => [
                'required',
                Rule::exists('orders', 'id')->whereNull('deleted_at'),
                Rule::unique('service_jobs', 'order_id')->ignore($serviceJob),
            ],
            'offer_id' => [
                'required',
                Rule::exists('offers', 'id')->whereNull('deleted_at'),
                Rule::unique('service_jobs', 'offer_id')->ignore($serviceJob),
            ],
            'customer_id' => ['required', Rule::exists('customers', 'id')->whereNull('deleted_at')],
            'craftman_id' => ['required', Rule::exists('craftmen', 'id')->whereNull('deleted_at')],
            'status' => ['required', Rule::in(['pending', 'in_progress', 'completed', 'cancelled'])],
            'started_at' => ['nullable', 'date', 'required_if:status,in_progress,completed', 'prohibited_if:status,pending'],
            'completed_at' => ['nullable', 'date', 'required_if:status,completed', 'prohibited_unless:status,completed', 'after_or_equal:started_at'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $order = Order::find($this->integer('order_id'));
            $offer = Offer::find($this->integer('offer_id'));

            if (! $order || ! $offer) {
                return;
            }

            if ($offer->order_id !== $order->id) {
                $validator->errors()->add('offer_id', 'يجب أن يكون العرض تابعًا للطلب المحدد.');
            }

            if ($offer->craftman_id !== $this->integer('craftman_id')) {
                $validator->errors()->add('craftman_id', 'يجب أن يكون الحرفي هو صاحب العرض المحدد.');
            }

            if ($order->customer_id !== $this->integer('customer_id')) {
                $validator->errors()->add('customer_id', 'يجب أن يكون العميل هو صاحب الطلب المحدد.');
            }

            $status = $this->string('status')->toString();
            $requiredLinkedStatus = $status === 'pending' ? 'accepted' : $status;

            if ($order->status !== $requiredLinkedStatus) {
                $validator->errors()->add('status', 'حالة الطلب غير متوافقة مع حالة مهمة الخدمة.');
            }

            if ($offer->status !== $requiredLinkedStatus) {
                $validator->errors()->add('status', 'حالة العرض غير متوافقة مع حالة مهمة الخدمة.');
            }
        }];
    }
}
