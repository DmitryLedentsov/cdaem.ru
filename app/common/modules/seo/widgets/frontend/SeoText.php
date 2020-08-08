<?php

namespace common\modules\seo\widgets\frontend;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use common\modules\seo\models\Seotext as SeotextModel;

/**
 * Виджет отображает Seo текст на странице
 * @package common\modules\site\widgets
 */
class SeoText extends Widget
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $tag = 'div';

    /**
     * @var array
     */
    public $options = ['class' => 'big-info'];

    /**
     * @var \yii\db\Query
     */
    private $_seoText;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $hostInfo = str_replace('www.', '', Yii::$app->request->hostInfo);

        if (Yii::$app->params['siteDomain'] == $hostInfo) {
            $currentUrl = Yii::$app->request->url;

            $this->_seoText = (new \yii\db\Query())
                ->select('*')
                ->from(SeotextModel::tableName())
                ->where('url = :url', [':url' => $currentUrl])
                ->andWhere('type = :type', [':type' => $this->type])
                ->andWhere('visible = 1')
                ->one();
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->_seoText && !empty($this->_seoText['text'])) {
            return Html::tag($this->tag, $this->_seoText['text'], $this->options);
        }

        return '';
    }
}
