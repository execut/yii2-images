<?php
namespace execut\images\console;


use execut\crudFields\fields\Field;
use yii\base\Exception;
use yii\console\Controller;
use yii\db\ActiveQuery;

class ConsoleController extends Controller
{
    public function actionIndex($fromId = null, $withoutAttribute = null) {
        $filesModule = $this->module->getFilesModule();
        $modelClass = $filesModule->modelClass;
        /**
         * @var ActiveQuery $q
         */
        $q = $modelClass::find()->orderBy('id ASC');
        if (!empty($fromId)) {
            $q->andWhere('id>' . $fromId);
        }

        if ($withoutAttribute) {
            $q->andWhere($withoutAttribute . ' is null');
        }

        $this->stderr('Getting count images' . "\n");
        $totalCount = $q->count();
        $this->stderr('Start resaving ' . $totalCount . " images\n");
        $currentCount = 0;
        $ids = (clone $q)->select('id')->column();
        foreach ($ids as $id) {
            $file = $modelClass::findOne($id);
            $file->scenario = Field::SCENARIO_FORM;
            if (!$file->save()) {
                echo $file->file_md5 . "\n";
                $this->stderr($file  . ' resave errors: ' . var_export($file->errors, true) . "\n");
            }

            $this->stdout($file . ' is resaved' . "\n");
            $currentCount++;
            $this->stderr('Saved ' . $currentCount . ' from ' . $totalCount . "\n");
        }
    }
}