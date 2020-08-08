<?php

namespace common\modules\users\models\backend;

/**
 * @inheritdoc
 */
class Profile extends \common\modules\users\models\Profile
{
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'create' => ['name', 'surname', 'birthday', 'whau', 'about_me', 'legal_person', 'user_type', 'user_partner', 'user_partner_verify', 'user_partner_rating', 'phone2', 'phones', 'email', 'skype', 'ok', 'vk', 'phones', 'fb', 'twitter'],
            'update' => ['name', 'surname', 'birthday', 'whau', 'about_me', 'legal_person', 'user_type', 'user_partner', 'user_partner_verify', 'user_partner_rating', 'phone2', 'phones', 'email', 'skype', 'ok', 'vk', 'phones', 'fb', 'twitter'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // Name
            ['name', 'match', 'pattern' => '/^[a-zа-яё]+$/iu'],

            // Surname
            ['surname', 'match', 'pattern' => '/^[a-zа-яё]+(-[a-zа-яё]+)?$/iu'],

            // Whau
            ['whau', 'string', 'min' => 1, 'max' => 255],

            // Birthday
            ['birthday', 'string'],
            ['birthday', 'date', 'format' => 'php:Y-m-d'],

            // About Me
            ['about_me', 'string'],

            // User partner rating
            ['user_partner_rating', 'integer'],

            // Contacts
            [['phone2', 'phones', 'email', 'skype', 'ok', 'vk', 'phones', 'fb', 'twitter'], 'string'],

            // Legal Person
            ['legal_person', 'boolean'],

            ['user_type', 'in', 'range' => array_keys($this->getUserTypeArray())],
            ['user_partner', 'boolean'],
            ['user_partner_verify', 'boolean'],
            ['user_partner_verify', 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->scenario == 'create') {
                $this->user_partner_rating = 0;
            }

            return true;
        }

        return false;
    }
}
