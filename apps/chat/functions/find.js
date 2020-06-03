/**
 * Поиск комнаты с собеседником
 * Обходим все комнаты и ищем комнату где waiting: [userId, interlocutorId]
 * @param userId
 * @param interlocutorId
 * @param cb
 * @returns {*}
 */
function findWaitingRoomByUserIdAndInterlocutorId(userId, interlocutorId, cb)
{
    for (var room in chat.rooms) {
        if (
            (chat.rooms[room].waiting[0] == userId && chat.rooms[room].waiting[1] == interlocutorId) ||
            (chat.rooms[room].waiting[0] == interlocutorId && chat.rooms[room].waiting[1] == userId)
        ) {
            return cb(chat.rooms[room].name);
        }
    }
    return cb(null);
}


/**
 * Поиск комнаты по hash
 * @param hash
 * @param cb
 * @returns {*}
 */
function findRoomByHash(hash, cb)
{
    for (var room in chat.rooms) {
        if (chat.rooms[room].name == hash) {
            return cb(chat.rooms[room]);
        }
    }
    return cb(null);
}


/**
 * Поиск комнаты по сокету
 * @param socketId
 * @param cb
 * @returns {*}
 */
function findRoomBySocketId(socketId, cb)
{
    for (var room in chat.rooms) {
        for (var connection in chat.rooms[room].connections) {
            if (chat.rooms[room].connections[connection].sockets.indexOf(socketId) >= 0) {
                return cb(chat.rooms[room]);
            }
        }
    }
    return cb(null);
}