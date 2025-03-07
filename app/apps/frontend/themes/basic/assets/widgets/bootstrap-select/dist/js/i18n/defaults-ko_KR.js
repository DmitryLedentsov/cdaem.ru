/*!
 * Bootstrap-select v1.7.1 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
(function ($) {
    $.fn.selectpicker.defaults = {
        noneSelectedText: '항목을 선택해주세요',
        noneResultsText: '{0} 검색 결과가 없습니다',
        countSelectedText: function (numSelected, numTotal) {
            return "{0}개를 선택하였습니다";
        },
        maxOptionsText: function (numAll, numGroup) {
            return [
                '{n}개까지 선택 가능합니다',
                '해당 그룹은 {n}개까지 선택 가능합니다'
            ];
        },
        selectAllText: '전체선택',
        deselectAllText: '전체해제',
        multipleSeparator: ', '
    };
})(jQuery);
