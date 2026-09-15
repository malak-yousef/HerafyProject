@props(['title', 'description' => null, 'icon' => 'fa-inbox', 'resetUrl' => null, 'resetLabel' => 'إعادة تعيين الفلاتر'])
<div {{ $attributes->merge(['class' => 'cms-empty-state text-center text-muted py-5']) }}>
    <i class="fas {{ $icon }} fa-2x mb-3" aria-hidden="true"></i>
    <p class="mb-1">{{ $title }}</p>
    @if($description)<small>{{ $description }}</small>@endif
    @if($resetUrl)<div class="mt-3"><a class="btn btn-outline-secondary btn-sm" href="{{ $resetUrl }}">{{ $resetLabel }}</a></div>@endif
</div>
