(function ($) {
    "use strict";

    /*=========================================================================
        Filter nav
    =========================================================================*/
    const btnOpenFilter = document.querySelector('.search-form .search-form-filter-extra');
    const btnCloseFilter = document.querySelector('.search-form .btn-close');
    const filter = document.querySelector('.search-form .search-form-filter-options');

    if (!!btnOpenFilter && !!btnCloseFilter) {
        document.addEventListener("click", (e) => {
            if (e.target === btnOpenFilter) {
                return btnOpenFilter.classList.toggle('is-open');
            }
            btnOpenFilter.classList.remove('is-open');
        });

        filter.addEventListener("click", (e) => e.stopPropagation());
        btnCloseFilter.addEventListener("click", () => btnOpenFilter.classList.remove('is-open'));
    }


})(jQuery);