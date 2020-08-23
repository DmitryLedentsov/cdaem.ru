<?php

namespace console\controllers;

use Yii;
use yii\helpers\Console;
use vitalik74\geocode\Geocode;
use common\modules\agency\models\Apartment as ApartmentAgency;
use common\modules\partners\models\Apartment as ApartmentPartners;

/**
 * php yii apartment/set-geo-code
 *
 * Apartment Controller
 * @package console\controllers
 */
class ApartmentController extends \yii\console\Controller
{
    /**
     * @throws \Exception
     * @throws \yii\db\Exception
     */
    public function actionSetGeoCode()
    {
        $apartmentsAgencyQuery = ApartmentAgency::find()
            ->joinWith(['city' => function ($query) {
                $query->joinWith('country');
            }]);

        $apartmentsPartnersQuery = ApartmentPartners::find()
            ->joinWith(['city' => function ($query) {
                $query->joinWith('country');
            }]);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $n = 0;
            $totalCount = $apartmentsAgencyQuery->count() + $apartmentsPartnersQuery->count();
            Console::startProgress($n, $totalCount);

            foreach ($apartmentsAgencyQuery->all() as $apartmentAgency) {
                Console::updateProgress(++$n, $totalCount);
                $address = $apartmentAgency->city->country->name . ' ' . $apartmentAgency->city->name . ' ' . $apartmentAgency->address;
                if (!$coords = $this->getGeo($address)) {
                    continue;
                }
                $apartmentAgency->longitude = $coords['longitude'];
                $apartmentAgency->latitude = $coords['latitude'];
                $apartmentAgency->save(false);
            }

            foreach ($apartmentsPartnersQuery->batch(100) as $apartmentsPartners) {
                foreach ($apartmentsPartners as $apartmentPartners) {
                    Console::updateProgress(++$n, $totalCount);
                    $address = $apartmentPartners->city->country->name . ' ' . $apartmentPartners->city->name . ' ' . $apartmentPartners->address;
                    if (!$coords = $this->getGeo($address)) {
                        continue;
                    }
                    $apartmentPartners->longitude = $coords['longitude'];
                    $apartmentPartners->latitude = $coords['latitude'];
                    $apartmentPartners->save(false);
                }
            }

            Console::endProgress();

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * @param $address
     * @return array|bool
     */
    public function getGeo($address)
    {
        $geo = new Geocode();
        $api = $geo->get($address, ['kind' => 'house']);

        $pos = null;
        if (isset($api['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos'])) {
            $pos = $api['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos'];
        }

        if (!$pos) {
            return false;
        }

        $coord = @explode(' ', $pos);

        $latitude = isset($coord[0]) ? $coord[0] : null;
        $longitude = isset($coord[1]) ? $coord[1] : null;

        return [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];
    }
}
