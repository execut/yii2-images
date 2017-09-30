<?php
/**
 */

namespace execut\images;


use execut\yii\migration\Inverter;
use execut\yii\migration\Migration;

class Attacher extends \execut\yii\migration\Attacher
{
    public $tables = [];
    public $sizes = [];
    protected $attributes = [
        'title',
        'alt'
    ];

    protected function getVariations () {
        return ['tables', 'sizes', 'attributes'];
    }

    public function initInverter(Inverter $i) {
        foreach ($this->tables as $table) {
            foreach ($this->attributes as $attribute) {
                $tableSchema = $this->db->getTableSchema($table);
                if (!$tableSchema) {
                    continue 2;
                }

                if (!$tableSchema->getColumn($attribute)) {
                    $i->table($table)->addColumn($attribute, $this->string());
                }
            }

            foreach ($this->sizes as $sizeName => $sizeParams) {
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
            }
        }
    }
}