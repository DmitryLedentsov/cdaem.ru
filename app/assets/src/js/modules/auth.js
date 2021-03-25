(function ($) {
    "use strict";




    $(document).on('submit', '#form-signIn', function (e) {
        e.preventDefault();
        let $form = $(this);

        window.ajaxRequest($form, function () {


           /* return {
                beforeSend: function () {},
                complete: function () {},
                success: function (response) {


                }
            };*/
        });


    });



/*
    $('#form-signIn').displayValidation({
        'email': [],
        'password': ['Неверный пароль'],
    });


    $('#form-forgotPassword').displayValidation({
        'email': ['Пользователь с таким Email не найден'],
    });


    $('#form-signUp').displayValidation({
        'email': ['Пользователь с таким Email уже зарегистрирован'],
        'phone': ['Пользователь с таким телефоном уже зарегистрирован'],
        'password': ['Вы ввели слишком слабый пароль'],
        'name': ['Введите ваше имя'],
        'user_type': ['Указан некорректный тип аренды'],
    });

    $(document).on('submit', '#form-signUp', function (e) {
        e.preventDefault();

        $('#form-signUp').displayValidation({
            'email': ['Пользователь с таким Email уже зарегистрирован'],
            'phone': ['Пользователь с таким телефоном уже зарегистрирован'],
            'password': ['Вы ввели слишком слабый пароль'],
            'name': ['Введите ваше имя'],
            'user_type': ['Указан некорректный тип аренды'],
        });
    });*/


})(jQuery);