<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 7/13/18
 * Time: 4:54 PM
 */

namespace execut\images\widgets;


use execut\files\models\File;
use execut\yii\jui\Widget;
use yii\bootstrap\Carousel;

class Gallery extends Widget
{
    public $modelClass = File::class;
    public $dataAttribute = 'data';
    public function run()
    {
        parent::run();
        $items = [];
        $modelClass = $this->modelClass;
        $images = $modelClass::find()->limit(10)->all();
        foreach ($images as $image) {
            $items[] = [
                'content' => '<img src="' . $image->getUrl($this->dataAttribute) . '"/>',
            ];
        }
        echo Carousel::widget([
            'options' => [
                'class' => 'slide',
            ],
            'items' => $items,
            'controls' => [
                '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>',
                '<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>',
            ],
        ]);
    }
}