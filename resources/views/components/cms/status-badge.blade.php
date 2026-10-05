@props(['status'])

@php
    $state = \App\Support\StatusLabel::resolve($status);
@endphp
<span {{ $attributes->merge(['class' => 'badge '.$state['class']]) }}>{{ $state['label'] }}</span>