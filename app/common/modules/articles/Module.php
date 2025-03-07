<?php

namespace common\modules\articles;

/**
 * Общий модуль [[Articles]]
 * Осуществляет всю работу со статьями.
 */
class Module extends \yii\base\Module
{
    /**
     * @var integer Кол-во статей котрое необходимо выводить на страницу
     */
    public $recordsPerPage = 12;

    /**
     * @var string Image path
     */
    public $imagePath = '@frontend/web/uploads/articles';

    /**
     * @var string Image URL
     */
    public $imageUrl = '/uploads/articles';
}
