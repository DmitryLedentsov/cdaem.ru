<?php

namespace common\modules\agency\commands;

use common\modules\agency\models\Image;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\Url;
use yii\log\Logger;
use Yii;

/**
 * Сборщик мусора
 * @package common\modules\agency\commands
 */
class CollectorController extends \yii\console\Controller
{
    /**
     * Garbage Collector Images
     * Сборщик мусора
     *
     * Осуществляет поиск старых и/или удаленных изображений
     * Осуществляет чистку диска и базы данных
     *
     * Вызов команды: php yii agency/collector/images
     *
     * TODO: НЕОБХОДИМО НАСТРОИТЬ КРОН ДЛЯ ВЫПОЛНЕНИЯ ДАННОГО СЦЕНАРИЯ
     */
    public function actionImages()
    {
        //models\Image
        $this->stdout('Чистит изображения' . PHP_EOL);

        $path = Yii::getAlias('@frontend/web/images') . '/';
        $thumbsPath = Yii::getAlias('@frontend/web/images/thumbs') . '/';
        $result = [];
        // Удаление записей в БД ссылающихся на несуществующие файлы Больших картинок
        $image = true;
        $offset = 0;
        $result['deleted_rows'] = 0;
        while ($image) {
            $image = Image::find()->limit(1)->offset($offset)->asArray()->one();
            $offset++;
            if (!$image) continue;
            if (!file_exists($path . $image['review'])) {
                $result['deleted_rows'] += (new \yii\db\Query())
                    ->createCommand()
                    ->delete(Image::tableName(), ['image_id' => $image['image_id']])
                    ->execute();
                $offset--;
            }
        }
        // Удаление картинок или любых других файлов с папки, если на них нету записи в БД
        $result['deleted_reviews'] = 0;
        $result['deleted_previews'] = 0;
        $result['files'] = [];
        if ($handle = opendir($path)) {

            while (false !== ($file = readdir($handle))) {
                if ($file == '.gitignore') continue;
                if (!is_file($path . $file)) continue;

                $image = Image::find()->where(['review' => $file])->asArray()->one();
                if (!$image) {
                    $result['deleted_reviews'] += @unlink($path . $file);
                    $result['deleted_previews'] += @unlink($thumbsPath . $file . '_100x80.jpg');
                }
            }
            closedir($handle);
        }
        // Удаление маленьких файлов
        if ($handle = opendir($thumbsPath)) {

            while (false !== ($file = readdir($handle))) {
                if ($file == '.gitignore') continue;
                if (!is_file($thumbsPath . $file)) continue;

                $image = Image::find()->where(['preview' => $file])->asArray()->one();
                if (!$image) {
                    $result['deleted_previews'] += @unlink($thumbsPath . $file);
                }

            }
            closedir($handle);
        }

        print_r($result);
    }
}