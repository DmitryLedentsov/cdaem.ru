(function ($) {
    "use strict";

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
    });


})(jQuery);