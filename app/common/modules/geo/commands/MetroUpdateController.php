<?php

namespace common\modules\geo\commands;

use Yii;
use yii\helpers\Console;

class MetroUpdateController extends \yii\console\Controller
{
    // Действует метрополитен в 7 городах: Москве, Санкт-Петербурге, Нижнем Новгороде, Новосибирске, Казани, Екатеринбурге и Самаре.
    // https://api.hh.ru/areas
    public static array $cityIdListHH = [
        "1" => "Москва",
        "2" => "Санкт-Петербург",
        "66" => "Нижний Новгород",
        "4" => "Новосибирск",
        "88" => "Казань",
        "3" => "Екатеринбург",
        "78" => "Самара",
    ];

    public static string $metroApiUrlHH = "https://api.hh.ru/metro/";

    /**
     * Обновляет информацию по станциям метро, используя API hh.ru
     *
     * Вызов команды: php yii geo/metroUpdate
     *
     * TODO: НЕОБХОДИМО НАСТРОИТЬ КРОН ДЛЯ ВЫПОЛНЕНИЯ ДАННОГО СЦЕНАРИЯ
     */
    public function actionIndex()
    {
        foreach (self::$cityIdListHH as $cityIdHH => $cityName) {
            try {
                $jsonRaw = file_get_contents(self::$metroApiUrlHH . $cityIdHH);
            } catch (\Exception $e) {
                echo "Ошибка при обращении к API: " . $e->getMessage() . PHP_EOL;
                continue;
            }

            if (!$jsonRaw) {
                echo "Пустой ответ. Город '$cityName', id hh '$cityIdHH'" . PHP_EOL;
                continue;
            }

            $json = json_decode($jsonRaw);
            $city = \common\modules\geo\models\City::findOne(["name" => $cityName]);

            if (!$city) {
                echo "Не найден город '$cityName'" . PHP_EOL;
                continue;
            }

            foreach ($json->lines as $line) {
                foreach ($line->stations as $station) {
                    $name = $station->name;
                    $lat = $station->lat;
                    $lon = $station->lng;
                    $cityId = $city->city_id;

                    $existStation = \common\modules\geo\models\Metro::findOne(["name" => $name, "city_id" => $cityId]);

                    if (!$existStation) {
                        $existStation = new \common\modules\geo\models\Metro([
                            "city_id" => $cityId,
                            "name" => $name,
                            "latitude" => $lat,
                            "longitude" => $lon,
                        ]);
                        echo "$cityName ($cityId): создана станция '$name'" . PHP_EOL;
                    }
                    else {
                        $existStation->latitude = $lat;
                        $existStation->longitude = $lon;
                        echo "$cityName ($cityId): обновляем координаты станции '$name'" . PHP_EOL;
                    }

                    $existStation->save(false);
                }
            }
            sleep(5);
        }
    }
}
