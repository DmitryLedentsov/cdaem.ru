<?php

use yii\db\Migration;

/**
 * Class m160324_152349_seo_specifications
 */
class m160324_152349_seo_specifications extends Migration
{
    /**
     * Таблица сео спецификации, мета теги к любым страницам
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{seo_specifications}}', [
            'id' => $this->primaryKey() . ' COMMENT "№"',
            'city' => $this->string(128) . ' COMMENT "Город"',
            'url' => $this->string() . ' COMMENT "Url"',
            'title' => $this->string() . ' COMMENT "Title"',
            'description' => $this->string() . ' COMMENT "Description"',
            'keywords' => $this->string() . ' COMMENT "Keywords"',
            'date_create' => $this->dateTime() . ' COMMENT "Дата создания"',
            'date_update' => $this->dateTime() . ' COMMENT "Дата редактирования"',
        ], $tableOptions);

        $this->createIndex('idx_seo_specifications_city', '{{seo_specifications}}', 'city');
        $this->createIndex('idx_seo_specifications_url', '{{seo_specifications}}', 'url');
    }

    public function safeDown()
    {
        $this->dropTable('{{seo_specifications}}');
    }
}
