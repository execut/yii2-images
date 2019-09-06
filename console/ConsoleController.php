<?php
namespace execut\images\console;


use execut\crudFields\fields\Field;
use yii\console\Controller;
use yii\db\ActiveQuery;

class ConsoleController extends Controller
{
    public function actionIndex($fromId = null) {
        $filesModule = $this->module->getFilesModule();
        $modelClass = $filesModule->modelClass;
        /**
         * @var ActiveQuery $q
         */
        $q = $modelClass::find()->withoutData([
            $filesModule->getColumnName('data')
        ])->orderBy('id ASC');
        if ($fromId !== null) {
            $q->andWhere('id>' . $fromId);
        }

        $this->stderr('Getting count images' . "\n");
        $totalCount = $q->count();
        $this->stderr('Start resaving ' . $totalCount . " images\n");
        $currentCount = 0;
        foreach ($q->batch(1) as $files) {
            foreach ($files as $file) {
                $file->scenario = Field::SCENARIO_FORM;
                $file->save();
                $this->stdout($file . ' is resaved' . "\n");
                $currentCount++;
                $this->stderr('Saved ' . $currentCount . ' from ' . $totalCount . "\n");
            }
        }
    }
}