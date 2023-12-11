<?php

/**
 * Главная страница
 * @var yii\base\View $this Представление
 * @var string $content Контент
 * @var $agencyReservations array of instances backend\modules\agency\models\ApartmentReservations
 * @var $callbacks array of instances backend\modules\callback\models\Callback,
 * @var $wantPasses array of instances backend\modules\agency\models\WantPass,
 * @var $selects array of instances backend\modules\agency\models\Select,
 * @var $helpdesks array of instances backend\modules\helpdesk\models\Helpdesk,
 * @var $detailsHistory array of instances backend\modules\agency\models\DetailsHistory,
 */

use yii\helpers\Html;

echo '<meta http-equiv="Refresh" content="1000" />';
$this->title = 'Панель управления';

echo \common\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Панель управления',
    'description' => 'Программный инструмент, позволяющий через графический интерфейс управлять проектом.',
    'breadcrumb' => [
        'Панель управления'
    ]
]);
?>

<?php if (Yii::$app->user->can('callback-view') && Yii::$app->user->can('agency-details-history-view')): ?>

<div id="admin-index-info">
    <h6 class="heading-hr"><i class="icon-grid"></i>Агентство</h6>
    <div class="row">
        <?php if (Yii::$app->user->can('callback-view')): ?>
            <div class="col-md-3">
                <div class="block">
                    <h6><i class="icon-phone"></i> Заявки на обратный звонок</h6>
                    <ul class="message-list pre-scrollable">
                        <li class="message-list-header">Необходимо перезвонить:</li>
                        <li>
                            <?php
                            if ($callbacks) {
                                foreach ($callbacks as $callback) :
                                    ?>
                                    <div class="clearfix">
                                        <div class="chat-member">
                                            <h6>
                                                <span class="status status-success"></span> <?= Html::encode($callback->phone) ?>
                                            </h6>
                                        </div>
                                        <div class="chat-actions">
                                            <?= HtmL::a('<i class="icon-phone2"></i>', ['/callback/default/update', 'id' => $callback->callback_id], ['class' => 'btn btn-link btn-icon btn-xs']) ?>
                                        </div>
                                    </div>
                                <?php
                                endforeach;
                            } else {
                                echo '<p>В данный момент нет заявок</p>';
                            }
                            ?>
                        </li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>


        <?php if (Yii::$app->user->can('agency-details-history-view')): ?>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading"><h6 class="panel-title">Заявки на отправку реквизитов</h6></div>
                    <div class="table-responsive pre-scrollable">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Объявление</th>
                                <th class="tdStatus">Реквизиты</th>
                                <th>Контакты</th>
                                <th class="tdStatus">Дата</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if ($detailsHistory) {
                                foreach ($detailsHistory as $detail) : ?>

                                    <?php
                                    if ($detail->advert->apartment->titleImage) {
                                        $aHtml = '<div class="thumbnail thumbnail-boxed">
                                                    <div class="thumb">
                                                        ' . Html::img($detail->advert->apartment->titleImage->previewSrc, ['alt' => '']) . '
                                                    </div>
                                                </div>';
                                    } else {
                                        $aHtml = '(картинка не задана)';
                                    } ?>

                                    <tr>

                                        <td class="tdImage"><?= Html::a($aHtml, ['/agency/details/update', 'id' => $detail->id]) ?></td>
                                        <td class="tdStatus"><?= \yii\helpers\ArrayHelper::getValue($detail->typeArray, $detail->type) ?></td>
                                        <td><?= 'Телефон: ' . Html::encode($detail->phone), '<br/> EMAIL: ', Html::encode($detail->email) ?></td>
                                        <td class="tdStatus"><?= Yii::$app->BasisFormat->helper('DateTime')->toFullDateTime($detail->date_create) ?></td>
                                    </tr>
                                <?php
                                endforeach;
                            } else {
                                echo '<tr><td colspan="4">В данный момент нет резерваций</td></tr>';
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-footer">
                        <?= HtmL::a('Посмотреть все новые заявки на реквизиты (' . $detailsHistory_count . ')', ['/agency/details/index'], ['class' => 'btn btn-link btn-icon btn-xs']) ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <p><br/></p>
    <?php endif; ?>


    <?php if (Yii::$app->user->can('agency-reservation-view')): ?>
        <div class="row">
            <div class="col-md-9">
                <div class="panel panel-default">
                    <div class="panel-heading"><h6 class="panel-title"><i class="icon-home6"></i> Заявки на бронь</h6>
                    </div>
                    <div class="table-responsive pre-scrollable">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Апартаменты</th>
                                <th>Пользователь</th>
                                <th>Количество человек</th>
                                <th>Дополнительная информация</th>
                                <th>Дата заезда</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if ($agencyReservations) {
                                foreach ($agencyReservations as $reservation) : ?>

                                    <?php
                                    if ($reservation->apartment->titleImage) {
                                        $aHtml = '<div class="thumbnail thumbnail-boxed">
                                                <div class="thumb">
                                                    ' . Html::img($reservation->apartment->titleImage->previewSrc, ['alt' => '']) . '
                                                </div>
                                            </div>';
                                    } else {
                                        $aHtml = '(картинка не задана)';
                                    } ?>

                                    <tr>
                                        <td class="tdId"><?= Html::a($aHtml, ['/agency/reservation/update', 'id' => $reservation->reservation_id]) ?></td>
                                        <td><?= Html::encode($reservation->name), ' ', Html::encode($reservation->email), ' ', Html::encode($reservation->phone) ?></td>
                                        <td class="text-center"><?= $reservation->clients_count ?></td>
                                        <td><?= Html::encode($reservation->more_info) ?></td>
                                        <td><?= Yii::$app->BasisFormat->helper('DateTime')->toFullDateTime($reservation->date_arrived) ?></td>
                                    </tr>
                                <?php
                                endforeach;
                            } else {
                                echo '<tr><td colspan="5">В данный момент нет резерваций</td></tr>';
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-footer">
                        <?= HtmL::a('Посмотреть все новые заявки на бронь (' . $agencyReservations_count . ')', ['/agency/reservation/index'], ['class' => 'btn btn-link btn-icon btn-xs']) ?>
                    </div>
                </div>
            </div>
        </div>
        <p><br/></p>
    <?php endif; ?>




    <?php if (Yii::$app->user->can('agency-select-view') && Yii::$app->user->can('agency-want-pass-view')): ?>
        <div class="row">

            <?php if (Yii::$app->user->can('agency-select-view')): ?>
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading"><h6 class="panel-title"><i class="icon-home6"></i> Быстро подберём
                                квартиру</h6></div>
                        <div class="table-responsive pre-scrollable">
                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Пользователь</th>
                                    <th>Типы аренды</th>
                                    <th>Станции метро</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if ($selects) {
                                    foreach ($selects as $select) : ?>
                                        <tr>
                                            <td class="tdId">
                                                <strong><?= HtmL::a('№ ' . $select->apartment_select_id, ['/agency/select/update', 'id' => $select->apartment_select_id]) ?></strong>
                                            </td>
                                            <td><?= $select->name ? Html::encode($select->name) : '(не задано)' ?></td>
                                            <td><?= $select->rent_types_array ? implode('<br/>', array_intersect_key($select->rentTypesList, array_flip($select->rent_types_array))) : '(не задано)' ?></td>
                                            <td><?= $select->metro_array ? implode('<br/>', array_intersect_key($select->metroStations, array_flip($select->metro_array))) : '(не задано)' ?></td>
                                        </tr>
                                    <?php
                                    endforeach;
                                } else {
                                    echo '<tr><td colspan="4">В данный момент нет заявок</td></tr>';
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="table-footer">
                            <?= HtmL::a('Посмотреть все новые заявки (' . $selects_count . ')', ['/agency/select/index'], ['class' => 'btn btn-link btn-icon btn-xs']) ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>


        <p><br/></p>
        <p><br/></p>
    <?php endif; ?>


    <?php if (Yii::$app->user->can('helpdesk-view')): ?>
        <h6 class="heading-hr"><i class="icon-grid"></i> Связь с пользователями</h6>

        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading"><h6 class="panel-title"><i class="icon-support"></i> Техническая
                            поддержка</h6></div>
                    <div class="table-responsive pre-scrollable">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Тема</th>
                                <th class="tdStatus">Приоритет</th>
                                <th class="tdStatus">Тип обращения</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if ($helpdesks) {
                                foreach ($helpdesks as $helpdesk) : ?>
                                    <tr>

                                        <td class="tdId"><?= HtmL::a($helpdesk->ticket_id, ['/helpdesk/default/view', 'id' => $helpdesk->ticket_id]) ?></td>
                                        <td><?= HtmL::a(Html::encode($helpdesk->theme), ['/helpdesk/default/view', 'id' => $helpdesk->ticket_id]) ?></td>
                                        <td class="tdStatus"><?= Yii::$app->BasisFormat->helper('Status')->getItem($helpdesk->priorityArray, $helpdesk->priority) ?></td>
                                        <td class="tdStatus"><?= Yii::$app->BasisFormat->helper('Status')->getItem($helpdesk->departmentArray, $helpdesk->department) ?></td>
                                    </tr>
                                <?php
                                endforeach;
                            } else {
                                echo '<tr><td colspan="4">В данный момент нет обращений</td></tr>';
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-footer">
                        <?= HtmL::a('Посмотреть все новые обращения (' . $helpdesks_count . ')', ['/helpdesk/default/index'], ['class' => 'btn btn-link btn-icon btn-xs']) ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>