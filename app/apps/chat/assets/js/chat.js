$(function () {

    /**
     * AJAX подгрузка сообщений
     */
    /*$('#messages').infinitescroll({
     navSelector  : "ul.pagination",
     nextSelector : "ul.pagination a:first",
     itemSelector : "#messages table.message",
     debug        : false,
     loadingImg   : "/img/loading.gif",
     loadingText  : '',
     donetext     : '',
     bufferPx     : 1700,
     localMode    : true,
     loading: {
     finishedMsg: '',
     msgText: ''
     }
     });*/


    var serverStatus = true;

    if ($('#dialog').length) {

        var $targetMessages = $('#dialogs');
        var $targetMessagesGet0 = $targetMessages.get(0);
        $targetMessagesGet0.scrollTop = $targetMessagesGet0.scrollHeight;

        try {
            /**
             * Соединение с сервером node.js
             */
            var socket = io.connect('https://cdaem.ru:8443');
            socket.on('connect', function () {
                console.log('connect');
                socket.emit('add user', readCookie("PHPSESSID"), $('#dialogs').data('interlocutor_id'));
            });

            socket.on('message', function (response) {
                if (response.status == 1) {
                    var tmp = '<table class="message">' +
                        '<tr>' +
                        '<td class="user">' +
                        '<div class="avatar">' + getUserAvatar(response.user.avatar) + '</div>' +
                        '<div class="name">' + getUserName(response.user) + '</div>' +
                        '<div class="date">' + getDateFormat(response.message.date) + '</div>' +
                        '</td>' +
                        '<td class="answer">' +
                        '<div class="text">' + getMessage(response.message.text) + '</div>' +
                        '<span class="delete" data-message_id="' + response.message.message_id + '">Удалить</span>' +
                        '</td>' +
                        '</tr>' +
                        '</table>';

                    var $targetMessages = $('#dialogs');
                    $targetMessages.append(tmp);

                    // Опустить скрол вниз к последнему сообщению
                    var $targetMessagesGet0 = $targetMessages.get(0);
                    $targetMessagesGet0.scrollTop = $targetMessagesGet0.scrollHeight;

                    var $targetMessage = $('#message-text');
                    $targetMessage.val('');
                    $targetMessage.focus();

                } else if (response.status == -1) {

                    console.log(response.message);

                } else {
                    showStackError('Ошибка', response.message);

                    if (typeof response.reload != 'undefined' && response.reload == false) {
                        // ..
                    } else {
                        setTimeout(function () {
                            document.location.reload();
                        }, 1000);
                    }
                }

            });
            /**
             * Отправить сообщение по кнопке
             */
            $("#message-send").on('click', function (event) {
                event.preventDefault();
                messageSend();
            });
            /**
             * Отправить сообщение комбинацией ctrl + enter
             */
            $('#message-text').keydown(function (e) {
                if (e.ctrlKey && e.keyCode == 13) {
                    messageSend();
                }
            });

            /**
             * Отправить сообщение
             */
            function messageSend() {

                var $targetMessage = $('#message-text');
                var $message = $targetMessage.val();

                if ($message.length > 0) {
                    // Отправить сообщение на событие
                    if (serverStatus) {
                        socket.emit('private message', $message);
                    } else {
                        showStackError('Ошибка', 'Сервер сообщений недоступен');
                        reloadStatus = true;
                    }
                }
            }

        } catch (err) {
            serverStatus = false;
            showStackError('Ошибка', 'Нет соединения с сервером сообщений');
        }
    }


    /**
     * Удалить все сообщения
     */
    $(document).on('click', "#delete-all", function (event) {
        var $this = $(this);

        $.getJSON("/ajax/delete-conversation/" + $this.data('interlocutor_id'), function (response) {
            if ($.isPlainObject(response) && 'status' in response) {
                if (response.status == 1) {
                    $(document).find('table.message').remove();
                    showStackInfo('Информация', response.message);
                } else {
                    showStackError('Ошибка', response.message);
                }
            }
        });

    });


    /**
     * Удалить одно сообщение
     */
    $(document).on('click', "#dialogs .message .delete", function (event) {
        var $this = $(this);
        $.getJSON("/ajax/delete-message/" + $this.data('message_id'), function (response) {
            if ($.isPlainObject(response) && 'status' in response) {
                if (response.status == 1) {
                    $this.parents('table')[0].remove();
                    showStackInfo('Информация', response.message);
                } else {
                    showStackError('Ошибка', response.message);
                }
            }
        });
    });
});


/**
 * Имя
 * @param user
 * @returns {string}
 */
function getUserName(user) {
    if (user.name && user.surname) {
        return user.name + ' ' + user.surname;
    }
    return 'Без имени';
}


/**
 * Аватар
 * @param avatar
 * @returns {*}
 */
function getUserAvatar(avatar) {
    if (avatar) {
        avatar = '/avatars/' + avatar;
    } else {
        avatar = '/basic-images/no-avatar.png';
    }
    return '<img src="' + avatar + '" alt="">';
}


/**
 * Дата
 * @param date
 * @returns {*}
 */
function getDateFormat(date) {
    return '<time datetime="' + date + '">только что</time>';
}


/**
 * Сообщение
 * @param message
 * @returns {*}
 */
function getMessage(message) {
    return nl2br(message);
}


/**
 * Заменить переносы строк на <br/>
 * @param str
 * @returns {*}
 */
function nl2br(str) {
    return str.replace(/([^>])\n/g, '$1<br/>');
}


/**
 * Чтение кук
 * @param name
 * @returns {*}
 */
function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}
