# TODO - نظام التقييمات (Reviews)

- [x] إنشاء ملف الـ Migration لجدول `reviews`
- [x] إنشاء ملف الـ Model `app/Models/Review.php`
- [x] إضافة علاقات `reviews()` في نماذج `Craftman`, `Customer`, `Order`
- [x] إنشاء `ReviewController` بدعم CRUD كامل + سلة المحذوفات
- [x] إنشاء صفحات العرض `cms/review/` (index, create, edit, show, trashed)
- [x] إنشاء `ReviewFactory` و `ReviewSeeder`
- [x] إضافة Routes في `routes/web.php`
- [x] إضافة قائمة "التقييمات" في `cms/parent.blade.php`
- [x] تشغيل `php artisan migrate` (تم بنجاح)
