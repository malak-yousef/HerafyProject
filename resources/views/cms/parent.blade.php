<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> لوحة التحكم - حرفي || @yield('title') </title>


    <!-- Bootstrap 5 RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- CSS الخاص بالـ Dashboard -->
    <link rel="stylesheet" href="{{ asset('cms/css/style.css') }}">

    @yield('styles')

</head>

<body>

    <!-- ============================================ -->
    <!-- SIDEBAR -->
    <!-- ============================================ -->
    <nav class="sidebar">
        <div class="sidebar-brand">
            <h3><i class="fas fa-hammer"></i> {{env('APP_NAME')}}</h3>
            <small>لوحة التحكم</small>
        </div>

        <ul class="sidebar-nav">
            <li class="nav-item">
                <a class="nav-link active" href="index.html">
                    <i class="fas fa-chart-pie"></i> الرئيسية
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="users.html">
                    <i class="fas fa-users"></i> المستخدمون
                    <span class="badge" id="usersBadge">0</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('craftmen.index') }}">
                    <i class="fas fa-tools"></i> الحرفيون
                    <span class="badge" id="craftsmenBadge">0</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="requests.html">
                    <i class="fas fa-clipboard-list"></i> الطلبات
                    <span class="badge" id="requestsBadge">0</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="offers.html">
                    <i class="fas fa-handshake"></i> العروض
                    <span class="badge" id="offersBadge">0</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="settings.html">
                    <i class="fas fa-cog"></i> الإعدادات
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-info" href="#" onclick="quickEdit()">
                    <i class="fas fa-pen-fancy"></i> تعديل سريع
                    <!-- <span class="badge bg-info text-white">جديد</span> -->
                </a>
            </li>
            <li class="nav-item mt-2">
                <a class="nav-link text-danger" href="#" onclick="logout()">
                    <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
                </a>
            </li>
        </ul>
        <hr>
        <div class="text-center small">
            <small>© {{ now()->year}} - {{ now()->year+1 }} {{ env('APP_NAME') }}</small>
        </div>
    </nav>

    <!-- ============================================ -->
    <!-- MAIN CONTENT -->
    <!-- ============================================ -->
    <main class="main-content">

        <!-- ===== HEADER ===== -->
        <div class="page-header">
            <div>
                <h4 class="fw-bold mb-0">📊 لوحة التحكم @yield('main_content') </h4>
                <p class="text-muted small">نظرة عامة على أداء المنصة</p>
            </div>
            <div>
                <span class="badge bg-success">
                    <i class="fas fa-circle"></i> النظام يعمل
                </span>
                <span class="badge bg-info text-white" id="lastUpdate">
                    <i class="fas fa-clock"></i> الآن
                </span>
            </div>
        </div>

        <!-- ===== STATISTICS ===== -->
        <!-- <div class="stats-grid">
            <div class="stat-card" style="border-right-color: #6c5ce7;">
                <div class="stat-info">
                    <div class="stat-number" id="statUsers">0</div>
                    <div class="stat-label">المستخدمون</div>
                </div>
                <div class="stat-icon" style="background: rgba(108, 92, 231, 0.15); color: #6c5ce7;">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stat-card" style="border-right-color: #00b894;">
                <div class="stat-info">
                    <div class="stat-number" id="statCraftsmen">0</div>
                    <div class="stat-label">الحرفيون</div>
                </div>
                <div class="stat-icon" style="background: rgba(0, 184, 148, 0.15); color: #00b894;">
                    <i class="fas fa-tools"></i>
                </div>
            </div>
            <div class="stat-card" style="border-right-color: #0984e3;">
                <div class="stat-info">
                    <div class="stat-number" id="statRequests">0</div>
                    <div class="stat-label">الطلبات</div>
                </div>
                <div class="stat-icon" style="background: rgba(9, 132, 227, 0.15); color: #0984e3;">
                    <i class="fas fa-clipboard-list"></i>
                </div>
            </div>
            <div class="stat-card" style="border-right-color: #e17055;">
                <div class="stat-info">
                    <div class="stat-number" id="statOffers">0</div>
                    <div class="stat-label">العروض</div>
                </div>
                <div class="stat-icon" style="background: rgba(225, 112, 85, 0.15); color: #e17055;">
                    <i class="fas fa-handshake"></i>
                </div>
            </div>
        </div> -->

        <!-- ===== CHARTS ===== -->
        <!-- <div class="charts-grid">
            <div class="chart-card">
                <h6><i class="fas fa-chart-line text-warning"></i> الطلبات حسب الشهر</h6>
                <canvas id="requestsChart"></canvas>
            </div>
            <div class="chart-card">
                <h6><i class="fas fa-chart-pie text-warning"></i> توزيع الحرفيين</h6>
                <canvas id="craftsmenChart"></canvas>
            </div>
        </div> -->

        <!-- ===== RECENT ACTIVITIES ===== -->
        <!-- <div class="table-card">
            <div class="table-header">
                <h6><i class="fas fa-bolt text-warning"></i> آخر النشاطات</h6>
            </div>
            <div id="recentActivities">
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                    <p>جاري التحميل...</p>
                </div>
            </div>
        </div> -->

        @yield('content')
    </main>

    <!-- ============================================ -->
    <!-- JavaScript -->
    <!-- ============================================ -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('cms/js/main.js') }}"></script>
    <script>
        // ============================================
        // تحميل البيانات
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {
            // جلب البيانات من localStorage أو API
            const users = JSON.parse(localStorage.getItem('users') || '[]');
            const craftsmen = JSON.parse(localStorage.getItem('craftsmen') || '[]');
            const requests = JSON.parse(localStorage.getItem('requests') || '[]');
            const offers = JSON.parse(localStorage.getItem('offers') || '[]');

            // تحديث الإحصائيات
            document.getElementById('statUsers').textContent = users.length;
            document.getElementById('statCraftsmen').textContent = craftsmen.length;
            document.getElementById('statRequests').textContent = requests.length;
            document.getElementById('statOffers').textContent = offers.length;

            // تحديث البادجات
            document.getElementById('usersBadge').textContent = users.length;
            document.getElementById('craftsmenBadge').textContent = craftsmen.length;
            document.getElementById('requestsBadge').textContent = requests.length;
            document.getElementById('offersBadge').textContent = offers.length;

            // تحديث وقت آخر تحديث
            document.getElementById('lastUpdate').innerHTML =
                `<i class="fas fa-clock"></i> ${new Date().toLocaleString('ar')}`;

            // رسم الرسوم البيانية
            drawCharts(requests, craftsmen);

            // عرض آخر النشاطات
            renderActivities(users, craftsmen, requests, offers);
        });

        // ============================================
        // دالة رسم الرسوم البيانية
        // ============================================
        function drawCharts(requests, craftsmen) {
            // الطلبات حسب الشهر
            const months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'];
            const data = months.map((_, i) => {
                return requests.filter(r => new Date(r.createdAt).getMonth() === i).length;
            });

            new Chart(document.getElementById('requestsChart'), {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'الطلبات',
                        data: data,
                        backgroundColor: 'rgba(255, 193, 7, 0.6)',
                        borderColor: '#FFC107',
                        borderWidth: 2,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });

            // توزيع الحرفيين
            const craftCounts = {};
            craftsmen.forEach(c => {
                craftCounts[c.craft] = (craftCounts[c.craft] || 0) + 1;
            });

            new Chart(document.getElementById('craftsmenChart'), {
                type: 'doughnut',
                data: {
                    labels: Object.keys(craftCounts),
                    datasets: [{
                        data: Object.values(craftCounts),
                        backgroundColor: ['#6c5ce7', '#00b894', '#0984e3', '#e17055', '#fdcb6e']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    size: 10
                                }
                            }
                        }
                    }
                }
            });
        }

        // ============================================
        // دالة عرض آخر النشاطات
        // ============================================
        function renderActivities(users, craftsmen, requests, offers) {
            const container = document.getElementById('recentActivities');
            let activities = [];

            users.slice(-3).forEach(u => {
                activities.push({
                    text: `مستخدم جديد: ${u.fullName || u.firstName}`,
                    time: u.createdAt
                });
            });
            craftsmen.slice(-3).forEach(c => {
                activities.push({
                    text: `حرفي جديد: ${c.name} (${c.craft})`,
                    time: c.joinDate
                });
            });
            requests.slice(-3).forEach(r => {
                activities.push({
                    text: `طلب جديد: ${r.title}`,
                    time: r.createdAt
                });
            });
            offers.slice(-3).forEach(o => {
                activities.push({
                    text: `عرض جديد من ${o.craftsmanName}`,
                    time: o.createdAt
                });
            });

            activities.sort((a, b) => new Date(b.time) - new Date(a.time));
            activities = activities.slice(0, 8);

            if (activities.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <p>لا توجد نشاطات</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = activities.map(a => `
                <div class="activity-item">
                    <p class="mb-0 small">${a.text}</p>
                    <small class="text-muted">${timeAgo(new Date(a.time))}</small>
                </div>
            `).join('');
        }

        // ============================================
        // دالة timeAgo
        // ============================================
        function timeAgo(date) {
            const seconds = Math.floor((new Date() - date) / 1000);
            if (seconds < 60) return 'الآن';
            const minutes = Math.floor(seconds / 60);
            if (minutes < 60) return `منذ ${minutes} دقيقة`;
            const hours = Math.floor(minutes / 60);
            if (hours < 24) return `منذ ${hours} ساعة`;
            const days = Math.floor(hours / 24);
            return `منذ ${days} يوم`;
        }
        // ============================================
        // 5. التعديل السريع
        // ============================================
        function quickEdit() {
            Swal.fire({
                title: '✏️ تعديل سريع',
                html: `
                    <div class="text-start">
                        <p class="text-muted">اختر العنصر الذي تريد تعديله:</p>
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary" onclick="showSection('users'); Swal.close();">
                                <i class="fas fa-users"></i> تعديل مستخدم
                            </button>
                            <button class="btn btn-outline-warning" onclick="showSection('craftsmen'); Swal.close();">
                                <i class="fas fa-tools"></i> تعديل حرفي
                            </button>
                            <button class="btn btn-outline-success" onclick="showSection('requests'); Swal.close();">
                                <i class="fas fa-clipboard-list"></i> تعديل طلب
                            </button>
                            <button class="btn btn-outline-info" onclick="showSection('offers'); Swal.close();">
                                <i class="fas fa-handshake"></i> تعديل عرض
                            </button>
                        </div>
                    </div>
                `,
                confirmButtonColor: '#FFC107',
                confirmButtonText: 'إغلاق'
            });
        }

        // ============================================
        // دالة تسجيل الخروج
        // ============================================
        function logout() {
            Swal.fire({
                title: 'تسجيل الخروج',
                text: 'هل أنت متأكد؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#FFC107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    localStorage.removeItem('currentUser');
                    window.location.href = '../index.html';
                }
            });
        }
    </script>
    @yield('script')
</body>