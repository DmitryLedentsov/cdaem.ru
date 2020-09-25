$(function () {
    $(document).on('beforeSubmit', '#modal-image-content #image_form', function (e) {
        var form = $(this);
        $.post(form.attr('action'), form.serialize()).done(function (result) {
            if (result.status == 1) {
                $(document).find('#modal-image-content').empty();
                $(document).find('#imageModal').modal('hide');
            }
        });
        return false;
    });
});