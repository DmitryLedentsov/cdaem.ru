<?php

use yii\helpers\Url;

?>

<ul class="graphical-menu clearfix">
    <li <?= strripos(Yii::$app->request->pathInfo, '/dialog') ? 'class="active"' : '' ?> title="Ваши переписки">
        <span class="gm-icon message"></span> <a
                href="<?= Url::toRoute(['/messages/default/index']) ?>">Сообщения <?= $newMessagesCount ? '<div class="label">' . $newMessagesCount . '</div>' : '' ?></a>
    </li>

    <li <?= Yii::$app->request->pathInfo == 'office/bookmark' ? 'class="active"' : '' ?>
            title="Пользователи, которых Вы добавили в избранное">
        <span class="gm-icon favorites"></span> <a
                href="<?= Url::toRoute(['/office/default/bookmark']) ?>">Избранное</a>
    </li>

    <li <?= Yii::$app->request->pathInfo == 'office/blacklist' ? 'class="active"' : '' ?>
            title="Пользователи, которых Вы заблокировали">
        <span class="gm-icon ban"></span> <a href="<?= Url::toRoute(['/office/default/blacklist']) ?>">Черный список</a>
    </li>

    <li <?= (Yii::$app->request->pathInfo == 'office/total-bid' or Yii::$app->request->pathInfo == 'office/total-bid/open' or Yii::$app->request->pathInfo == 'office/total-bid/all') ? 'class="active"' : '' ?>
            title="Общие заявки и предложения всех пользователей">
        <span class="gm-icon bid"></span> <a
                href="<?= Url::toRoute(['/partners/reservation/total-bid', 'find' => 'all']) ?>">Заявки</a> <?= $reservationsForAll ? '<div class="label">' . $reservationsForAll . '</div>' : '' ?>
    </li>

    <li <?= Yii::$app->request->pathInfo == 'office/reservations' ? 'class="active"' : '' ?>
            title="Заявки на бронь Ваших апартаментов">
        <span class="gm-icon reservations"></span> <a
                href="<?= Url::toRoute(['/partners/reservation/reservations']) ?>">Бронь</a> <?= $reservationsForAdvert ? '<div class="label">' . $reservationsForAdvert . '</div>' : '' ?>
    </li>
</ul>