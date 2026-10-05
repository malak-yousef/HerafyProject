@props([
    'value',
    'placeholder' => '-',
])

<span {{ $attributes->merge(['class' => 'text-nowrap']) }}>{{ \App\Support\Money::format($value, $placeholder) }}</span>