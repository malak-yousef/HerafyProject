@extends('cms.parent')

@section('title', 'طلبات التوثيق')

@section('main_content', 'طلبات التوثيق')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                <i class="fas fa-user-check text-warning"></i>
                طلبات التوثيق
            </h4>

            <p class="text-muted mb-0">
                إدارة ومراجعة طلبات توثيق الحرفيين
            </p>
        </div>

        <x-cms.trash-link :url="route('verifications.trashed')" permission="verifications.view" />
    </div>



    <div class="card shadow-sm border-0">

        <div class="card-body">

            <form method="GET" class="row g-2 mb-3 align-items-end"><div class="col-md-4"><label class="form-label">بحث</label><input name="search" value="{{ request('search') }}" class="form-control" maxlength="100"></div><div class="col-md-3"><label class="form-label">الحالة</label><select name="status" class="form-select"><option value="">الكل</option>@foreach(['pending','approved','rejected'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>@endforeach</select></div><div class="col-md-2"><button class="btn btn-primary" type="submit">بحث</button></div><div class="col-md-3"><a href="{{ route('verifications.index') }}">إعادة ضبط</a></div></form>
            <form method="GET" class="row g-2 mb-3"><div class="col-md-3"><input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control"></div><div class="col-md-3"><input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control"></div><div class="col-md-3"><select name="sort" class="form-select"><option value="newest">الأحدث</option><option value="oldest">الأقدم</option><option value="pending_first">المعلقة أولًا</option></select></div><div class="col-md-3"><button class="btn btn-outline-primary" type="submit">تطبيق</button></div></form><div class="table-responsive">

                <table class="table table-hover align-middle text-center">

                    <thead class="table-light">

                        <tr>
                            <th>#</th>
                            <th>الحرفي</th>
                            <th>الوثيقة</th>
                            <th>الحالة</th>
                            <th>تاريخ الطلب</th>
                            <th>الإجراءات</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($verifications as $verification)

                            <tr>

                                <td>
                                    {{ $loop->iteration }}
                                </td>

                                <td>
                                   {{ $verification->craftman->user->name ?? 'غير معروف' }}
                                </td>

                                <td>

                                    <a href="{{ asset('storage/' . $verification->document_path) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-primary">

                                        <i class="fas fa-file-alt"></i>
                                        عرض الوثيقة

                                    </a>

                                </td>

                                <td>

                                    <x-cms.status-badge :status="$verification->status" />
                                    @if($verification->status === 'pending')

                                        <span class="badge bg-warning text-dark">
                                            قيد المراجعة
                                        </span>

                                    @elseif($verification->status === 'approved')

                                        <span class="badge bg-success">
                                            مقبول
                                        </span>

                                    @elseif($verification->status === 'rejected')

                                        <span class="badge bg-danger">
                                            مرفوض
                                        </span>

                                    @endif

                                </td>

                                <td>
                                    {{ $verification->created_at->format('Y-m-d') }}
                                </td>

                                <td>

                                    <x-cms.table-actions :show-url="route('verifications.show', $verification->id)" :edit-url="route('verifications.edit', $verification->id)" :delete-url="route('verifications.destroy', $verification->id)" show-permission="verifications.view" edit-permission="verifications.update" delete-permission="verifications.delete" />
                                    <a href="{{ route('verifications.show', $verification->id) }}"
                                       class="btn btn-sm btn-info text-white legacy-verification-action">

                                        <i class="fas fa-eye"></i>
                                        عرض

                                    </a>

                                    <a href="{{ route('verifications.edit', $verification->id) }}"
                                       class="btn btn-sm btn-warning legacy-verification-action">

                                        <i class="fas fa-edit"></i>
                                        مراجعة

                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6" class="text-muted py-4"><x-cms.empty-state :title="request()->hasAny(['search','status','date_from','date_to','sort']) ? 'لا توجد نتائج مطابقة لخيارات البحث.' : 'لا توجد طلبات توثيق حتى الآن'" :reset-url="route('verifications.index')" />

                                    <i class="fas fa-inbox fa-2x mb-2"></i>

                                    <p class="mb-0">
                                        لا توجد طلبات توثيق حاليًا
                                    </p>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            <div class="mt-3">

                {{ $verifications->links() }}

            </div>

        </div>

    </div>

</div>

@endsection
