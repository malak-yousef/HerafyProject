@extends('cms.parent')

@section('title', 'تفاصيل الخدمة')

@section('main_content', 'تفاصيل الخدمة')

@section('content')

<div class="container-fluid">

    <div class="card shadow-sm">

        <div class="card-header d-flex justify-content-between">

            <h5>
                <i class="fas fa-eye"></i>
                تفاصيل الخدمة #{{ $serviceJob->id }}
            </h5>

            <a href="{{ route('service-jobs.index') }}"
               class="btn btn-secondary btn-sm">

                العودة

            </a>

        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <tr>
                    <th>رقم الخدمة</th>
                    <td>{{ $serviceJob->id }}</td>
                </tr>

                <tr>
                    <th>رقم الطلب</th>
                    <td>#{{ $serviceJob->order_id }}</td>
                </tr>

                <tr>
                    <th>رقم العرض</th>
                    <td>#{{ $serviceJob->offer_id }}</td>
                </tr>

                <tr>
                    <th>العميل</th>
                    <td>
                        {{ $serviceJob->customer->user->name ?? '-' }}
                    </td>
                </tr>

                <tr>
                    <th>الحرفي</th>
                    <td>
                        {{ $serviceJob->craftman->user->name ?? '-' }}
                    </td>
                </tr>

                <tr>
                    <th>الحالة</th>
                    <td>
                        <x-cms.status-badge :status="$serviceJob->status" />
                    </td>
                </tr>

                <tr>
                    <th>بدأت في</th>
                    <td>
                        {{ $serviceJob->started_at?->format('Y-m-d H:i') ?? '-' }}
                    </td>
                </tr>

                <tr>
                    <th>اكتملت في</th>
                    <td>
                        {{ $serviceJob->completed_at?->format('Y-m-d H:i') ?? '-' }}
                    </td>
                </tr>

                <tr>
                    <th>تاريخ الإنشاء</th>
                    <td>
                        {{ $serviceJob->created_at?->format('Y-m-d H:i') }}
                    </td>
                </tr>

            </table>

        </div>

    </div>

</div>

@endsection