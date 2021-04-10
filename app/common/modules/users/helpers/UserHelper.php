<?php

namespace common\modules\users\helpers;

use common\modules\users\models\backend\Profile;
use yii\helpers\Html;
use common\modules\users\models\User;

/**
 * Class UserHelper
 * @package common\modules
 */
class UserHelper
{
    /**
     * @return array
     */
    public static function getUserTypeArray(): array
    {
        return Profile::getUserTypeArray();
    }

    /**
     * Возвращает html блок открытых контактов у пользователя
     * @param User $user
     * @return string
     */
    public static function getOpenContactsBlock(User $user)
    {
        $result = Html::tag('p', Html::tag('b', self::getUserName($user)));

        $email = $user->email ? self::formatEmail($user->email) : 'Не указан';
        $phone = $user->phone ? self::formatPhone($user->phone) : 'Не указан';

        $result .= Html::tag('p', 'EMAIL: ' . $email);
        $result .= Html::tag('p', 'Тел.: ' . $phone);

        return $result;
    }

    /**
     * Возвращает имя пользователя
     * @param User $user
     * @return string
     */
    public static function getUserName(User $user)
    {
        $name = [];

        if ($user->profile->name) {
            $name[] = $user->profile->name;
        }

        if ($user->profile->surname) {
            $name[] = $user->profile->surname;
        }

        if (empty($name)) {
            return 'Имя не указано';
        }

        return implode(' ', $name);
    }

    /**
     * Форматирует номер телефона
     * @param $phone
     * @return string
     */
    public static function formatPhone($phone)
    {
        if (empty($phone)) {
            return 'не указан';
        }

        $count = mb_strlen($phone);
        if ($phone[0] == '+') {
            $phone = str_replace('+', '', $phone);
        }
        if ($phone[0] == '+' && $phone[1] == '7') {
            $phone = substr($phone, 2);
            $p = '8';
        } elseif ($phone[0] == '+' && $phone[1] == '3') {
            $phone = substr($phone, 1);
            $p = '+3';
        } else {
            $phone = substr($phone, 1);
        }
        $p = '8';


        if ($count >= 8) {
            $sPrefix = substr($phone, 0, $count - 8);
            $sNumber1 = substr($phone, $count - 8, 3);
            $sNumber2 = substr($phone, $count - 5, 2);
            $sNumber3 = substr($phone, $count - 3, 2);

            return $p . '(' . $sPrefix . ')-' . $sNumber1 . '-' . $sNumber2 . '-' . $sNumber3;
        }

        return $p . $phone;
    }

    //Format phone 2

    public static function formatPhone2($phone)
    {
        if (empty($phone)) {
            return 'не указан';
        }

        $count = mb_strlen($phone);
        if ($phone[0] == '+') {
            $phone = str_replace('+', '', $phone);
        }
        $p = '+';

        if ($count >= 8) {
            $sPrefix = substr($phone, 0, $count - 7);
            $sNumber1 = substr($phone, $count - 7, 3);
            $sNumber2 = substr($phone, $count - 4, 2);
            $sNumber3 = substr($phone, $count - 2, 2);

            return '' . $sPrefix . '' . '-' . $sNumber1 . '-' . $sNumber2 . '-' . $sNumber3;
        }

        return $p . $phone;
    }

    /**
     * Форматирует почтовый адрес
     * @param $email
     * @return string
     */
    public static function formatEmail($email)
    {
        if (empty($email)) {
            return 'Не указан';
        }

        return Html::mailto($email, $email);
    }
}
