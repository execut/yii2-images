<?php
/**
 */

namespace execut\images;


use execut\dependencies\PluginBehavior;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\db\ActiveRecord;

class Module extends \yii\base\Module implements Plugin
{
    public $dataAttribute = 'data';
    public $extensionAttribute = 'extension';
    public $filesModuleId = 'files';

    public function behaviors()
    {
        return [
            [
                'class' => PluginBehavior::class,
                'pluginInterface' => Plugin::class,
            ],
        ];
    }

    public function getSizes($file = null) {
        return $this->getPluginsResults(__FUNCTION__, false, func_get_args());
    }
}