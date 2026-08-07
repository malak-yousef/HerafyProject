@extends('herafy.parent')

@section('title' , 'قدم طلبك')

@section('style')
@endsection

@section('content')
<!-- ============================================ -->
<!-- REQUEST SECTION -->
<!-- ============================================ -->
<section class="request-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0 rounded-4 p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="request-icon bg-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                            style="width: 70px; height: 70px; font-size: 2rem;">
                            <i class="fas fa-pen text-dark"></i>
                        </div>
                        <h3 class="fw-bold">قدم طلبك الآن</h3>
                        <p class="text-muted">املأ البيانات التالية وسيتم إرسال طلبك للحرفيين</p>
                    </div>

                    <!-- نموذج تقديم الطلب -->
                    <form id="requestForm" onsubmit="handleRequest(event)">
                        <!-- نوع الحرفة -->
                        <div class="mb-3">
                            <label for="craftType" class="form-label fw-bold">
                                <i class="fas fa-tools text-warning"></i> نوع الحرفة
                            </label>
                            <select class="form-select form-select-lg" id="craftType" required>
                                <option value="">اختر نوع الحرفة</option>
                                <option value="carpentry">نجارة</option>
                                <option value="plumbing">سباكة</option>
                                <option value="tiling">تبليط</option>
                                <option value="electricity">كهرباء</option>
                                <option value="painting">دهان</option>
                                <option value="welding">لحام</option>
                                <option value="masonry">بناء</option>
                                <option value="carpet">سجاد وموكيت</option>
                                <option value="glass">زجاج</option>
                                <option value="ac">تكييف وتبريد</option>
                                <option value="other">أخرى</option>
                            </select>
                        </div>

                        <!-- حقل الحرفة المخصصة -->
                        <div id="customCraftRequest" style="display: none;">
                            <div class="mb-3">
                                <label for="customCraftInput" class="form-label fw-bold">
                                    <i class="fas fa-pen text-warning"></i> اكتب الحرفة
                                </label>
                                <input type="text" class="form-control form-control-lg" id="customCraftInput"
                                    placeholder="مثال: حدادة، زجاج، رخام، ...">
                            </div>
                        </div>

                        <!-- عنوان الطلب -->
                        <div class="mb-3">
                            <label for="requestTitle" class="form-label fw-bold">
                                <i class="fas fa-heading text-warning"></i> عنوان الطلب
                            </label>
                            <input type="text" class="form-control form-control-lg" id="requestTitle"
                                placeholder="مثال: تركيب مطبخ جديد" required>
                        </div>

                        <!-- تفاصيل الطلب -->
                        <div class="mb-3">
                            <label for="requestDetails" class="form-label fw-bold">
                                <i class="fas fa-align-left text-warning"></i> تفاصيل الطلب
                            </label>
                            <textarea class="form-control form-control-lg" id="requestDetails" rows="5"
                                placeholder="صف بالتفصيل العمل المطلوب، المواد المستخدمة، القياسات، ..."
                                required></textarea>
                        </div>

                        <div class="row">
                            <!-- الموقع -->
                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label fw-bold">
                                    <i class="fas fa-map-marker-alt text-warning"></i> الموقع
                                </label>
                                <input type="text" class="form-control form-control-lg" id="location"
                                    placeholder="المدينة، الحي، الشارع" required>
                            </div>

                            <!-- الميزانية المتوقعة -->
                            <div class="col-md-6 mb-3">
                                <label for="budget" class="form-label fw-bold">
                                    <i class="fas fa-money-bill text-warning"></i> الميزانية المتوقعة (₪)
                                </label>
                                <input type="number" class="form-control form-control-lg" id="budget"
                                    placeholder="المبلغ المتوقع" min="0">
                            </div>
                        </div>

                        <div class="row">
                            <!-- التاريخ المطلوب -->
                            <div class="col-md-6 mb-3">
                                <label for="dateNeeded" class="form-label fw-bold">
                                    <i class="fas fa-calendar text-warning"></i> التاريخ المطلوب
                                </label>
                                <input type="date" class="form-control form-control-lg" id="dateNeeded" required>
                            </div>

                            <!-- الوقت المطلوب -->
                            <div class="col-md-6 mb-3">
                                <label for="timeNeeded" class="form-label fw-bold">
                                    <i class="fas fa-clock text-warning"></i> الوقت المناسب
                                </label>
                                <select class="form-select form-select-lg" id="timeNeeded">
                                    <option value="morning">الصباح (8-12)</option>
                                    <option value="afternoon">بعد الظهر (12-4)</option>
                                    <option value="evening">المساء (4-8)</option>
                                    <option value="any">أي وقت</option>
                                </select>
                            </div>
                        </div>

                        <!-- صور إضافية -->
                        <div class="mb-3">
                            <label for="requestImages" class="form-label fw-bold">
                                <i class="fas fa-image text-warning"></i> صور إضافية (اختياري)
                            </label>
                            <input type="file" class="form-control form-control-lg" id="requestImages"
                                accept="image/*" multiple>
                            <small class="text-muted">يمكنك رفع صور توضيحية للعمل المطلوب</small>
                        </div>

                        <!-- زر الإرسال -->
                        <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold">
                            <i class="fas fa-paper-plane"></i> إرسال الطلب
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- ============================================ -->
<!-- السكربت الخاص بالصفحة -->
<!-- ============================================ -->
<script>
    // معالجة تغيير نوع الحرفة
    document.getElementById('craftType').addEventListener('change', function() {
        const customField = document.getElementById('customCraftRequest');
        if (this.value === 'other') {
            customField.style.display = 'block';
            customField.style.animation = 'slideDown 0.3s ease forwards';
        } else {
            customField.style.display = 'none';
        }
    });

    // معالجة تقديم الطلب
    function handleRequest(event) {
        event.preventDefault();

        const craftType = document.getElementById('craftType').value;
        const customCraft = document.getElementById('customCraftInput').value.trim();
        const title = document.getElementById('requestTitle').value.trim();
        const details = document.getElementById('requestDetails').value.trim();
        const location = document.getElementById('location').value.trim();
        const budget = document.getElementById('budget').value;
        const dateNeeded = document.getElementById('dateNeeded').value;
        const timeNeeded = document.getElementById('timeNeeded').value;

        // التحقق من البيانات
        if (!craftType) {
            Swal.fire({
                icon: 'error',
                title: '⚠️ نوع الحرفة مطلوب',
                text: 'يرجى اختيار نوع الحرفة',
                confirmButtonColor: '#FFC107'
            });
            return;
        }

        if (craftType === 'other' && !customCraft) {
            Swal.fire({
                icon: 'error',
                title: '⚠️ الحرفة مطلوبة',
                text: 'يرجى كتابة نوع الحرفة',
                confirmButtonColor: '#FFC107'
            });
            return;
        }

        if (!title || !details || !location || !dateNeeded) {
            Swal.fire({
                icon: 'error',
                title: '⚠️ حقول ناقصة',
                text: 'يرجى ملء جميع الحقول المطلوبة',
                confirmButtonColor: '#FFC107'
            });
            return;
        }

        // إعداد بيانات الطلب
        const craftDisplay = craftType === 'other' ? customCraft :
            document.getElementById('craftType').options[document.getElementById('craftType').selectedIndex].text;

        const requestData = {
            id: Date.now(),
            craft: craftType,
            craftDisplay: craftDisplay,
            title: title,
            details: details,
            location: location,
            budget: budget || 'غير محدد',
            dateNeeded: dateNeeded,
            timeNeeded: timeNeeded,
            status: 'pending',
            createdAt: new Date().toISOString(),
            userId: localStorage.getItem('currentUser') ?
                JSON.parse(localStorage.getItem('currentUser')).id : null,
            userName: localStorage.getItem('currentUser') ?
                JSON.parse(localStorage.getItem('currentUser')).fullName : 'زائر'
        };

        console.log('📝 بيانات الطلب:', requestData);

        // تخزين الطلب في localStorage
        try {
            let requests = JSON.parse(localStorage.getItem('requests') || '[]');
            requests.push(requestData);
            localStorage.setItem('requests', JSON.stringify(requests));
            console.log('✅ تم تخزين الطلب بنجاح');
        } catch (e) {
            console.error('❌ خطأ في تخزين الطلب:', e);
        }

        // عرض رسالة النجاح
        const button = event.target.querySelector('button[type="submit"]');
        const originalText = button.innerHTML;

        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإرسال...';
        button.disabled = true;

        setTimeout(() => {
            Swal.fire({
                icon: 'success',
                title: '✅ تم إرسال الطلب بنجاح',
                html: `
                        <p>تم إرسال طلبك إلى الحرفيين في مجال <strong>${craftDisplay}</strong></p>
                        <p class="text-muted small">ستصلك عروض من حرفيين قريباً</p>
                    `,
                confirmButtonColor: '#FFC107',
                confirmButtonText: 'عرض طلباتي'
            }).then(() => {
                window.location.href = 'dashboard-user.html';
            });
        }, 1500);
    }

    // إضافة تأثيرات CSS
    document.addEventListener('DOMContentLoaded', function() {
        const style = document.createElement('style');
        style.textContent = `
                .request-section {
                    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
                    min-height: 90vh;
                }
                .request-section .card {
                    background: rgba(255, 255, 255, 0.95);
                    backdrop-filter: blur(10px);
                }
                #customCraftRequest {
                    animation: slideDown 0.3s ease forwards;
                }
                @keyframes slideDown {
                    from {
                        opacity: 0;
                        transform: translateY(-10px);
                        max-height: 0;
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                        max-height: 200px;
                    }
                }
                .form-control:focus, .form-select:focus {
                    border-color: #FFC107;
                    box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
                }
            `;
        document.head.appendChild(style);
    });
</script>
@endsection