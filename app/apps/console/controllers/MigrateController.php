<?php

namespace console\controllers;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Migrate From Multiple Path
 * @package console\controllers
 */
class MigrateController extends \yii\console\controllers\MigrateController
{
    /**
     * @var array
     */
    public $migrationLookup = [];

    /**
     * @var array
     */
    private $_migrationFiles;

    /**
     * @return array
     */
    protected function getMigrationFiles()
    {
        if ($this->_migrationFiles === null) {
            $this->_migrationFiles = [];
            $directories = ArrayHelper::merge($this->migrationLookup, (array)$this->migrationPath);
            $extraPath = ArrayHelper::getValue(Yii::$app->params, 'yii.migrations');

            if (!empty($extraPath)) {
                $directories = array_merge((array)$extraPath, $directories);
            }

            $directories = array_unique($directories);

            foreach ($directories as $dir) {
                $dir = Yii::getAlias($dir, false);
                if ($dir && is_dir($dir)) {
                    $handle = opendir($dir);
                    while (($file = readdir($handle)) !== false) {
                        if ($file === '.' || $file === '..') {
                            continue;
                        }
                        $path = $dir . DIRECTORY_SEPARATOR . $file;
                        if (preg_match('/^(m(\d{6}_\d{6})_.*?)\.php$/', $file, $matches) && is_file($path)) {
                            $this->_migrationFiles[$matches[1]] = $path;
                        }
                    }
                    closedir($handle);
                }
            }

            ksort($this->_migrationFiles);
        }

        return $this->_migrationFiles;
    }

    /**
     * @param string $class
     * @return \yii\db\Migration
     */
    protected function createMigration($class)
    {
        $file = $this->getMigrationFiles()[$class];
        require_once($file);

        return new $class(['db' => $this->db]);
    }

    /**
     * @return array
     */
    protected function getNewMigrations()
    {
        $applied = [];
        foreach ($this->getMigrationHistory(null) as $version => $time) {
            $applied[substr($version, 1, 13)] = true;
        }

        $migrations = [];
        foreach ($this->getMigrationFiles() as $version => $path) {
            if (!isset($applied[substr($version, 1, 13)])) {
                $migrations[] = $version;
            }
        }

        return $migrations;
    }
}
