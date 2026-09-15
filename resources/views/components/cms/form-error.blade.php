@props(['field'])

@error($field)
    <div {{ $attributes->merge(['class' => 'invalid-feedback']) }}>{{ $message }}</div>
@enderror
