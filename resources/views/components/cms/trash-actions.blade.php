@props(['restoreUrl', 'forceDeleteUrl', 'permission' => null, 'restoreMethod' => 'PATCH'])

@if(!$permission || auth('admin')->user()?->can($permission))
    <div class="d-flex flex-wrap justify-content-center gap-2 cms-trash-actions">
        <form method="POST" action="{{ $restoreUrl }}">
            @csrf
            @method($restoreMethod)
            <button type="submit" class="btn btn-sm btn-outline-success">
                <i class="fas fa-undo"></i> استعادة
            </button>
        </form>
        @if($forceDeleteUrl)
            <form method="POST" action="{{ $forceDeleteUrl }}" data-force-delete-form>
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-trash-alt"></i> حذف نهائي
                </button>
            </form>
        @endif
    </div>
@endif
