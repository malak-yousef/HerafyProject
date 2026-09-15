<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CmsReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $review = $this->route('review');

        return [
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string'],
            'craftsman_id' => ['required', Rule::exists('craftmen', 'id')->whereNull('deleted_at')],
            'customer_id' => ['required', Rule::exists('customers', 'id')->whereNull('deleted_at')],
            'request_id' => [
                'required',
                Rule::exists('orders', 'id')->whereNull('deleted_at'),
                Rule::unique('reviews', 'request_id')->ignore($review),
            ],
            'status' => ['nullable', Rule::in(['pending', 'approved', 'rejected'])],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $order = Order::query()->with(['serviceJob.offer'])->find($this->integer('request_id'));
            if (! $order) {
                return;
            }

            if ($order->customer_id !== $this->integer('customer_id')) {
                $validator->errors()->add('customer_id', 'يجب أن يكون العميل هو صاحب الطلب المحدد.');
            }

            $job = $order->serviceJob;
            if (! $job) {
                $validator->errors()->add('request_id', 'لا يمكن إضافة تقييم قبل وجود مهمة خدمة مكتملة للطلب.');

                return;
            }

            if ($job->customer_id !== $this->integer('customer_id')) {
                $validator->errors()->add('customer_id', 'عميل مهمة الخدمة لا يطابق العميل المحدد.');
            }

            if ($job->craftman_id !== $this->integer('craftsman_id')) {
                $validator->errors()->add('craftsman_id', 'الحرفي المحدد لم ينفذ مهمة الخدمة الخاصة بالطلب.');
            }

            if ($order->status !== 'completed' || $job->status !== 'completed' || $job->offer?->status !== 'completed') {
                $validator->errors()->add('request_id', 'لا يمكن إضافة تقييم إلا بعد اكتمال الطلب والعرض ومهمة الخدمة.');
            }
        }];
    }
}
