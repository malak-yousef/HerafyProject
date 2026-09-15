@props(['url', 'permission'])

@can($permission)
    <a href="{{ $url }}" {{ $attributes->merge(['class' => 'btn btn-outline-danger']) }}>
        <i class="fas fa-trash-can" aria-hidden="true"></i> سلة المحذوفات
    </a>
@endcan
