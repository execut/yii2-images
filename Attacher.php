<?php
/**
 */

namespace execut\images;


use execut\yii\migration\Inverter;
use execut\yii\migration\Migration;

class Attacher extends Migration
{
    public $tables = [];
    public $sizes = [];
    public function initInverter(Inverter $i) {
        foreach ($this->tables as $table) {
            $cache = \yii::$app->cache;
            $attributes = [
                'title',
                'alt'
            ];

            foreach ($attributes as $attribute) {
                $cacheKey = __CLASS__ . '_' . $table . '_' . $attribute;
                if ($cache->get($cacheKey)) {
                    continue;
                }
                $tableSchema = $this->db->getTableSchema($table);
                if (!$tableSchema) {
                    continue 2;
                }

                if (!$tableSchema->getColumn($attribute)) {
                    $i->table($table)->addColumn($attribute, $this->string());
                }

                $cache->set($cacheKey, 1);
            }

            foreach ($this->sizes as $sizeName => $sizeParams) {
                $cacheKey = __CLASS__ . '_' . $table . '_' . $sizeName;
                if ($cache->get($cacheKey)) {
                    continue;
                }

                $tableSchema = $this->db->getTableSchema($table);
                if (!$tableSchema) {
                    continue 2;
                }
                $isAttached = $tableSchema->getColumn($sizeName);
                if (!$isAttached) {
                    $helper = new MigrationHelper([
                        'table' => $i->table($table),
                        'sizeName' => $sizeName,
                    ]);
                    $helper->attach();
                }

                $cache->set($cacheKey, 1);
            }
        }
    }
}