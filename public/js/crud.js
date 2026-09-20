(function (window, document) {
    'use strict';

    const messages = {
        success: 'تمت العملية بنجاح.',
        error: 'تعذر إتمام العملية. يرجى المحاولة مرة أخرى.',
        deleteTitle: 'هل أنت متأكد؟',
        deleteText: 'لا يمكن التراجع عن عملية الحذف.',
        deleteSuccess: 'تمت عملية الحذف بنجاح.',
    };

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if (window.axios) {
        window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        if (csrfToken) window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
    }

    function normalizedMessage(data, icon = 'success', title = messages.success) {
        if (typeof data === 'string' && data.trim()) return { icon, title: data };
        return {
            icon: data?.icon || (data?.success === false ? 'error' : icon),
            title: data?.title || data?.message || title,
            text: data?.text,
        };
    }

    function showMessage(data, icon = 'success', title = messages.success) {
        const message = normalizedMessage(data, icon, title);
        if (!window.Swal) {
            window.alert(message.title);
            return Promise.resolve();
        }
        return window.Swal.fire({
            position: 'center', icon: message.icon, title: message.title, text: message.text,
            showConfirmButton: message.icon === 'error', confirmButtonText: 'حسنًا',
            timer: message.icon === 'error' ? undefined : 1500,
        });
    }

    function showErrorMessages(errors) {
        const alertElement = document.getElementById('error_alert');
        const list = document.getElementById('error_messages_ul');
        const items = Object.values(errors || {}).flat();
        if (!alertElement || !list) {
            return showMessage({ icon: 'error', title: items[0] || messages.error }, 'error', messages.error);
        }
        alertElement.hidden = false;
        list.replaceChildren();
        items.forEach(message => {
            const item = document.createElement('li');
            item.textContent = message;
            list.appendChild(item);
        });
    }

    function clearAndHideErrors() {
        const alertElement = document.getElementById('error_alert');
        const list = document.getElementById('error_messages_ul');
        if (alertElement) alertElement.hidden = true;
        if (list) list.replaceChildren();
    }

    function clearForm() { document.getElementById('create_form')?.reset(); }

    function handleError(error) {
        const errors = error?.response?.data?.errors;
        if (errors) return showErrorMessages(errors);
        return showMessage(error?.response?.data, 'error', messages.error);
    }

    function redirectFrom(response, fallback = null) {
        const target = response?.data?.redirect || fallback;
        if (target) window.location.href = target;
    }

    function request(method, url, data, options = {}) {
        return window.axios({ method, url, data, headers: options.headers })
            .then(response => {
                return showMessage(response.data, 'success', options.successMessage || messages.success)
                    .then(() => {
                        if (options.resetFirstForm) document.querySelector('form')?.reset();
                        if (options.clearForm) clearForm();
                        clearAndHideErrors();
                        if (options.removeRow) options.removeRow.closest('tr')?.remove();
                        redirectFrom(response, options.redirectUrl);
                        if (options.reload && !response?.data?.redirect) window.location.reload();
                        return response;
                    });
            })
            .catch(error => {
                handleError(error);
                return null;
            });
    }

    function store(url, data) {
        return request('post', url, data, { resetFirstForm: true });
    }
    function storepart(url, data) {
        return request('post', url, data, { clearForm: true });
    }
    function storeRoute(url, data) {
        return request('post', url, data, { headers: { 'Content-Type': 'multipart/form-data' } });
    }
    function storeRedirect(url, data, redirectUrl) {
        return request('post', url, data, { redirectUrl });
    }
    function update(url, data, redirectUrl = null) {
        return request('put', url, data, { redirectUrl });
    }
    function updateRoute(url, data) { return update(url, data); }
    function updateReload(url, data) { return request('put', url, data, { reload: true }); }
    function updatePage(url, data) { return request('post', url, data, { reload: true }); }
    function destroy(url, reference) {
        return request('delete', url, null, {
            removeRow: reference,
            successMessage: messages.deleteSuccess,
        });
    }

    function confirmDestroy(url, reference, confirmationMessage = messages.deleteText) {
        if (!window.Swal) {
            return window.confirm(confirmationMessage) ? destroy(url, reference) : Promise.resolve(false);
        }
        return window.Swal.fire({
            title: messages.deleteTitle, text: confirmationMessage, icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33',
            confirmButtonText: 'نعم', cancelButtonText: 'لا',
        }).then(result => result.isConfirmed ? destroy(url, reference) : false);
    }

    document.addEventListener('submit', event => {
        const form = event.target.closest('form[data-crud-delete]');
        if (!form || !window.axios) return;

        event.preventDefault();
        const submitButton = event.submitter || form.querySelector('[type="submit"]');
        if (submitButton) submitButton.disabled = true;

        confirmDestroy(form.action, submitButton, form.dataset.confirmMessage).finally(() => {
            if (submitButton?.isConnected) submitButton.disabled = false;
        });
    });

    document.addEventListener('submit', event => {
        const form = event.target.closest('form[data-swal-confirm]');
        if (!form || !window.Swal) return;

        event.preventDefault();
        window.Swal.fire({
            title: form.dataset.confirmTitle || messages.deleteTitle,
            text: form.dataset.confirmMessage || messages.deleteText,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم',
            cancelButtonText: 'لا',
        }).then(result => {
            if (result.isConfirmed) form.submit();
        });
    });

    Object.assign(window, {
        store, storepart, storeRoute, storeRedirect, update, updateRoute, updateReload,
        updatePage, confirmDestroy, destroy, showErrorMessages, clearAndHideErrors,
        clearForm, showMessage,
    });
})(window, document);
