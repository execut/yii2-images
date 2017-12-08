<?php
/**
 */

namespace execut\images;


use execut\yii\migration\Table;
use yii\base\BaseObject;

class MigrationHelper extends BaseObject
{
    /**
     * @var Table
     */
    public $table = null;
    public $sizeName = null;
    public function attach() {
        $this->table->addColumns([
            $this->sizeName => 'bytea',
        ]);
    }
}