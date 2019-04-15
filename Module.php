<?php
/**
 */

namespace execut\images;


use execut\dependencies\PluginBehavior;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\db\ActiveRecord;

class Module extends \yii\base\Module implements Plugin, BootstrapInterface
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

    public function getAttachedModels() {
        return $this->getPluginsResults(__FUNCTION__);
    }

    public function bootstrap($app)
    {
        $this->attachToModules();
    }

    protected function attachToModules() {
        $models = $this->getAttachedModels();
        $sizes = $this->getSizes();
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