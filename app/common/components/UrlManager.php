<?php

namespace common\components;

use Yii;
use yii\helpers\Url;
use yii\web\Request;
use yii\base\InvalidConfigException;

class UrlManager extends \yii\web\UrlManager
{
    private bool $needRedirect = false;

    public function createUrl($params): string
    {
        $this->needRedirect = $this->checkUrlForRedirect($params);

        return parent::createUrl($params);
    }

    private function checkUrlForRedirect(array $params):bool
    {
        foreach ($params as $param) {
            foreach (Yii::$app->params['redirectablePaths'] as $path) {
                if (str_starts_with($param, $path)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getBaseUrl()
    {
        if ($this->needRedirect) {
            return Yii::$app->request->hostInfo;
        }

        return '';
    }
}
