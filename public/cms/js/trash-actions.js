(function (window, document) {
    'use strict';

    const confirmation = 'هل أنت متأكد من الحذف النهائي؟ لا يمكن التراجع عن هذه العملية.';

    document.addEventListener('submit', (event) => {
        const form = event.target.closest('[data-force-delete-form]');
        if (!form) return;

        event.preventDefault();

        const proceed = () => form.submit();

        if (window.Swal) {
            window.Swal.fire({
                title: 'حذف نهائي',
                text: confirmation,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء',
            }).then((result) => {
                if (result.isConfirmed) proceed();
            });
            return;
        }

        if (window.confirm(confirmation)) {
            proceed();
        }
    });

    document.querySelectorAll('[data-trash-page]').forEach((page) => {
        const selectAll = page.querySelector('[data-trash-select-all]');
        const items = [...page.querySelectorAll('[data-trash-item]')];
        const bulkButton = page.querySelector('[data-trash-bulk-button]');
        const count = page.querySelector('[data-trash-selected-count]');

        if (!selectAll || items.length === 0) {
            return;
        }

        const refresh = () => {
            const selected = items.filter((item) => item.checked).length;
            selectAll.checked = selected === items.length;
            selectAll.indeterminate = selected > 0 && selected < items.length;
            if (bulkButton) bulkButton.disabled = selected === 0;
            if (count) count.textContent = String(selected);
        };

        selectAll.addEventListener('change', () => {
            items.forEach((item) => { item.checked = selectAll.checked; });
            refresh();
        });
        items.forEach((item) => item.addEventListener('change', refresh));
        refresh();
    });
})(window, document);
