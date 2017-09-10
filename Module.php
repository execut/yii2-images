<?php
/**
 */

namespace execut\images;


use execut\dependencies\PluginBehavior;
use yii\i18n\PhpMessageSource;
use yii\web\Application;

class Module extends \yii\base\Module implements Plugin
{
    public function behaviors()
    {
        return [
            [
                'class' => PluginBehavior::class,
                'pluginInterface' => Plugin::class,
            ],
        ];
    }

    public function getSizes() {
        return $this->getPluginsResults(__FUNCTION__);
    }

    public function getAttachedModels() {
        return $this->getPluginsResults(__FUNCTION__);
    }
}