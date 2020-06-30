jQuery(function () {
    var a = $("#gallery");
    a.length && a.owlCarousel({
        autoWidth: !0,
        loop: !1,
        margin: 10,
        nav: !1,
        autoplay: !0,
        autoplayTimeout: 3e3,
        smartSpeed: 1e3,
        autoplayHoverPause: !1,
        navText: ["", ""],
        responsive: {0: {items: 1}, 600: {items: 3}, 1e3: {items: 5}}
    }), $(".payment-methods .pm-icon").on("click", function (a) {
        var b = $(this);
        !b.parent().hasClass("no-active") && $(a.target).hasClass("pm-icon") && ($.each($(".payment-methods form"), function () {
            $(this).data("formApi").reset()
        }), $(".payment-methods .pm-icon").find(".payment-info").not(b.find(".payment-info")).hide(), b.find(".payment-info").toggle())
    }), $(".phone-mask").mask("+7 (999) 999-9999"), $(".payment-methods form").formApi({
        fields: ["_csrf", "DetailsHistoryForm[advert_id]", "DetailsHistoryForm[type]", "DetailsHistoryForm[payment]", "DetailsHistoryForm[phone]", "DetailsHistoryForm[email]"],
        extraSubmitFields: {submit: "submit"},
        validateFields: ["detailshistoryform-type", "detailshistoryform-payment", "detailshistoryform-phone", "detailshistoryform-email"],
        success: function (a, b) {
            $.isPlainObject(b) && "status" in b && (1 == b.status ? a.targetForm.parent().html('<div class="alert alert-success">' + b.message + "</div>") : showStackError("Ошибка", b.message))
        }
    }), $("#form-reserved").formApi({
        fields: ["_csrf", "Reservation[name]", "Reservation[email]", "Reservation[phone]", "Reservation[arrived_date]", "Reservation[arrived_time]", "Reservation[out_date]", "Reservation[out_time]", "Reservation[transfer]", "Reservation[clients_count]", "Reservation[more_info]", "Reservation[whau]", "Reservation[verifyCode]"],
        extraSubmitFields: {submit: "submit"},
        validateFields: ["reservation-name", "reservation-email", "reservation-phone", "reservation-arrived_date", "reservation-arrived_time", "reservation-out_date", "reservation-out_time", "reservation-transfer", "reservation-clients_count", "reservation-more_info", "reservation-whau", "reservation-verifycode"],
        success: function (a, b) {
            $.isPlainObject(b) && "status" in b && (1 == b.status ? $("#reserved-result").html('<div class="alert alert-success">' + b.message + "</div>") : $("#reserved-result").html('<div class="alert alert-danger">' + b.message + '</div><div><a href="javascript://" onclick="document.location.reload(); return false;">Попробовать еще раз.</a></div>'))
        },
        complete: function (a, b, c) {
            308 != b.status && 302 != b.status && $("#reservation-verifycode-image").length && $("#reservation-verifycode-image").yiiCaptcha("refresh")
        }
    }), $("#form-want-pass").formApi({
        fields: ["_csrf", "WantPassForm[rent_types_array][]", "WantPassForm[metro_array][]", "WantPassForm[address]", "WantPassForm[name]", "WantPassForm[phone]", "WantPassForm[phone2]", "WantPassForm[email]", "WantPassForm[rooms]", "WantPassForm[description]", "WantPassForm[files][]", "WantPassForm[verifyCode]"],
        extraSubmitFields: {submit: "submit"},
        validateFields: ["wantpassform-rent_types_array", "wantpassform-metro_array", "wantpassform-address", "wantpassform-name", "wantpassform-phone", "wantpassform-phone2", "wantpassform-email", "wantpassform-rooms", "wantpassform-description", "wantpassform-files", "wantpassform-verifycode"],
        success: function (a, b) {
            $.isPlainObject(b) && "status" in b && (1 == b.status ? $("#want-pass-result").html('<div class="alert alert-success">' + b.message + "</div>") : $("#want-pass-result").html('<div class="alert alert-danger">' + b.message + '</div><div><a href="javascript://" onclick="document.location.reload(); return false;">Попробовать еще раз.</a></div>'))
        },
        complete: function (a, b, c) {
            302 != b.status && $("#wantpassform-verifycode-image").length && $("#wantpassform-verifycode-image").yiiCaptcha("refresh")
        }
    }), $("#wantpassform-files").bind("change", function (a) {
        if (window.FileReader) {
            var b = $("#form-want-pass").data("formApi"), c = document.getElementById("wantpassform-files").files;
            if (c.length > 10 || $("#images-preview img").length + c.length > 10) showStackError("Внимание", "Вы можете загрузить до 10 изображений Вашего объекта. Остальные изображения будут проигнорированы."); else for (var d = 0, e = c.length; e > d; d++) if (!(d >= 10)) {
                var f = {};
                f["WantPassForm[files][" + d + "]"] = c[d], b.addFile(f), function (a) {
                    var b = new FileReader;
                    b.onloadend = function (c) {
                        $("#images-preview").append("<div style='width:150px; display: inline-block; margin-right: 5px;' data-id='WantPassForm[files][" + a + "]''><img style='width: 100%' src='" + b.result + "' alt='' /></div>")
                    }, b.readAsDataURL(c[a])
                }(d)
            }
        }
    }), $("#images-preview").bind("click", function (a) {
        a.preventDefault();
        var b = a.target, c = $("#form-want-pass").data("formApi");
        if ("IMG" == b.tagName) {
            var d = $(b);
            c.removeFile(d.parent().data("id")), d.parent().remove()
        }
    })
});


$('#form-select').formApi({

    // Все поля
    fields: [
        "_csrf",
        "SelectForm[rent_types_array][]",
        "SelectForm[metro_array][]",
        "SelectForm[name]",
        "SelectForm[phone]",
        "SelectForm[phone2]",
        "SelectForm[email]",
        "SelectForm[rooms]",
        "SelectForm[description]",
        "SelectForm[verifyCode]"
    ],

    // Дополнительные поля, будут отправлены по кнопке submit
    extraSubmitFields: {
        submit: "submit"
    },

    // Валидация полей
    validateFields: [
        "selectform-rent_types_array",
        "selectform-metro_array",
        "selectform-name",
        "selectform-phone",
        "selectform-phone2",
        "selectform-email",
        "selectform-rooms",
        "selectform-description",
        "selectform-verifycode"
    ],


    // Событие срабатывает при успешном запросе
    success: function (formApi, response) {

        if ($.isPlainObject(response) && 'status' in response) {
            if (response.status == 1) {
                $('#select-result').html('<div class="alert alert-success">' + response.message + '</div>');
            } else {
                $('#select-result').html('<div class="alert alert-danger">' + response.message + '</div><div><a href="javascript://" onclick="document.location.reload(); return false;">Попробовать еще раз.</a></div>');
            }

        }

    },

    // Событие срабатывает после завершения ajax запроса
    complete: function (formApi, jqXHR, textStatus) {
        if (jqXHR.status != 302) {
            // Обновить защитный код
            if ($('#selectform-verifycode-image').length) {
                $('#selectform-verifycode-image').yiiCaptcha('refresh');
            }
            $("html, body").animate({scrollTop: $('div.alert').height()}, 1000);
        }

    }
});
    
    
   

