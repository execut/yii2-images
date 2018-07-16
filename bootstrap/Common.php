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

    public function bootstrap($app)
    {
        parent::bootstrap($app);
        $this->registerTranslations($app);
    }

    public function registerTranslations($app) {
        $app->i18n->translations['execut/images'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@execut/yii2-images/messages',
            'fileMap' => [
                'execut/images' => 'images.php',
            ],
        ];
    }
}