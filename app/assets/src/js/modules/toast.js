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
        // "showDuration": "300000",
        "hideDuration": "1000",
        // "hideDuration": "10000000",
        "timeOut": "5000",
        // "timeOut": "500000000",
        // "timeOut": "30000000",
        "extendedTimeOut": "1000",
        // "extendedTimeOut": "100000000000000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    });
};
