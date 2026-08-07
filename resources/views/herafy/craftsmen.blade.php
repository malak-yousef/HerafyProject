@extends('herafy.parent')

@section('title' , 'جميع الحرفيين')

@section('content')
<!-- ============================================ -->
<!-- CRAFTSMEN SECTION -->
<!-- ============================================ -->
<section class="craftsmen-section py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">جميع الحرفيين</h2>
            <p class="text-muted">تعرف على نخبة من الحرفيين المحترفين في مجالات متنوعة</p>
        </div>

        <!-- فلتر البحث -->
        <div class="row mb-4">
            <div class="col-md-6 mx-auto">
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-warning border-0">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text"
                        class="form-control"
                        id="searchCraftsman"
                        placeholder="ابحث عن حرفي بالاسم أو الحرفة..."
                        oninput="filterCraftsmen()">
                </div>
            </div>
        </div>

        <!-- قائمة الحرفيين -->
        <div class="row g-4" id="craftsmenList">
            <!-- سيتم عرض الحرفيين هنا عبر JavaScript -->
        </div>

        <!-- رسالة عند عدم وجود نتائج -->
        <div id="noResults" class="text-center py-5" style="display: none;">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h5>لا يوجد حرفيون بهذا الاسم أو الحرفة</h5>
            <p class="text-muted">حاول تغيير كلمة البحث</p>
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
    // عرض الحرفيين
    function displayCraftsmen(craftsmen) {
        const container = document.getElementById('craftsmenList');
        const noResults = document.getElementById('noResults');

        if (!craftsmen || craftsmen.length === 0) {
            container.innerHTML = '';
            noResults.style.display = 'block';
            return;
        }

        noResults.style.display = 'none';

        container.innerHTML = craftsmen.map(craftsman => `
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm hover-card">
                        <div class="card-body text-center">
                            <div class="craftsman-avatar bg-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                 style="width: 80px; height: 80px; font-size: 2rem;">
                                <i class="fas fa-user text-dark"></i>
                            </div>
                            <h5 class="card-title">${craftsman.name || 'حرفي'}</h5>
                            <span class="badge bg-warning text-dark mb-2">${craftsman.craft || 'غير محدد'}</span>
                            <div class="mb-2">
                                <span class="text-warning">
                                    ${'★'.repeat(Math.floor(craftsman.rating || 0))}
                                    ${(craftsman.rating || 0) % 1 >= 0.5 ? '★' : ''}
                                    ${'☆'.repeat(5 - Math.ceil(craftsman.rating || 0))}
                                </span>
                                <span class="text-muted small">(${craftsman.totalReviews || 0} تقييم)</span>
                            </div>
                            <p class="card-text small text-muted">
                                <i class="fas fa-star text-warning"></i> 
                                ${craftsman.experience || 0} سنة خبرة
                            </p>
                            <p class="card-text small text-muted">
                                <i class="fas fa-calendar text-warning"></i> 
                                انضم: ${new Date(craftsman.joinDate).toLocaleDateString('ar')}
                            </p>
                            <a href="craftsman-profile.html?id=${craftsman.id}" class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-eye"></i> عرض الملف
                            </a>
                        </div>
                    </div>
                </div>
            `).join('');
    }

    // فلترة الحرفيين
    function filterCraftsmen() {
        const searchText = document.getElementById('searchCraftsman').value.toLowerCase().trim();
        const allCraftsmen = JSON.parse(localStorage.getItem('craftsmen') || '[]');

        if (!searchText) {
            displayCraftsmen(allCraftsmen);
            return;
        }

        const filtered = allCraftsmen.filter(c =>
            (c.name && c.name.toLowerCase().includes(searchText)) ||
            (c.craft && c.craft.toLowerCase().includes(searchText))
        );

        displayCraftsmen(filtered);
    }

    // تحميل الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        // جلب الحرفيين من localStorage
        let craftsmen = JSON.parse(localStorage.getItem('craftsmen') || '[]');

        // إذا لم يكن هناك حرفيين، نضيف بيانات افتراضية
        if (craftsmen.length === 0) {
            const defaultCraftsmen = [{
                    id: 1,
                    name: 'أحمد النجار',
                    craft: 'نجارة',
                    experience: 8,
                    rating: 4.8,
                    totalReviews: 120,
                    joinDate: new Date('2024-01-15').toISOString(),
                    phone: '0599 123 456',
                    email: 'ahmed@example.com'
                },
                {
                    id: 2,
                    name: 'محمد السباك',
                    craft: 'سباكة',
                    experience: 6,
                    rating: 4.9,
                    totalReviews: 85,
                    joinDate: new Date('2024-02-20').toISOString(),
                    phone: '0599 234 567',
                    email: 'mohammad@example.com'
                },
                {
                    id: 3,
                    name: 'خالد المبلط',
                    craft: 'تبليط',
                    experience: 10,
                    rating: 4.7,
                    totalReviews: 64,
                    joinDate: new Date('2024-03-10').toISOString(),
                    phone: '0599 345 678',
                    email: 'khaled@example.com'
                },
                {
                    id: 4,
                    name: 'سامي الكهربائي',
                    craft: 'كهرباء',
                    experience: 5,
                    rating: 4.6,
                    totalReviews: 52,
                    joinDate: new Date('2024-04-05').toISOString(),
                    phone: '0599 456 789',
                    email: 'sami@example.com'
                }
            ];
            localStorage.setItem('craftsmen', JSON.stringify(defaultCraftsmen));
            craftsmen = defaultCraftsmen;
        }

        displayCraftsmen(craftsmen);
    });

    // إضافة تأثيرات CSS
    document.addEventListener('DOMContentLoaded', function() {
        const style = document.createElement('style');
        style.textContent = `
                .craftsmen-section {
                    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
                    min-height: 90vh;
                }
                .craftsman-avatar {
                    background: linear-gradient(135deg, #FFC107, #e6a800);
                    transition: transform 0.3s ease;
                }
                .hover-card:hover .craftsman-avatar {
                    transform: scale(1.1);
                }
                .hover-card {
                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                }
                .hover-card:hover {
                    transform: translateY(-10px);
                    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15) !important;
                }
                #searchCraftsman:focus {
                    border-color: #FFC107;
                    box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
                }
            `;
        document.head.appendChild(style);
    });
</script>
@endsection