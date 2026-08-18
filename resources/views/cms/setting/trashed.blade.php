@extends('cms.parent')

@section('title', 'الإعدادات المحذوفة')

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                <i class="fas fa-trash me-2"></i>
                الإعدادات المحذوفة
            </h4>

            <p class="text-muted mb-0">
                إدارة الإعدادات التي تم حذفها مؤقتًا
            </p>
        </div>

        <a href="{{ route('settings.index') }}"
           class="btn btn-secondary">

            <i class="fas fa-arrow-right me-1"></i>
            العودة للإعدادات

        </a>

    </div>


    {{-- Success Message --}}
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show"
             role="alert">

            <i class="fas fa-check-circle me-2"></i>

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- Table --}}
    <div class="card shadow-sm border-0">

        <div class="card-header bg-white py-3">

            <h5 class="mb-0 fw-bold">
                قائمة الإعدادات المحذوفة
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
                                <th>تاريخ الحذف</th>
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
                                        {{ $setting->deleted_at?->format('Y-m-d H:i') }}
                                    </td>

                                    <td>

                                        <div class="d-flex justify-content-center gap-1">

                                            {{-- Restore --}}
                                            <form action="{{ route('settings.restore', $setting->id) }}"
                                                  method="POST">

                                                @csrf
                                                @method('PATCH')

                                                <button type="submit"
                                                        class="btn btn-sm btn-success"
                                                        title="استرجاع"
                                                        onclick="return confirm('هل تريد استرجاع هذا الإعداد؟')">

                                                    <i class="fas fa-undo"></i>

                                                </button>

                                            </form>


                                            {{-- Force Delete --}}
                                            <form action="{{ route('settings.forceDelete', $setting->id) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('سيتم حذف الإعداد نهائيًا، هل أنت متأكد؟');">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="btn btn-sm btn-danger"
                                                        title="حذف نهائي">

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

                    <i class="fas fa-trash-alt fa-3x text-muted mb-3"></i>

                    <h5 class="text-muted">
                        لا توجد إعدادات محذوفة
                    </h5>

                    <p class="text-muted mb-0">
                        لا يوجد حاليًا أي إعداد في سلة المحذوفات.
                    </p>

                </div>

            @endif

        </div>

    </div>

</div>
@endsection