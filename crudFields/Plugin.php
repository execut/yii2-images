<?php
/**
 */

namespace execut\images\crudFields;

use execut\crudFields\fields\HasOneSelect2;
use execut\files\models\File;
use yii\helpers\Html;
use yii\helpers\UnsetArrayValue;

class Plugin extends \execut\crudFields\Plugin
{
    public $previewDataAttribute = 'img_100';
    public $moduleId = 'images';
    public function getFields() {
//        $thumbAttributes = array_keys(\yii::$app->getModule('images')->getSizes());
//        $result = [];
//        foreach ($thumbAttributes as $thumbAttribute) {
//            $result[$thumbAttribute] = [
//                'attribute' => $thumbAttribute,
//            ];
//        }
//
//        return $result;
//

        $value = function ($row) {
            $module = \yii::$app->getModule($this->moduleId);
            $extensionAttribute = $module->extensionAttribute;

            return Html::a(Html::img(['/' . $module->filesModuleId . '/frontend/index', 'id' => $row->id, 'extension' => strtolower($row->$extensionAttribute), 'dataAttribute' => $this->previewDataAttribute]), [
                '/' . $module->filesModuleId . '/frontend/index',
                'id' => $row->id,
                'extension' => strtolower($row->$extensionAttribute),
            ]);
        };
        return [
            'preview' => [
                'module' => 'images',
                'attribute' => 'preview',
                'scope' => false,
                'field' => [
                    'format' => 'raw',
                    'displayOnly' => true,
                    'value' => function ($form, $widget) use ($value) {
                        return $value($widget->model);
                    },
                ],
                'column' => [
                    'filter' => false,
                    'format' => 'raw',
                    'value' => $value,
                ],
            ],
//            [
//                'attribute' => 'alt',
//            ],
//            [
//                'attribute' => 'title',
//            ],
        ];
    }
}