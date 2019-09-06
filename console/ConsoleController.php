<?php
namespace execut\images\console;


use execut\crudFields\fields\Field;
use yii\console\Controller;

class ConsoleController extends Controller
{
    public function actionIndex($fromId = null) {
        $filesModule = $this->module->getFilesModule();
        $modelClass = $filesModule->modelClass;
        $q = $modelClass::find()->withoutData([
            $filesModule->getColumnName('data')
        ])->orderBy('id ASC');
        if ($fromId !== null) {
            $q->andWhere('id>' . $fromId);
        }
        
        foreach ($q->batch() as $files) {
            foreach ($files as $file) {
                $file->scenario = Field::SCENARIO_FORM;
                $file->save();
                $this->stdout($file . ' is resaved' . "\n");
            }
        }
    }
}