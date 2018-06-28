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
    public $_defaultDepends = [
        'modules' => [
            'images' => [
                'class' => Module::class,
            ],
        ],
        'bootstrap' => [
            'images',
        ],
    ];

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

    protected function attachToModules() {
        $imagesModule = \yii::$app->getModule('images');
        $models = $imagesModule->getAttachedModels();
        $sizes = $imagesModule->getSizes();
        $tables = [];
        foreach ($models as $model) {
            $tables[] = $model::tableName();
        }

        $attacher = new Attacher([
            'tables' => $tables,
            'sizes' => $sizes,
        ]);

        $attacher->safeUp();
    }
}