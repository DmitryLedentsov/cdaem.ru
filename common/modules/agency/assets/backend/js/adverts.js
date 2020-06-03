$(function () {

    /**
     * Выбор типов аренты
     */
    $("#adverts-multi-update-rent-types-list input").on('click', function (event) {

        var $this = $(this);
        var list = $('#adverts-multi-update-apartments-list');

        var apartmentsBlock = list.find('.apartments');
        var objects = list.find('[data-rent_type_id="' + $this.val() + '"]');


        if (apartmentsBlock.find('.rent-type').length) {
            apartmentsBlock.show();
        } else {
            apartmentsBlock.hide();
        }

        if (!$this.prop("checked")) {
            objects.hide();
        } else {
            objects.show();
        }

        apartmentsBlock.each(function (index) {
            var $hiddenLenght = $(this).find('.rent-type').filter(':hidden').length;
            var $showLength = $(this).find('.rent-type').length;

            if ($hiddenLenght >= $showLength) {
                $(apartmentsBlock[index]).hide();
            } else {
                $(apartmentsBlock[index]).show();
            }
        });
    });


    /**
     * Размернуть объявление в типе аренды
     */
    $("#adverts-multi-update-apartments-list .rent-type h4").on('click', function (event) {
        var $this = $(this).parent();
        $this.find('.rent-type-form').toggle();
    });


    /**
     * Дублирование изменений элементов формы первого блока ко всем остальным
     */
    $("#adverts-multi-update-apartments-list input, #adverts-multi-update-apartments-list select, #adverts-multi-update-apartments-list textarea").on('change keyup', function (event) {

        var $this = $(this);
        var $first = $('#adverts-multi-update-apartments-list').find('.apartments').filter(':first').find($this);

        // Если изменения были сделаны в 1 блоке,
        // необходимо применить их ко всем
        if ($first.length) {
            var rentTypeId = $this.parents('.rent-type').data('rent_type_id');

            var name = $this.attr('name').split("[");
            name = '[' + name[name.length - 1];

            // Везде указываем текущее значение
            $(".rent-type[data-rent_type_id=" + rentTypeId + "] [name$='" + name + "']").val($this.val());
        }
    });

});


var currentRedactorId = null;

/**
 * Дублирование изменений содержимого визуального редактора первого блока ко всем остальным
 * @param imperavi
 */
function imperaviChangeCallback(imperavi)
{
    var elementId = imperavi.$element.attr('id');

    var idArray = elementId.split('-');
    idArray.pop();
    var elementIdIngoing = idArray.join('-');


    if (currentRedactorId != null && currentRedactorId != elementIdIngoing) {
        return;
    }

    currentRedactorId = elementIdIngoing;

    var currentRedactorName = imperavi.$element.attr('name');
    var currentRedactorText = imperavi.code.get();


    var $first = $('#adverts-multi-update-apartments-list').find('.apartments').filter(':first').find('[data-redactor="1"]');
    var $firstRedactorIds = [];


    $first.each(function (index) {
        $firstRedactorIds.push($(this).attr('id'));
    });

    var rentTypeId = $('#' + elementId).parents('.rent-type').data('rent_type_id');

    var name = currentRedactorName.split("[");
    name = '[' + name[name.length - 1];


    if (jQuery.inArray(elementId, $firstRedactorIds) >= 0) {

        // Везде указываем текущее значение
        var $redactors = $(".rent-type[data-rent_type_id=" + rentTypeId + "] [name$='" + name + "']");

         $redactors.each(function( index ) {
         var $this = $(this);
             if ($this.attr('id') != elementId) {
                $('#' + $this.attr('id')).redactor('code.set', currentRedactorText);
             }
         });
    }
}
