function openWindowByService(a) {
    $preloadFlag || ($preloadFlag = !0, $("#services .service").not(a).removeClass("active"), a.toggleClass("active"), $serviceData.service = a.data("type"), $.get("/partners/ajax/realty-objects-by-service", {service: $serviceData.service}, function (a) {
        $("#modal-realty-objects-by-service").remove(), $("body").append(a), $("#modal-realty-objects-by-service").find(".advert-preview").removeClass("selected"), $("#modal-realty-objects-by-service").modal("show"), $targetSelectedAdvertModalTitle = $("#modal-realty-objects-by-service").data("title")
    }).complete(function () {
        $preloadFlag = !1
    }))
}

var $preloadFlag = !1, $targetSelectedAdvertModalTitle, $serviceData = {}, $responseCache = null;
$(function () {
    $(document).on("click", ".payments-method-logo", function (a) {
        a.preventDefault();
        var b = $(this);
        $(".payments-method-logo").removeClass("active"), b.addClass("active"), $(".payment-service select[name='Pay[system]'] option[value='" + b.data("system") + "']").prop("selected", !0), b.parents(".payment-service").find('input[name="Pay[system]"]').val(b.data("system"))
    }), $(".payment-service select[name='Pay[system]']").change(function () {
        var a = $(this);
        $(".payments-method-logo").removeClass("active"), $('.payments-method-logo[data-system="' + a.val() + '"]').addClass("active")
    }), $(".settings-nav .settings").on("click", function (a) {
        var b = $(this);
        $preloadFlag || ($preloadFlag = !0, $.post("/office/ajax/social", {
            type: b.data("type"),
            interlocutor: b.data("interlocutor")
        }, function (a) {
            $.isPlainObject(a) && "status" in a && (1 == a.status ? (showStackInfo("Информация", a.message), window.location.reload()) : showStackError("Ошибка", a.message))
        }).complete(function () {
            $preloadFlag = !1
        }))
    }), $(".delete-top-slider").on("click", function (a) {
        if (!confirm("Подтверждение")) return !1;
        var b = $(this);
        $preloadFlag || ($preloadFlag = !0, $.post("/office/ajax/delete-top-slider/" + b.data("advertisement_id"), function (a) {
            $.isPlainObject(a) && "status" in a && (1 == a.status ? (showStackInfo("Информация", a.message), window.location.reload()) : showStackError("Ошибка", a.message))
        }).complete(function () {
            $preloadFlag = !1
        }))
    }), $("#modal-realty-objects-by-service").on("hidden.bs.modal", function () {
        $("#services .service").removeClass("active")
    }), $("#services .service[data-type]").on("click, mouseup", function (a) {
        var b = $(this);
        return $("#services").hasClass("min") ? (location.href = "/office/services#" + b.data("type"), !1) : (openWindowByService(b), !1)
    });
    var a = window.location.hash.replace("#", "");
    if ("" != a) {
        var b = $('#services .service[data-type="' + a + '"]');
        b.length && openWindowByService(b)
    }
    $(document).on("click", "#modal-realty-objects-by-service .advert-preview", function (a) {
        a.preventDefault();
        var b = $(this);
        b.toggleClass("selected"), $serviceData.selected = [], $("#modal-realty-objects-by-service").find(".advert-preview.selected").each(function (a) {
            $(this).data("advert") ? $serviceData.selected.push($(this).data("advert")) : $(this).data("apartment") && $serviceData.selected.push($(this).data("apartment"))
        }), $("#selected-advert-count").html($serviceData.selected.length);
        var c = $("#selected-advert-info");
        $serviceData.selected.length > 0 ? c.length || $("#modal-realty-objects-by-service").find(".load").append('<div class="text-center"><span class="btn btn-primary btn-special" id="selected-advert-info">Рассчитать стоимость</span></div>') : c.length && c.parent().remove(), $responseCache = $("#modal-realty-objects-by-service").find(".load").html()
    }), $(document).on("click", "#selected-advert-info", function (a) {
        a.preventDefault();
        var b = ($(this), $("#modal-realty-objects-by-service"));
        $serviceData.days = $("#realty-objects-by-service-days").val(), $serviceData.date = $("#realty-objects-by-service-date").val(), $serviceData.request = "calc", $preloadFlag || ($preloadFlag = !0, $.post("/partners/ajax/buy-service", $serviceData, function (a) {
            b.find(".load").html(a), b.find(".modal-title").html("Калькулятор"), b.find(".alert-danger").length || b.find(".load").append('<div class="text-center"><span class="btn btn-primary btn-special" id="selected-advert-ago">Назад</span> &nbsp; <span class="btn btn-orange btn-special" id="selected-advert-pay">Оплатить</span></div>')
        }).complete(function () {
            $preloadFlag = !1
        }))
    }), $(document).on("click", "#selected-advert-ago", function (a) {
        a.preventDefault();
        var b = ($(this), $("#modal-realty-objects-by-service"));
        b.find(".modal-title").html($targetSelectedAdvertModalTitle), b.find(".load").html($responseCache), refreshScripts(), $("#realty-objects-by-service-days").val($serviceData.days), $("#realty-objects-by-service-date").val($serviceData.date)
    }), $(document).on("click", "#selected-advert-pay", function (a) {
        a.preventDefault();
        var b = ($(this), $("#modal-realty-objects-by-service"));
        $serviceData.request = "payment", $preloadFlag || ($preloadFlag = !0, $.post("/partners/ajax/buy-service", $serviceData, function (a) {
            b.find(".load").html(a), b.find(".modal-title").html("Оплата")
        }).complete(function () {
            $preloadFlag = !1
        }))
    }), $(document).on("change", "#realty-objects-by-service-rent-type-list", function () {
        var a = $(this), b = a.parents(".office");
        if (b.find(".alert-warning").remove(), a.val()) {
            b.find(".item").hide();
            var c = b.find('[data-rent-type="' + a.val() + '"]').parents(".item");
            c.show(), c.length || b.find(".apartment-list").prepend('<div class="alert alert-warning">В данном типе аренды нет объектов.</div>')
        } else b.find(".item").show()
    })
});

$(window).scroll(function () {
    if ($(this).scrollTop() > 62) {
        $('#navigatio').addClass('fixed');
    } else if ($(this).scrollTop() < 62) {
        $('#navigatio').removeClass('fixed');
    }
});

$("#opendescription2").click(function () {
    $("#information-advert2").slideToggle("slow", function () {

    });
});

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