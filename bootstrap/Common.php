<?php
/**
 */

namespace execut\images\bootstrap;


use execut\images\Attacher;
use execut\images\Module;
use execut\yii\Bootstrap;
use Imagine\Image\ImageInterface;

class Common extends Bootstrap
{
    public $isBootstrapI18n = true;
    public $moduleId = 'images';
    public function getDefaultDepends()
    {
        return [
            'modules' => [
                $this->moduleId => [
                    'class' => Module::class,
                ],
            ],
            'bootstrap' => [
                $this->moduleId,
            ],
        ];
    }
}