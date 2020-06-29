<?php
use frontend\modules\partners\widgets\PreviewAdvertTmp;
use yii\helpers\Html;
?>

<div class="apartment-list clearfix">

    <?php if ($userAdverts) : ?>

        <?php foreach ($userAdverts as $advert): ?>

            <div class="item">
                <?=PreviewAdvertTmp::widget([
                    'advert' => $advert,
                    'enableAdvertPosition' => true,
                    'customUrl' => ['/partners/default/update', 'id' => $advert->apartment_id],
                ]);?>
            </div>

        <?php endforeach; ?>

        <div class="clearfix"></div>

        <div class="separator-e">
            <div class="line-left"></div>
            <div class="title"><?=Html::a('Показать все', ['/partners/default/apartments'])?> </div>
            <div class="line-right"></div>
        </div>
    <?php else: ?>

        <p>У Вас нет объявлений... <br/> <?=Html::a('Добавить', ['/partners/default/create'])?> </p>

    <?php endif; ?>

</div>