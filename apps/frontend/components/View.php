<?php

namespace frontend\components;

use common\modules\seo\models\SeoSpecification;
use yii\helpers\Html;
use Yii;

/**
 * @inheritdoc
 * @package frontend\components
 */
class View extends \yii\web\View
{
    private $metaTagsRegistered = false;
    private $dbTags;

    /**
     * @inheritdoc
     */
    public function registerTitle($default = null)
    {
        if ($this->dbMetaTagsExists()) {
            return $this->title = $this->dbTags['title'];
        }

        return $this->title = $default;
    }

    /**
     * @inheritdoc
     */
    public function registerMetaTag($options, $key = null)
    {
        if ($this->metaTagsRegistered) {
            return;
        }

        if ($this->registerDbMetaTags()) {
            return;
        }

        if ($key === null) {
            $this->metaTags[] = Html::tag('meta', '', $options);
        } else {
            $this->metaTags[$key] = Html::tag('meta', '', $options);
        }
    }

    /**
     * @return bool
     */
    private function registerDbMetaTags()
    {
        if (!$this->dbMetaTagsExists()) {
            return false;
        }

        if (!empty($this->dbTags['description'])) {
            $this->metaTags[] = Html::tag('meta', '', ['name' => 'description', 'content' => $this->dbTags['description']]);
        }

        if (!empty($this->dbTags['keywords'])) {
            $this->metaTags[] = Html::tag('meta', '', ['name' => 'keywords', 'content' => $this->dbTags['keywords']]);
        }

        $this->metaTagsRegistered = true;
        return true;
    }

    /**
     * Возвращает есть ли мета теги в БД, отталкиваясь от поддомена(домена третьего уровня) и URL-пути
     * @return boolean
     */
    private function dbMetaTagsExists()
    {
        if ($this->dbTags !== null) {
            return (bool)$this->dbTags;
        }


        $requestedUrl = parse_url(Yii::$app->request->absoluteUrl);
        $domain = explode('.', $requestedUrl['host']);
        $domain = array_reverse($domain);

        // Берем третий уровень домена, предполагая, что доменное имя все же есть
        $subDomain = isset($domain[2]) ? $domain[2] : null;

        $dbTags = SeoSpecification::find()->where([
            'url' => $requestedUrl['path'],
            'city' => $subDomain
        ])->asArray()->one();

        if ($dbTags) {
            $this->dbTags = $dbTags;
            return true;
        }

        $this->dbTags = false;
        return false;
    }

    /**
     * @return bool
     */
    public function serviceHead()
    {
        if (!$this->dbMetaTagsExists()) {
            return false;
        }

        return $this->dbTags['service_head'];
    }

    /**
     * @return bool
     */
    public function serviceFooter()
    {
        if (!$this->dbMetaTagsExists()) {
            return false;
        }

        return $this->dbTags['service_footer'];
    }
}
