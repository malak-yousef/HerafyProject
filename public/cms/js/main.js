// ============================================
// الدوال الأساسية للـ Dashboard
// ============================================

// ===== جلب البيانات =====
function getData(key) {
    try {
        const data = localStorage.getItem(key);
        return data ? JSON.parse(data) : [];
    } catch {
        return [];
    }
}

// ===== حفظ البيانات =====
function saveData(key, data) {
    try {
        localStorage.setItem(key, JSON.stringify(data));
        return true;
    } catch {
        return false;
    }
}

// ===== تحديث البادجات =====
function updateBadges() {
    const users = getData('users');
    const craftsmen = getData('craftsmen');
    const requests = getData('requests');
    const offers = getData('offers');

    document.getElementById('usersBadge').textContent = users.length;
    document.getElementById('craftsmenBadge').textContent = craftsmen.length;
    document.getElementById('requestsBadge').textContent = requests.length;
    document.getElementById('offersBadge').textContent = offers.length;
}

// ===== عرض رسالة نجاح =====
function showSuccess(message) {
    Swal.fire({
        icon: 'success',
        title: '✅ تمت العملية',
        text: message,
        timer: 1500,
        showConfirmButton: false
    });
}

// ===== عرض رسالة خطأ =====
function showError(message) {
    Swal.fire({
        icon: 'error',
        title: '⚠️ خطأ',
        text: message,
        confirmButtonColor: '#FFC107'
    });
}

// ===== تأكيد الحذف =====
function confirmDelete(message, callback) {
    Swal.fire({
        title: '⚠️ هل أنت متأكد؟',
        text: message || 'سيتم حذف هذا العنصر نهائياً',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e17055',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'نعم، حذف',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            callback();
        }
    });
}

// ===== دالة timeAgo =====
function timeAgo(date) {
    const seconds = Math.floor((new Date() - date) / 1000);
    if (seconds < 60) return 'الآن';
    const minutes = Math.floor(seconds / 60);
    if (minutes < 60) return `منذ ${minutes} دقيقة`;
    const hours = Math.floor(minutes / 60);
    if (hours < 24) return `منذ ${hours} ساعة`;
    const days = Math.floor(hours / 24);
    if (days < 30) return `منذ ${days} يوم`;
    const months = Math.floor(days / 30);
    return `منذ ${months} شهر`;
}

// ===== تصدير البيانات =====
function exportData(type) {
    const data = getData(type);
    const json = JSON.stringify(data, null, 2);
    const blob = new Blob([json], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `${type}_${new Date().toISOString().slice(0, 10)}.json`;
    a.click();
    URL.revokeObjectURL(url);
    showSuccess(`تم تصدير ${data.length} سجل`);
}

// ===== مسح جميع البيانات =====
function clearAllData() {
    confirmDelete('سيتم مسح جميع البيانات نهائياً!', () => {
        localStorage.clear();
        location.reload();
        showSuccess('تم مسح جميع البيانات');
    });
}

// ===== تسجيل الخروج =====
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

