<?php

use yii\db\Migration;
use yii\db\Schema;

class m150101_232652_total_rbac_mc extends Migration
{
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $permission = $auth->createPermission('agency-advertisement-multi-control');
        $permission->description = '<b>Разрешить массовое управление рекламными объявлениями агенства</b><p>Разрешение на любое действие из массового управления рекламными объявлениями агенства.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('agency-reservation-multi-control');
        $permission->description = '<b>Разрешить массовое управление заявками на резервацию объявлений агенства</b><p>Разрешение на любое действие из массового управления заявками на резервацию объявлений агенства.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('agency-special-advert-multi-control');
        $permission->description = '<b>Разрешить массовое управление спецпредложениями агенства</b><p>Разрешение на любое действие из массового управления спецпредложений в разделе агенства.</p>';
        $auth->add($permission);
    }

    public function safeDown()
    {
        return false;
    }
}
