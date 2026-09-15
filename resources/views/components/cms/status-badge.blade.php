@props(['status'])
@php
    $states = [
        'pending' => ['label' => 'قيد الانتظار', 'class' => 'bg-warning text-dark'],
        'approved' => ['label' => 'معتمد', 'class' => 'bg-success'],
        'rejected' => ['label' => 'مرفوض', 'class' => 'bg-danger'],
        'reviewing' => ['label' => 'قيد المراجعة', 'class' => 'bg-info text-dark'],
        'resolved' => ['label' => 'تم الحل', 'class' => 'bg-success'],
        'accepted' => ['label' => 'مقبول', 'class' => 'bg-primary'],
        'in_progress' => ['label' => 'قيد التنفيذ', 'class' => 'bg-primary'],
        'completed' => ['label' => 'مكتمل', 'class' => 'bg-success'],
        'cancelled' => ['label' => 'ملغى', 'class' => 'bg-danger'],
        'active' => ['label' => 'نشط', 'class' => 'bg-success'],
        'inactive' => ['label' => 'غير نشط', 'class' => 'bg-secondary'],
        'in_active' => ['label' => 'غير نشط', 'class' => 'bg-secondary'],
        1 => ['label' => 'نشط', 'class' => 'bg-success'],
        0 => ['label' => 'غير نشط', 'class' => 'bg-secondary'],
        'blocked' => ['label' => 'محظور', 'class' => 'bg-dark'],
    ];
    $state = $states[$status] ?? ['label' => 'غير معروف', 'class' => 'bg-secondary'];
@endphp
<span {{ $attributes->merge(['class' => 'badge '.$state['class']]) }}>{{ $state['label'] }}</span>
