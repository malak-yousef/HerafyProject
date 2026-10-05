@extends('cms.parent')
@section('title', 'التصنيفات')
@section('main_content', 'التصنيفات')
@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4"><div><h4 class="mb-1 fw-bold">التصنيفات</h4><p class="text-muted mb-0">إدارة تصنيفات المهن.</p></div><div class="d-flex flex-wrap gap-2">@can('categories.create')<a href="{{ route('categories.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة تصنيف</a>@endcan<x-cms.trash-link :url="route('categories_trashed')" permission="categories.view" /></div></div>
    <div class="card cms-form-card"><div class="card-body p-0"><div class="table-responsive">
        <table class="table table-hover align-middle mb-0"><thead><tr><th>الاسم</th><th>الوصف</th><th>المهن</th><th>تاريخ الإضافة</th><th class="text-center">الإجراءات</th></tr></thead><tbody>
        @forelse($categories as $category)<tr><td class="fw-semibold">{{ $category->name }}</td><td>{{ Str::limit($category->description, 70) }}</td><td>{{ $category->professions_count }}</td><td>{{ $category->created_at?->format('Y-m-d') ?? '-' }}</td><td><x-cms.table-actions :show-url="route('categories.show', $category)" :edit-url="route('categories.edit', $category)" :delete-url="route('categories.destroy', $category)" show-permission="categories.view" edit-permission="categories.update" delete-permission="categories.delete" :delete-id="$category->id" /></td></tr>
        @empty<tr><td colspan="5"><x-cms.empty-state title="لا توجد تصنيفات" description="أضف أول تصنيف للبدء." icon="fa-layer-group" /></td></tr>@endforelse
        </tbody></table>
    </div></div></div>
    @if($categories->hasPages())<div class="mt-4">{{ $categories->links() }}</div>@endif
</div>
@endsection
