<?php

use yii\db\Migration;
use yii\db\Schema;

class m150101_232651_total_rbac_ss extends Migration
{
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        # Сео спецификации
        #=============================================================
        $permission = $auth->createPermission('seo-specifications-view');
        $permission->description = '<b>Разрешить просмотр сео-спецификий</b><p>Разрешение на просмотр любых сео-спецификий.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('seo-specifications-update');
        $permission->description = '<b>Разрешить редактировать сео-спецификии</b><p>Разрешение на редактирование любых сео-спецификий.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('seo-specifications-delete');
        $permission->description = '<b>Разрешить удалять сео-спецификии</b><p>Разрешение на удаление любых сео-спецификий.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('seo-specifications-create');
        $permission->description = '<b>Разрешить создавать сео-спецификии</b><p>Разрешение на создание любых сео-спецификий.</p>';
        $auth->add($permission);
    }

    public function safeDown()
    {
        return false;
    }
}
