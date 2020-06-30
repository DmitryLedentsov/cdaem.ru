<?php

use yii\helpers\Url;


?>

<ul class="graphical-menu clearfix">
    <li <?= strripos(Yii::$app->request->pathInfo, '/profile') ? 'class="active"' : '' ?> title="edit">
        <span class="gm-icon message"></span> <a style="width:100%;position: relative;display:block;padding-left: 20px"
                                                 href="<?= Url::toRoute(['/users/user/profile']) ?>">Редактировать
            профиль <?= $newMessagesCount ? '<div class="label">' . $newMessagesCount . '</div>' : '' ?><i
                    class="fa fa-pencil" style="color: #f4ad49;position: absolute;right: 25px;top: 7px;"></i></a>
    </li>
    <li <?= strripos(Yii::$app->request->pathInfo, '/dialog') ? 'class="active"' : '' ?> title="Ваши переписки">
        <span class="gm-icon message"></span> <a style="width:100%;position: relative;display:block;padding-left: 20px"
                                                 href="<?= Url::toRoute(['/messages/default/index']) ?>">Сообщения <?= $newMessagesCount ? '<div class="label">' . $newMessagesCount . '</div>' : '' ?>
            <i class="fa fa-envelope" style="color: #f4ad49;position: absolute;right: 25px;top: 7px;"></i></a>
    </li>

    <li <?= Yii::$app->request->pathInfo == 'office/bookmark' ? 'class="active"' : '' ?>
            title="Пользователи, которых Вы добавили в избранное">
        <span class="gm-icon favorites"></span> <a
                style="width:100%;position: relative;display:block;padding-left: 20px"
                href="<?= Url::toRoute(['/office/default/bookmark']) ?>">Избранное<i class="fa fa-star"
                                                                                     style="color: #f4ad49;position: absolute;right: 25px;top: 7px;"></i></a>
    </li>

    <li <?= Yii::$app->request->pathInfo == 'office/blacklist' ? 'class="active"' : '' ?>
            title="Пользователи, которых Вы заблокировали">
        <span class="gm-icon ban"></span> <a style="width:100%;position: relative;display:block;padding-left: 20px"
                                             href="<?= Url::toRoute(['/office/default/blacklist']) ?>">Черный список<i
                    class="fa fa-ban" style="color: #f4ad49;position: absolute;right: 25px;top: 7px;"></i></a>
    </li>

    <li <?= (Yii::$app->request->pathInfo == 'office/total-bid' or Yii::$app->request->pathInfo == 'office/total-bid/open' or Yii::$app->request->pathInfo == 'office/total-bid/all') ? 'class="active"' : '' ?>
            title="Общие заявки и предложения всех пользователей">
        <span class="gm-icon bid"></span> <a style="width:100%;position: relative;display:block;padding-left: 20px"
                                             href="<?= Url::toRoute(['/partners/reservation/total-bid', 'find' => 'all']) ?>">Заявки<i
                    class="fa fa-list"
                    style="color: #f4ad49;position: absolute;right: 25px;top: 7px;"></i></a> <?= $reservationsForAll ? '<div class="label">' . $reservationsForAll . '</div>' : '' ?>
    </li>

    <li <?= Yii::$app->request->pathInfo == 'office/reservations' ? 'class="active"' : '' ?>
            title="Заявки на бронь Ваших апартаментов">
        <span class="gm-icon reservations"></span> <a
                style="width:100%;position: relative;display:block;padding-left: 20px"
                href="<?= Url::toRoute(['/partners/reservation/reservations']) ?>">Бронь<i class="fa fa-book"
                                                                                           style="color: #f4ad49;position: absolute;right: 25px;top: 7px;"></i></a> <?= $reservationsForAdvert ? '<div class="label">' . $reservationsForAdvert . '</div>' : '' ?>
    </li>
</ul>