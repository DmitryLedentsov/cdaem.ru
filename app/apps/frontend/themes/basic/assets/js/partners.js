function writeMessage(a) {
    if (!$preloadFlag) {
        $preloadFlag = !0;
        var b = $.get("/ajax/message/" + a, function (a) {
            $("#modal-message").remove(), $("body").append(a), $("#modal-message").modal("show")
        });
        b.complete(function () {
            $preloadFlag = !1
        })
    }
}

jQuery(function () {
    $("#advertform-rent_type").find("input[type=checkbox]").on("click", function () {
        var a = $(this);
        if (a.prop("checked")) {
            var b = $("#form-apartment").data("formApi");
            b.reset("advertform-type-" + a.val()), b.reset("#advertform-price-" + a.val()), b.reset("#advertform-currency-" + a.val()), b.targetForm.find('[name ^= "AdvertForm[currency]"]').val(1).selectpicker("refresh"), $("#rent-type-" + a.val()).addClass("show")
        } else $("#rent-type-" + a.val()).removeClass("show")
    }), $("#form-reservation").formApi({
        fields: ["_csrf", "ReservationForm[city_id]", "ReservationForm[address]", "ReservationForm[pets]", "ReservationForm[children]", "ReservationForm[clients_count]", "ReservationForm[arrived_date]", "ReservationForm[arrived_time]", "ReservationForm[out_date]", "ReservationForm[out_time]", "ReservationForm[actuality_duration]", "ReservationForm[rent_type]", "ReservationForm[rooms]", "ReservationForm[floor]", "ReservationForm[beds]", "ReservationForm[metro_walk]", "ReservationForm[more_info]", "ReservationForm[verifyCode]", "ReservationForm[money_from]", "ReservationForm[money_to]", "ReservationForm[currency]", "ReservationForm[email]", "ReservationForm[phone]", "ReservationForm[password]", "ReservationForm[agreement]"],
        extraSubmitFields: {submit: "submit"},
        validateFields: ["reservationform-city_id", "reservationform-address", "reservationform-pets", "reservationform-children", "reservationform-clients_count", "reservationform-arrived_date", "reservationform-arrived_time", "reservationform-out_date", "reservationform-out_time", "reservationform-actuality_duration", "reservationform-rent_type", "reservationform-rooms", "reservationform-floor", "reservationform-beds", "reservationform-metro_walk", "reservationform-more_info", "reservationform-verifycode", "reservationform-budget", "reservationform-email", "reservationform-phone", "reservationform-password", "reservationform-agreement"],
        success: function (a, b) {
            $.isPlainObject(b) && "status" in b == 0 && scrollToFirstError(b, a.validateFields)
        },
        complete: function (a, b, c) {
            302 != b.status && $("#reservationform-verifycode-image").length && $("#reservationform-verifycode-image").yiiCaptcha("refresh")
        }
    }), $("#form-apartment").formApi({
        fields: ["_csrf", "AdvertForm[rent_type][]", "AdvertForm[price]", "AdvertForm[currency]", "ApartmentForm[city]", "ApartmentForm[visible]", "ApartmentForm[city_id]", "ApartmentForm[address]", "ApartmentForm[metro_walk]", "ApartmentForm[apartment]", "ApartmentForm[floor]", "ApartmentForm[total_area]", "ApartmentForm[total_rooms]", "ApartmentForm[beds]", "ApartmentForm[remont]", "ApartmentForm[description]", "ApartmentForm[metro_array][]", "ImageForm[files][]"],
        extraSubmitFields: {submit: "submit"},
        validateFields: ["advertform-rent_type", "advertform-type-*", "apartmentform-city_id", "apartmentform-address", "apartmentform-visible", "apartmentform-metro_walk", "apartmentform-apartment", "apartmentform-floor", "apartmentform-total_area", "apartmentform-total_rooms", "apartmentform-beds", "apartmentform-remont", "apartmentform-description", "apartmentform-metro_array", "imageform-files"],
        success: function (a, b) {
            $.isPlainObject(b) && "status" in b && (1 == b.status ? showStackInfo("Информация", b.message) : showStackError("Ошибка", b.message)), $.isPlainObject(b) && "status" in b == 0 && scrollToFirstError(b, a.validateFields)
        }
    }), $("#imageform-files").bind("change", function (a) {
        if (window.FileReader) {
            var b = $("#form-apartment").data("formApi"), c = document.getElementById("imageform-files").files;
            if (c.length > 10 || $("#images-preview .advert-preview").length + c.length > 10) showStackError("Внимание", "Вы можете загрузить до 10 изображений Вашего объекта. Остальные изображения будут проигнорированы."); else for (var d = 0, e = c.length; e > d; d++) if (!(d >= 10)) {
                var f = {};
                f["ImageForm[files][" + d + "]"] = c[d], b.addFile(f), function (a) {
                    var b = new FileReader;
                    b.onloadend = function (c) {
                        var d = '<div class="advert-preview shadow-box" data-image="ImageForm[files][' + a + ']" style="margin-right: 15px; margin-bottom: 15px;"><div class="control"><div class="delete"></div></div><div class="apartment-wrap"><div class="image"><img src="' + b.result + '" alt="" /></div></div></div>';
                        $("#images-preview").append(d)
                    }, b.readAsDataURL(c[a])
                }(d)
            }
        }
    }), $(document).on("click", ".images-preview", function (a) {
        a.preventDefault();
        var b = $(a.target), c = b.parents(".advert-preview");
        if (b.hasClass("index")) $.getJSON("/office/control-image/index/" + c.data("image"), function (a) {
            if (1 == a.status) {
                c.find(".control .index").remove(), c.addClass("default");
                var b = $(".images-preview .advert-preview").not(c);
                b.removeClass("default"), b.find(".index").remove(), b.find(".control").prepend('<div class="index" title="Сделать главным"></div>'), showStackInfo("Информация", "Данные сохранены успешно")
            } else showStackError("Ошибка", "Возникла критическая ошибка")
        }); else if (b.hasClass("delete")) if (c.hasClass("loaded")) $.getJSON("/office/control-image/delete/" + c.data("image"), function (a) {
            1 == a.status ? (c.remove(), showStackInfo("Информация", "Данные сохранены успешно")) : showStackError("Ошибка", "Возникла критическая ошибка")
        }); else {
            var d = $("#form-apartment").data("formApi");
            d.removeFile(c.data("image")), c.remove()
        }
    }), $(document).on("click", ".reservation-send", function (a) {
        a.preventDefault();
        var b = $(this);
        $.ajax({
            url: "/partners/ajax/reservation-confirm",
            method: "POST",
            data: {
                action: "confirm",
                type: b.data("type"),
                reservation_id: b.data("reservation"),
                user_id: b.data("user-id"),
                department: b.data("department"),
                user_type: b.data("user-type")
            },
            context: document.body
        }).done(function (a) {
            $("#modal-reservation-confirm").remove(), $("body").append(a), $("#modal-reservation-confirm").modal("show")
        })
    }), $(document).on("click", "#button-reservation-confirm", function (a) {
        a.preventDefault();
        var b = $(this);
        $.ajax({
            url: "/partners/ajax/reservation-confirm",
            method: "POST",
            data: {
                action: "send",
                type: b.data("type"),
                reservation_id: b.data("reservation"),
                cancel_reason: $("#reservation-confirm-reason").val(),
                department: b.data("department"),
                user_type: b.data("user-type"),
                priced: b.data("priced"),
                user_id: b.data("user-id")
            },
            context: document.body
        }).done(function (a) {
            var b = $("#reservation-confirm-reason").parents(".form-group");
            b.removeClass("has-success"), b.removeClass("has-error"), b.find(".help-block").html(""), $.isPlainObject(a) && "status" in a ? 1 == a.status ? ($("#modal-reservation-confirm").find(".modal-body").html('<div class="alert alert-info">' + a.message + "</div>"), setTimeout(function () {
                document.location.reload()
            }, FA0)) : showStackError("Ошибка", a.message) : $.isArray(a) && a[0] && (b.addClass("has-error"), b.find(".help-block").html(a[0]))
        })
    }), $(document).on("click", "#button-reservation-failure", function (a) {
        a.preventDefault();
        var b = $(this);
        $.ajax({
            url: "/partners/ajax/reservation-failure/" + b.data("id"),
            method: "GET",
            context: document.body
        }).done(function (a) {
            $("#modal-reservation-failure").remove(), $("body").append(a), $("#modal-reservation-failure").modal("show")
        })
    }), $(document).on("click", "#button-reservation-failure-confirm", function (a) {
        a.preventDefault();
        var b = $(this);
        $.ajax({
            url: "/partners/ajax/reservation-failure/" + b.data("id"),
            method: "POST",
            data: {cause_text: $("#reservation-failure-reason").val()},
            context: document.body
        }).done(function (a) {
            var b = $("#reservation-failure-reason").parents(".form-group");
            b.removeClass("has-success"), b.removeClass("has-error"), b.find(".help-block").html(""), $.isPlainObject(a) && "status" in a ? 1 == a.status ? $("#modal-reservation-failure").find(".modal-body").html('<div class="alert alert-info">' + a.message + "</div>") : showStackError("Ошибка", a.message) : $.isArray(a) && a[0] && (b.addClass("has-error"), b.find(".help-block").html(a[0]))
        })
    })
});
var $preloadFlag = !1;

$(document).ready(function () {
    $("#country").on('change', '', function (e) {
        if ($("#country option:selected").text() == "Russia") {
            $("#phone").inputmask("+79999999999");
        }
        if ($("#country option:selected").text() == "Ukraine") {
            $("#phone").inputmask("+380999999999");
        }
        if ($("#country option:selected").text() == "Belarus") {
            $("#phone").inputmask("+375999999999");
        }
    });
});