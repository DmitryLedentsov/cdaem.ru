let currentNotify, currentNotifyDiv, previousNotifyText;

toastr.subscribe(item => {
    currentNotify = item;
});

// Удаляем повторяющиеся сообщения
const removeCopies = () => {
    if (currentNotify && currentNotify.map.message === previousNotifyText && currentNotifyDiv.length > 0) {
        toastr.remove();
    }
};

window.toastSuccess = function (message, title) {
    removeCopies();

    previousNotifyText = message;
    currentNotifyDiv = toastr.success(message, title || null, {
        "closeButton": false,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    });
};

window.toastError = function (message, title) {
    removeCopies();

    previousNotifyText = message;
    currentNotifyDiv = toastr.error(message, title || null, {
        "closeButton": false,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    });
};
