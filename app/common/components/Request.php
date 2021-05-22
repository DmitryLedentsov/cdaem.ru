<?php

namespace common\components;

use yii;

/**
 * Class Request
 * @package common\components
 */
class Request extends \yii\web\Request
{
    /**
     * @return string|null
     */
    public function getCurrentCitySubDomain(): ?string
    {
        if (isset(Yii::$app->params['siteDomain']) === false) {
            return null;
        }

        $paramSiteDomainHost = trim(trim(parse_url(Yii::$app->params['siteDomain'], PHP_URL_HOST)), '/');
        $currentHost = trim(trim(parse_url($this->hostInfo, PHP_URL_HOST)), '/');

        if (mb_strtolower($paramSiteDomainHost) === mb_strtolower($currentHost)) {
            return null;
        }

        return trim((string)str_replace(sprintf('.%s', $paramSiteDomainHost), '', $currentHost));
    }

    /**
     * @return string
     */
    public function getCurrentUrlWithoutSubDomain(): string
    {
        if (!$citySubDomain = $this->getCurrentCitySubDomain()) {
            return $this->absoluteUrl;
        }

        $currentScheme = parse_url($this->hostInfo, PHP_URL_SCHEME);

        return str_replace(
            sprintf('%s://%s.', $currentScheme, $citySubDomain),
            sprintf('%s://', $currentScheme),
            $this->absoluteUrl
        );
    }
}
