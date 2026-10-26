(function () {
    'use strict';

    const optionsAfterPlaceholder = select => Array.from(select.options).slice(1);

    const setOptionAvailability = (option, available) => {
        option.hidden = !available;
        option.disabled = !available;
    };

    const filterOptions = (select, predicate) => {
        let availableCount = 0;

        optionsAfterPlaceholder(select).forEach(option => {
            const available = predicate(option);
            setOptionAvailability(option, available);
            if (available) availableCount += 1;
        });

        return availableCount;
    };

    const setPlaceholder = (select, label) => {
        if (select.options.length > 0) select.options[0].textContent = label;
    };

    const initOffer = (orderSelector, craftmanSelector) => {
        const order = document.querySelector(orderSelector);
        const craftman = document.querySelector(craftmanSelector);
        if (!order || !craftman || craftman.dataset.relationshipFilterInitialized === 'true') return;

        craftman.dataset.relationshipFilterInitialized = 'true';

        const refresh = () => {
            const professionId = order.selectedOptions[0]?.dataset.professionId || '';
            const selectedCraftman = craftman.value;
            const availableCount = filterOptions(craftman, option => professionId !== '' && option.dataset.professionId === professionId);
            const selectedIsAvailable = optionsAfterPlaceholder(craftman).some(option => option.value === selectedCraftman && !option.disabled);

            if (!selectedIsAvailable) craftman.value = '';
            craftman.disabled = professionId === '' || availableCount === 0;
            setPlaceholder(craftman, professionId === '' ? 'اختر الطلب أولاً' : (availableCount === 0 ? 'لا يوجد حرفيون متوافقون' : 'اختر الحرفي'));
        };

        order.addEventListener('change', refresh);
        refresh();
    };

    const initServiceJob = (orderSelector, offerSelector, customerSelector, craftmanSelector) => {
        const order = document.querySelector(orderSelector);
        const offer = document.querySelector(offerSelector);
        const customer = document.querySelector(customerSelector);
        const craftman = document.querySelector(craftmanSelector);
        if (!order || !offer || !customer || !craftman || offer.dataset.relationshipFilterInitialized === 'true') return;

        offer.dataset.relationshipFilterInitialized = 'true';

        const refreshCraftman = () => {
            const selectedOffer = offer.selectedOptions[0];
            const craftmanId = selectedOffer?.dataset.craftmanId || '';
            const availableCount = filterOptions(craftman, option => craftmanId !== '' && option.value === craftmanId);

            craftman.value = availableCount === 1 ? craftmanId : '';
            craftman.disabled = craftmanId === '' || availableCount === 0;
            setPlaceholder(craftman, craftmanId === '' ? 'اختر العرض أولاً' : (availableCount === 0 ? 'الحرفي المرتبط غير متاح' : 'الحرفي المرتبط بالعرض'));
        };

        const refreshOrderRelations = () => {
            const selectedOrder = order.selectedOptions[0];
            const orderId = order.value;
            const customerId = selectedOrder?.dataset.customerId || '';
            const selectedOfferId = offer.value;
            const offerCount = filterOptions(offer, option => orderId !== '' && option.dataset.orderId === orderId);
            const selectedOfferIsAvailable = optionsAfterPlaceholder(offer).some(option => option.value === selectedOfferId && !option.disabled);

            if (!selectedOfferIsAvailable) offer.value = '';
            offer.disabled = orderId === '' || offerCount === 0;
            setPlaceholder(offer, orderId === '' ? 'اختر الطلب أولاً' : (offerCount === 0 ? 'لا توجد عروض مرتبطة بهذا الطلب' : 'اختر العرض'));

            const customerCount = filterOptions(customer, option => customerId !== '' && option.value === customerId);
            customer.value = customerCount === 1 ? customerId : '';
            customer.disabled = customerId === '' || customerCount === 0;
            setPlaceholder(customer, orderId === '' ? 'اختر الطلب أولاً' : (customerCount === 0 ? 'العميل المرتبط غير متاح' : 'عميل الطلب'));

            refreshCraftman();
        };

        order.addEventListener('change', refreshOrderRelations);
        offer.addEventListener('change', refreshCraftman);
        refreshOrderRelations();
    };

    window.CmsMarketplaceRelations = Object.freeze({ initOffer, initServiceJob });
})();
