/**
 * Верификация пользователя
 * @param hash
 * @param cb
 */
function verifyHash(hash, cb) {
    var user = [];

    var sql = 'SELECT user.id, session.id AS hash, profile.name, profile.surname, profile.avatar_url, profile.second_name FROM `users` user ';
    sql += 'LEFT JOIN `users_profile` profile ON user.id = profile.user_id ';
    sql += 'LEFT JOIN `session` session ON user.id = session.user_id ';
    sql += 'WHERE session.id = ? LIMIT 1;';

    MysqlDBconnection.query(sql, [hash], function (err, rows) {
        if (err) {
            logError('Не найден пользователь по хэшу: ' + hash);
            throw err;
        }
        var result = null;
        if (rows.length > 0) {
            var user = rows[0];
            result = {
                id: user.id,
                name: user.name,
                surname: user.surname,
                second_name: user.second_name,
                avatar: user.avatar_url
            };
        }
        cb(result);
    });
}


/**
 * Проверить список заблокированных
 * @param user_id
 * @param interlocutor_id
 * @param cb
 */
function checkBlackList(user_id, interlocutor_id, cb) {
    var user = [];
    var sql = 'SELECT COUNT(record_id) FROM `users_list` WHERE user_id = ? AND interlocutor_id = ? AND type = 0';
    MysqlDBconnection.query(sql, [interlocutor_id, user_id], function (err, rows) {
        if (err) {
            throw err;
        }
        if (rows.length) {
            rows = rows[0]['COUNT(record_id)'];
        }
        cb(rows);
    });
}


/**
 * Отправить сообщение
 * @param user_id
 * @param interlocutor_id
 * @param theme
 * @param text
 * @param cb
 */
function sendMessage(user_id, interlocutor_id, theme, text, cb) {

    MysqlDBconnection.beginTransaction(function (err) {
        if (err) {
            throw err;
        }

        var message = {
            theme: theme,
            text: text,
            date: getDateTime(),
            message_id: null
        };

        MysqlDBconnection.query('INSERT INTO user_messages (theme, text, date_create) VALUES (?, ?, ?);', [message.theme, message.text, message.date], function (err, result) {
            if (err) {
                MysqlDBconnection.rollback(function () {
                    throw err;
                });
            }

            message.message_id = result.insertId;

            // Сообщение для пользователя
            MysqlDBconnection.query('INSERT INTO user_messages_mailbox (`message_id`, `user_id`, `interlocutor_id`, `read`, `inbox`) VALUES (?, ?, ?, ?, ?);',
                [message.message_id, user_id, interlocutor_id, 1, 0], function (err, result) {
                    if (err) {
                        MysqlDBconnection.rollback(function () {
                            throw err;
                        });
                    }

                    if (user_id != interlocutor_id) {
                        // Сообщение для собеседника
                        MysqlDBconnection.query('INSERT INTO user_messages_mailbox (`message_id`, `user_id`, `interlocutor_id`, `read`, `inbox`) VALUES (?, ?, ?, ?, ?);',
                            [message.message_id, interlocutor_id, user_id, 0, 1], function (err, result) {
                                if (err) {
                                    MysqlDBconnection.rollback(function () {
                                        throw err;
                                    });
                                }
                                MysqlDBconnection.commit(function (err) {
                                    if (err) {
                                        MysqlDBconnection.rollback(function () {
                                            throw err;
                                        });
                                    }
                                    return cb(true, message);
                                });
                            });
                    } else {
                        MysqlDBconnection.commit(function (err) {
                            if (err) {
                                MysqlDBconnection.rollback(function () {
                                    throw err;
                                });
                            }
                            return cb(true, message);
                        });
                    }
                });
        });
    });
}