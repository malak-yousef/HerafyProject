(function () {
    'use strict';

    window.initCmsOrderLocation = function (citySelector, areaSelector) {
        const city = document.querySelector(citySelector);
        const area = document.querySelector(areaSelector);
        if (!city || !area) return;

        const filterAreas = function () {
            const cityId = city.value;
            const selectedArea = area.value;
            let selectedStillValid = false;

            Array.from(area.options).forEach(function (option, index) {
                if (index === 0) {
                    option.hidden = false;
                    option.disabled = false;
                    return;
                }
                const matches = cityId !== '' && option.dataset.city === cityId;
                option.hidden = !matches;
                option.disabled = !matches;
                if (matches && option.value === selectedArea) selectedStillValid = true;
            });

            area.disabled = cityId === '';
            if (!selectedStillValid) area.value = '';
        };

        city.addEventListener('change', filterAreas);
        filterAreas();
    };
})();
