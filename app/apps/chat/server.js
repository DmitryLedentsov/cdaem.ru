var app = require('express')();
var fs = require('fs');

var server = require('https').createServer({
    key: fs.readFileSync('/etc/letsencrypt/live/cdaem.com-000/privkey.pem'),
    cert: fs.readFileSync('/etc/letsencrypt/live/cdaem.com-0002/cert.pem'),
    ca: fs.readFileSync('/etc/letsencrypt/live/cdaem.com-0002/chain.pem'),
    requestCert: false,
    rejectUnauthorized: false
}, app);
var io = require('socket.io')(server);
var request = require('request');
var mysql = require('mysql');
var util = require('util');
var uuid = require('node-uuid');
server.listen(8443, function () {
    console.log('listening on *:8443');
});

// Логирование
var log_file = fs.createWriteStream(__dirname + '/error.log', {flags: 'a+'});
var log_stdout = process.stdout;

eval(fs.readFileSync('functions/helper.js') + '');
eval(fs.readFileSync('functions/room.js') + '');
eval(fs.readFileSync('functions/find.js') + '');
eval(fs.readFileSync('functions/MySql.js') + '');


// Соединение с базой данных
var MysqlConfig = require('./mysql.json');
var MysqlDBconnection = mysql.createConnection(MysqlConfig);

MysqlDBconnection.connect(function (err) {
    if (err) {
        logError('Ошибка подключения к базе данных');
    }
});

/*MysqlDBconnection.end(function (err) {

 });*/

console.log('START SERVER');

/**
 * Общий глобальный объект
 * @type {{room: Array, rooms: Array}}
 */
var chat = {
    rooms: [] // Список всех комнат с пользователями
};


try {

    /**
     * Соединение по сокету
     */
    io.on('connection', function (socket) {

        console.log("connected");
        /**
         * Добавляем пользователя в комнату
         * @var hash - на основе которого необходимо проверить текущего пользователя
         * @var interlocutor_id - идентификатор собеседника
         */
        socket.on('add user', function (hash, interlocutor_id) {
            console.log('add user');

            verifyHash(hash, function (user) {
                if (user && typeof user === 'object') {

                    console.log('Пользователь и собеседник: ' + user.id + ' ' + interlocutor_id);
                    findWaitingRoomByUserIdAndInterlocutorId(user.id, interlocutor_id, function (roomHash) {

                        console.log('Комната: ' + roomHash);
                        roomUserPush({
                            user: user,
                            interlocutor_id: interlocutor_id,
                            roomHash: roomHash,
                            socket: socket
                        }, function (room) {
                            // Добавляем текущего пользователя в комнату
                            socket.join(room.name);
                            socket.room = room.name;
                            socket.user = user;
                            socket.interlocutor_id = interlocutor_id;
                            // Сообщение
                            io.emit('message', {
                                status: -1,
                                message: 'Пользователь id' + user.id + ' зашел в комнату ' + room.name + '. Собеседник: id' + interlocutor_id
                            });
                            console.log('Пользователь id' + user.id + ' зашел в комнату ' + room.name + '. Собеседник: id' + interlocutor_id);
                        });

                    });
                } else {
                    var error = 'Пользователь не найден. Данные [hash: ' + hash + ', interlocutor_id: ' + interlocutor_id + '].';
                    console.log(error);
                    logError(error);
                    io.emit('message', {
                        status: 0,
                        message: 'Возникла критическая ошибка, Вы не можете писать в чат. Пожалуйста обратитесь в службу технической поддержки.'
                    });
                }
            });
        });
        /**
         * Обрабатываем событие приватного сообщения
         */
        socket.on('private message', function (msg) {
            findRoomByHash(socket.room, function (room) {
                if (room) {
                    checkBlackList(socket.user.id, socket.interlocutor_id, function (result) {
                        if (result > 0) {
                            io.emit('message', {
                                status: 0,
                                message: 'Вы не можете писать данному пользователю, так как Вы в черном списке!'
                            });
                        } else {

                            //TODO: Если постоянно тыкают сообщения не разрешать их писать в течении н времени.

                            if (msg.length > 2000) {
                                io.emit('message', {
                                    status: 0,
                                    reload: false,
                                    message: 'Сообщение не должно быть длиннее 2000 символов!'
                                });
                            } else {
                                sendMessage(socket.user.id, socket.interlocutor_id, null, msg, function (result, message) {
                                    var response = {
                                        status: 1,
                                        user: socket.user,
                                        message: message
                                    };
                                    if (response.message.theme) {
                                        response.message.theme = htmlspecialchars(response.message.theme);
                                    }
                                    if (response.message.text) {
                                        response.message.text = htmlspecialchars(response.message.text);
                                    }
                                    // Отсылаем сообщение
                                    io.to(socket.room).emit('message', response);
                                    socket.broadcast.to(socket.room).emit({event: 'message', response: response});
                                });
                            }
                        }
                    });
                } else {
                    var error = 'Пользователь не найден в комнате. Данные [socketId: ' + socket.id + ']';
                    console.log(error);
                    logError(error);
                    io.to(chat.room.name).emit('message', {
                        status: 0,
                        message: 'Возникла критическая ошибка, Вы не можете писать в чат. Пожалуйста обратитесь в службу технической поддержки.'
                    });
                }
            });
        });

        /**
         * Потеря соединения
         */
        socket.on('disconnect', function () {
            socket.leave(socket.room);
            roomsClear(socket.id, function (result) {
                // Сообщение
                io.emit('message', {
                    status: -1,
                    message: 'Пользователь id' + socket.user.id + ' покинул комнату ' + socket.room
                });
                console.log('Disconnect. Clear: Sockets - ' + result.clearSockets + ', Connections - ' + result.clearConnections + ' Rooms - ' + result.clearRooms);
            });
        });
    });

} catch (e) {
    console.log('catch');
    console.log(e);
    logError(e);
}