@if($errors->any())
    <div {{ $attributes->merge(['class' => 'alert alert-danger cms-form-errors-summary']) }} role="alert">
        <i class="fas fa-circle-exclamation" aria-hidden="true"></i>
        يرجى مراجعة الحقول التالية وتصحيح الأخطاء.
    </div>
@endif
