@extends('cms.parent')

@section('title', 'الإعدادات')

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                <i class="fas fa-cog me-2"></i>
                إعدادات الموقع
            </h4>

            <p class="text-muted mb-0">
                إدارة إعدادات منصة حرفي
            </p>
        </div>

        <div class="d-flex gap-2">

            <a href="{{ route('settings.trashed') }}"
               class="btn btn-outline-danger">
                <i class="fas fa-trash me-1"></i>
                المحذوفة
            </a>

            <a href="{{ route('settings.create') }}"
               class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>
                إضافة إعداد
            </a>

        </div>
    </div>


    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>
        </div>
    @endif


    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- Settings Table --}}
    <div class="card shadow-sm border-0">

        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">
                قائمة الإعدادات
            </h5>
        </div>

        <div class="card-body">

            @if($settings->count())

                <div class="table-responsive">

                    <table class="table table-hover align-middle text-center">

                        <thead class="table-light">

                            <tr>
                                <th>#</th>
                                <th>المفتاح</th>
                                <th>القيمة</th>
                                <th>تاريخ الإضافة</th>
                                <th>الإجراءات</th>
                            </tr>

                        </thead>

                        <tbody>

                            @foreach($settings as $setting)

                                <tr>

                                    <td>
                                        {{ $loop->iteration }}
                                    </td>

                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $setting->key }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $setting->value ?? '---' }}
                                    </td>

                                    <td>
                                        {{ $setting->created_at?->format('Y-m-d H:i') }}
                                    </td>

                                    <td>

                                        <div class="d-flex justify-content-center gap-1">

                                            {{-- Show --}}
                                            <a href="{{ route('settings.show', $setting) }}"
                                               class="btn btn-sm btn-info text-white"
                                               title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>


                                            {{-- Edit --}}
                                            <a href="{{ route('settings.edit', $setting) }}"
                                               class="btn btn-sm btn-warning"
                                               title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>


                                            {{-- Delete --}}
                                            <form action="{{ route('settings.destroy', $setting) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا الإعداد؟');">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="btn btn-sm btn-danger"
                                                        title="حذف">
                                                    <i class="fas fa-trash"></i>
                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="text-center py-5">

                    <i class="fas fa-cogs fa-3x text-muted mb-3"></i>

                    <h5 class="text-muted">
                        لا توجد إعدادات حاليًا
                    </h5>

                    <p class="text-muted">
                        يمكنك إضافة أول إعداد للموقع.
                    </p>

                    <a href="{{ route('settings.create') }}"
                       class="btn btn-primary">

                        <i class="fas fa-plus me-1"></i>
                        إضافة إعداد

                    </a>

                </div>

            @endif

        </div>

    </div>

</div>
@endsection