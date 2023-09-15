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
        $this->needRedirect = $this->checkParamsForRedirect($params);

        return parent::createUrl($params);
    }

    private function checkParamsForRedirect(array $params):bool
    {
        $action = $params[0];
        foreach (Yii::$app->params['actionsWithSubdomain'] as $actionRule) {
            if (str_starts_with($action, $actionRule)) {
                return true;
            }
        }

        return false;
    }

    public function getBaseUrl() : string
    {
        if ($this->needRedirect) {
            return Yii::$app->request->hostInfo;
        }

        return '';
    }
}
