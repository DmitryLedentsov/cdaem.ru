<?php

use yii\helpers\Html;

?>

<h3>Мои объявления:</h3>

<?php if (!empty($userAdvertsCount)) : ?>
        <?php foreach($userAdvertsCount as $advertsType): ?>

        <ul class="rent-type-list clearfix">
            <li>
                <div class="name"><?=Html::a($advertsType['rentTypeName'], ['/partners/default/apartments', 'filter' => 'slug=' . $advertsType['slug']])?></div>
                <div class="count"><?= $advertsType['count']; ?></div>
            </li>
        </ul>
        
        <?php endforeach; ?>

<?php endif; ?>