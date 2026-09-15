@props([
    'showUrl' => null,
    'editUrl' => null,
    'deleteUrl' => null,
    'showPermission' => null,
    'editPermission' => null,
    'deletePermission' => null,
    'deleteId' => null,
])

<div {{ $attributes->merge(['class' => 'cms-table-actions d-flex flex-wrap justify-content-center gap-1']) }}>
    @if($showUrl && (!$showPermission || auth('admin')->user()?->can($showPermission)))
        <a href="{{ $showUrl }}" class="btn btn-sm btn-info text-white" title="عرض" aria-label="عرض">
            <i class="fas fa-eye" aria-hidden="true"></i><span class="visually-hidden">عرض</span>
        </a>
    @endif

    @if($editUrl && (!$editPermission || auth('admin')->user()?->can($editPermission)))
        <a href="{{ $editUrl }}" class="btn btn-sm btn-warning" title="تعديل" aria-label="تعديل">
            <i class="fas fa-edit" aria-hidden="true"></i><span class="visually-hidden">تعديل</span>
        </a>
    @endif

    @if($deleteUrl && (!$deletePermission || auth('admin')->user()?->can($deletePermission)))
        <form action="{{ $deleteUrl }}" method="POST" class="d-inline" data-crud-delete data-confirm-message="هل أنت متأكد من حذف هذا العنصر؟">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger" title="حذف" aria-label="حذف">
                <i class="fas fa-trash" aria-hidden="true"></i><span class="visually-hidden">حذف</span>
            </button>
        </form>
    @endif
</div>
