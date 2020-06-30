jQuery(function () {
    $("#form-profile").formApi({
        fields: ["_csrf", "Profile[name]", "Profile[surname]", "Profile[second_name]", "Profile[about_me]", "Profile[user_type]", "Profile[phone2]", "Profile[email]", "Profile[skype]", "Profile[ok]", "Profile[vk]", "Profile[fb]", "Profile[twitter]", "Profile[legal_person]", "Profile[image]"],
        extraSubmitFields: {submit: "submit"},
        validateFields: ["profile-name", "profile-surname", "profile-second_name", "profile-about_me", "profile-user_type", "profile-phone2", "profile-email", "profile-skype", "profile-ok", "profile-vk", "profile-fb", "profile-twitter", "profile-legal_person", "profile-image"],
        success: function (a, b) {
            $.isPlainObject(b) && "status" in b && (1 == b.status ? showStackInfo("Информация", b.message) : showStackError("Ошибка", b.message))
        }
    }), $("#form-password").formApi({
        fields: ["_csrf", "PasswordForm[oldpassword]", "PasswordForm[password]", "PasswordForm[repassword]"],
        extraSubmitFields: {submit: "submit"},
        validateFields: ["passwordform-oldpassword", "passwordform-password", "passwordform-repassword"],
        success: function (a, b) {
            $.isPlainObject(b) && "status" in b && (1 == b.status ? showStackInfo("Информация", b.message) : showStackError("Ошибка", b.message))
        }
    }), $("#form-signup").formApi({
        fields: ["_csrf", "User[email]", "User[phone]", "User[password]", "User[repassword]", "User[agreement]", "Profile[user_type]", "Profile[name]", "Profile[surname]", "Profile[second_name]", "Profile[whau]", "Profile[advertising]"],
        extraSubmitFields: {submit: "submit"},
        validateFields: ["user-email", "user-phone", "user-password", "user-repassword", "user-agreement", "profile-user_type", "profile-name", "profile-surname", "profile-second_name", "profile-whau", "profile-advertising"],
        success: function (a, b) {
            $.isPlainObject(b) && "status" in b && (1 == b.status ? showStackInfo("Информация", b.message) : showStackError("Ошибка", b.message))
        }
    }), $("#form-signin").formApi({
        fields: ["_csrf", "LoginForm[username]", "LoginForm[password]", "LoginForm[rememberMe]"],
        extraSubmitFields: {submit: "submit"},
        validateFields: ["loginform-username", "loginform-password", "loginform-rememberme"],
        success: function (a, b) {
            $.isPlainObject(b) && "status" in b && (1 == b.status ? showStackInfo("Информация", b.message) : showStackError("Ошибка", b.message))
        }
    }), $("#form-resend").formApi({
        fields: ["_csrf", "ResendForm[email]"],
        extraSubmitFields: {submit: "submit"},
        validateFields: ["resendform-email"],
        success: function (a, b) {
            $.isPlainObject(b) && "status" in b && (1 == b.status ? showStackInfo("Информация", b.message) : showStackError("Ошибка", b.message))
        }
    }), $("#form-recovery").formApi({
        fields: ["_csrf", "RecoveryForm[email]"],
        extraSubmitFields: {submit: "submit"},
        validateFields: ["recoveryform-email"],
        success: function (a, b) {
            $.isPlainObject(b) && "status" in b && (1 == b.status ? showStackInfo("Информация", b.message) : showStackError("Ошибка", b.message))
        }
    }), $("#form-recovery-confirmation").formApi({
        fields: ["_csrf", "RecoveryConfirmationForm[password]", "RecoveryConfirmationForm[repassword]"],
        extraSubmitFields: {submit: "submit"},
        validateFields: ["recoveryconfirmationform-password", "recoveryconfirmationform-repassword"],
        success: function (a, b) {
            $.isPlainObject(b) && "status" in b && (1 == b.status ? showStackInfo("Информация", b.message) : showStackError("Ошибка", b.message))
        }
    }), $("#form-legal-person").formApi({
        fields: ["_csrf", "LegalPerson[name]", "LegalPerson[formattedRegisterDate]", "LegalPerson[INN]", "LegalPerson[PPC]", "LegalPerson[account]", "LegalPerson[bank]", "LegalPerson[KS]", "LegalPerson[BIK]", "LegalPerson[BIN]", "LegalPerson[legal_address]", "LegalPerson[physical_address]", "LegalPerson[director]", "LegalPerson[description]"],
        extraSubmitFields: {submit: "submit"},
        validateFields: ["legalperson-name", "legalperson-formattedRegisterDate", "legalperson-inn", "legalperson-ppc", "legalperson-account", "legalperson-bank", "legalperson-ks", "legalperson-bik", "legalperson-bin", "legalperson-legal_address", "legalperson-physical_address", "legalperson-director", "legalperson-description"],
        success: function (a, b) {
            $.isPlainObject(b) && "status" in b && (1 == b.status ? showStackInfo("Информация", b.message) : showStackError("Ошибка", b.message))
        }
    }), $("#profile-image").bind("change", function (a) {
        if (window.FileReader) {
            var b = $("#form-profile").data("formApi");
            b.clearFiles(), $("#image-preview").html("");
            var c = document.getElementById("profile-image").files, d = {};
            d["Profile[image]"] = c[0], b.addFile(d), function (a) {
                var b = new FileReader;
                b.onloadend = function (a) {
                    $("#image-preview").append("<div data-id='Profile[image]''><img src='" + b.result + "' alt='' /></div>")
                }, b.readAsDataURL(c[a])
            }(0)
        }
    }), $("#image-preview").bind("click", function (a) {
        a.preventDefault(), $("#profile-image").val("");
        var b = a.target, c = $("#form-profile").data("formApi");
        if ("IMG" == b.tagName) {
            var d = $(b);
            if ("default" == d.data("type")) return !1;
            c.removeFile(d.parent().data("id")), d.parent().remove()
        }
    })
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