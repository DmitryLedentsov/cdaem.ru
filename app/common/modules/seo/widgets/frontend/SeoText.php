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
    public string $type;

    /**
     * @var string
     */
    public string $tag = 'div';

    /**
     * @var array
     */
    public array $options = ['class' => 'big-info'];

    /**
     * @var array|null
     */
    private ?array $seoText;

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        $hostInfo = $this->clearHost(Yii::$app->request->hostInfo);
        $paramSiteDomain = $this->clearHost(Yii::$app->params['siteDomain']);

        if ($paramSiteDomain === $hostInfo) {
            $currentUrl = Yii::$app->request->url;

            $seoText = (new \yii\db\Query())
                ->select('*')
                ->from(SeotextModel::tableName())
                ->where('url = :url', [':url' => $currentUrl])
                ->andWhere('type = :type', [':type' => $this->type])
                ->andWhere('visible = 1')
                ->one();

            if ($seoText) {
                $this->seoText = $seoText;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function run(): string
    {
        if ($this->seoText && !empty($this->seoText['text'])) {
            return Html::tag($this->tag, $this->seoText['text'], $this->options);
        }

        return '';
    }

    /**
     * @param string $host
     * @return string
     */
    private function clearHost(string $host): string
    {
        return mb_strtolower(str_replace(['http://', 'https://', 'http://www.', 'https://www.'], '', $host));
    }
}
