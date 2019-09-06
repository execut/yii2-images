<?php
namespace execut\images\console;


use execut\crudFields\fields\Field;
use yii\console\Controller;

class ConsoleController extends Controller
{
    public function actionIndex() {
        $filesModule = $this->module->getFilesModule();
        $modelClass = $filesModule->modelClass;
        $q = $modelClass::find()->withoutData([
            $filesModule->getColumnName('data')
        ]);
        foreach ($q->batch() as $files) {
            foreach ($files as $file) {
                $file->scenario = Field::SCENARIO_FORM;
                $file->save();
                $this->stdout($file . ' is resaved' . "\n");
            }
        }
    }
}