/**
 * Добавить пользователя в комнату
 * @param data
 * @param cb
 * @returns {*}
 */
function roomUserPush(data, cb) {
    if (data.roomHash) {
        findRoomByHash(data.roomHash, function (room) {
            if (room) {
                var connection = null;
                for (var user in room.connections) {
                    if (data.user.id == room.connections[user].user.id) {
                        connection = room.connections[user];
                        break;
                    }
                }
                if (connection) {
                    connection.sockets.push(data.socket.id);
                } else {
                    room.connections.push({
                        sockets: [data.socket.id],
                        user: data.user
                    });
                }
                return cb(room);
            } else {
                createRoom(data, function (room) {
                    return cb(room);
                });
            }
        });
    } else {
        createRoom(data, function (room) {
            return cb(room);
        });
    }
}


/**
 * Создать комнату
 * @param data
 * @param cb
 * @returns {*}
 */
function createRoom(data, cb) {
    var newRoom = {
        name: uuid.v1(),
        waiting: [data.user.id, data.interlocutor_id],
        connections: [{
            sockets: [data.socket.id],
            user: data.user
        }]
    };
    chat.rooms.push(newRoom);
    return cb(newRoom);
}


/**
 * Почистить комнаты
 * @param socketId
 * @param cb
 */
function roomsClear(socketId, cb) {
    var result = {
        clearRooms: 0,
        clearSockets: 0,
        clearConnections: 0

    };

    // Проходим по всем комнатам
    for (var room in chat.rooms) {
        // Проходим все соединения
        for (var connection in chat.rooms[room].connections) {
            var socketKey = chat.rooms[room].connections[connection].sockets.indexOf(socketId);

            // Если найден текущий сокет
            if (socketKey >= 0) {
                // Удаляем его из списка сокетов
                chat.rooms[room].connections[connection].sockets.splice(socketKey, 1);
                result.clearSockets++;

                // Если больше сокетов нет
                if (chat.rooms[room].connections[connection].sockets.length <= 0) {

                    // Удалить подключение
                    chat.rooms[room].connections.splice(connection, 1);
                    result.clearConnections++;

                    // В комнате больше нет пользователей, необходимо ее удалить
                    if (chat.rooms[room].connections.length <= 0) {
                        // удалить комнату
                        chat.rooms.splice(room, 1);
                        result.clearRooms++;
                    }
                }
            }
        }
    }

    cb(result);
}