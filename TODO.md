# خطة تقليص فورم إنشاء الحساب (جعل الحجم وسط)

## الخطوات

### تعديل public/cms/css/auth.css (القواعد الرئيسية - لأنها تستخدم !important)
- [x] 1. تقليص حشوة البطاقة `.auth-section .card` من `38px 40px` إلى `28px 30px`
- [x] 2. تصغير الأيقونة `.auth-section .card .auth-icon` إلى 56px و `1.4rem`
- [x] 3. تصغير عنوان البطاقة `.auth-section .card h3` إلى `1.5rem`
- [x] 4. خفض ارتفاع زر إنشاء الحساب `.auth-section .btn-warning` إلى 44px
- [x] 5. تقليص اللوحة الجانبية `.auth-side-panel` (padding + الأيقونة + عناصر المزايا)
- [x] 6. تصغير فاصل بيانات الحرفي `.craftsman-divider` (الأيقونة إلى 34px)

### تعديل resources/views/cms/auth/register.blade.php
- [x] 7. إزالة الـ inline style من أيقونة الصفحة والسماح لـ CSS بالتحكم
- [x] 8. تقليص حشوة `.auth-icon` الداخلية إلى 56px / `1.4rem`
- [x] 9. تقليل التباعد بين العنوان وكروت النوع `mb-4` إلى `mb-3`
- [x] 10. تقليص `.craftsman-divider` و `.divider-icon` (الأيقونة إلى 34px)
- [x] 11. إزالة `btn-lg` من زر إنشاء الحساب وخفض ارتفاعه إلى 44px
- [x] 12. تقليص اللوحة الجانبية الداخلية (padding + side-icon + feature items)

### التحقق النهائي
- [x] 13. عرض الصفحة والتأكد من أن الحجم وسط ومتناسق

---

## حالة التنفيذ (Implementation Tracking)

### خطوات التنفيذ المكتملة
- [x] أ. تعديل `.auth-side-panel` في register.blade.php: padding `35px 28px` → `28px 24px`
- [x] ب. تصغير `.side-icon`: `70px` → `60px`، `font-size: 1.8rem` → `1.5rem`، `margin-bottom: 18px` → `14px`
- [x] ج. تقليص `.features-list` margin-top: `28px` → `18px`
- [x] د. تصغير `.feature-item`: gap `13px` → `10px`، padding `14px 0` → `11px 0`
- [x] هـ. تصغير `.feature-icon`: `42px` → `34px` (مع flex-basis)
- [x] و. تحديث TODO.md بوضع علامة ✅ على البند 12
- [x] ز. التحقق البصري النهائي من الصفحة (البند 13)

