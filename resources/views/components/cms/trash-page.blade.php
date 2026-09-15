@props([
    'items',
    'title',
    'backUrl',
    'columns',
    'restoreRoute',
    'restoreMethod' => 'PATCH',
    'forceDeleteRoute' => null,
    'permission',
    'bulkForceDeleteUrl' => null,
])
@php($showBulk = $bulkForceDeleteUrl && auth('admin')->user()?->can($permission))

<div class="container-fluid" data-trash-page>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">{{ $title }} — سلة المحذوفات</h4>
            <p class="text-muted mb-0">إدارة العناصر المحذوفة واستعادتها أو حذفها نهائيًا.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @if($showBulk && $items->count())
                <form id="bulk-trash-form" method="POST" action="{{ $bulkForceDeleteUrl }}" data-force-delete-form>
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" data-trash-bulk-button disabled>
                        حذف المحدد نهائيًا (<span data-trash-selected-count>0</span>)
                    </button>
                </form>
            @endif
            <a href="{{ $backUrl }}" class="btn btn-outline-secondary">العودة للقائمة</a>
        </div>
    </div>

    <div class="card cms-form-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            @if($showBulk)
                                <th class="text-center" style="width:44px">
                                    <input type="checkbox" class="form-check-input" data-trash-select-all aria-label="تحديد عناصر الصفحة الحالية">
                                </th>
                            @endif
                            @foreach($columns as $label => $path)
                                <th>{{ $label }}</th>
                            @endforeach
                            <th>تاريخ الحذف</th>
                            <th class="text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                @if($showBulk)
                                    <td class="text-center">
                                        <input form="bulk-trash-form" type="checkbox" class="form-check-input" name="ids[]" value="{{ $item->getKey() }}" data-trash-item aria-label="تحديد {{ data_get($item, array_values($columns)[0]) }}">
                                    </td>
                                @endif
                                @foreach($columns as $label => $path)
                                    @php($value = data_get($item, $path))
                                    <td>
                                        @if($path === 'status')
                                            <x-cms.status-badge :status="$value" />
                                        @else
                                            {{ filled($value) ? Str::limit((string) $value, 80) : '—' }}
                                        @endif
                                    </td>
                                @endforeach
                                <td>{{ $item->deleted_at?->format('Y-m-d H:i') ?? '—' }}</td>
                                <td>
                                    <x-cms.trash-actions
                                        :restore-url="route($restoreRoute, $item->getKey())"
                                        :force-delete-url="$forceDeleteRoute ? route($forceDeleteRoute, $item->getKey()) : null"
                                        :permission="$permission"
                                        :restore-method="$restoreMethod"
                                    />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($columns) + 2 + ($showBulk ? 1 : 0) }}">
                                    <x-cms.empty-state title="لا توجد عناصر محذوفة." icon="fa-trash-restore" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($items->hasPages())
        <div class="mt-4">{{ $items->links() }}</div>
    @endif
</div>
