$(function() {

    /**
     * Раскрыть форму
     */
    $('#special-adverts-multi-create-apartments-list input:checkbox').on('click', function (event) {
        var $this = $(this);
        checkedAction($this);
    });

    /**
     * Раскрыть форму с активным чекбоксом
     */
    $('#special-adverts-multi-create-apartments-list input:checkbox').each(function( index ) {
        var $this = $( this );
        checkedAction($this);
    });

});


function checkedAction($this)
{
    var $targetAdvert = $this.parents('.rent-type');

    if ($this.prop('checked')) {
        $targetAdvert.find('.rent-type-form').show();
    } else {
        $targetAdvert.find('.rent-type-form').hide();
    }
}