<?php
/**
 */

namespace execut\images\crudFields;

use execut\crudFields\fields\HasOneSelect2;
use execut\files\models\File;

class Plugin extends \execut\crudFields\Plugin
{
    public function getFields() {
        return [
            [
                'attribute' => 'alt',
            ],
            [
                'attribute' => 'title',
            ],
        ];
    }
}