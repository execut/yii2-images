<?php
/**
 */

namespace execut\images;


use execut\dependencies\PluginBehavior;
use execut\files\models\File;
use Imagine\Image\ManipulatorInterface;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\db\ActiveRecord;
use yii\i18n\PhpMessageSource;
use yii\imagine\BaseImage;
use yii\imagine\Image;
use yii\web\Application;

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
        $this->attachToModels();

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

    public function attachToModels() {
        foreach ($this->getAttachedModels() as $model) {
            Event::on($model, ActiveRecord::EVENT_BEFORE_INSERT, function ($e) {
                $file = $e->sender;
                $this->onBeforeFileSave($file);
            });
            Event::on($model, ActiveRecord::EVENT_BEFORE_UPDATE, function ($e) {
                $file = $e->sender;
                $this->onBeforeFileSave($file);
            });
        }
    }

    public function onBeforeFileSave($file) {
        $sizes = $this->getSizes($file);
        $dataAttribute = $this->dataAttribute;
        $data = $file->$dataAttribute;
        if (!$data) {
            return;
        }

        if (is_string($data)) {
            $tempFile = tempnam('/tmp', 'temp_');
            file_put_contents($tempFile, $data);
            $data = fopen($tempFile, 'r+');
        }

        $sourceImage = Image::getImagine()->read($data);

        foreach ($sizes as $sizeName => $size) {
            $thumbnailAttributeName = $sizeName;
            if (!empty($size['width'])) {
                $width = $size['width'];
                if ($width < 0) {
                    $originalWidgth = $sourceImage->getSize()->getWidth();
                    if (-$originalWidgth < $width * 4) {
                        $width = $sourceImage->getSize()->getWidth() + $width;
                    } else {
                        $width = $originalWidgth;
                    }
                }
            } else {
                $width = null;
            }

            if (!empty($size['height'])) {
                $height = $size['height'];
                if ($height < 0) {
                    $originalHeight = $sourceImage->getSize()->getHeight();
                    if (-$originalHeight < $height * 4) {
                        $height = $sourceImage->getSize()->getHeight() + $height;
                    } else {
                        $height = $originalHeight;
                    }
                }
            } else {
                $height = null;
            }

            if (!empty($size['mode'])) {
                $mode = $size['mode'];
            } else {
                $mode = ImageInterface::THUMBNAIL_INSET;
            }

            BaseImage::$thumbnailBackgroundAlpha = 0;
            $image = Image::thumbnail($sourceImage, $width, $height, $mode);

            if (!empty($size['watermark'])) {
                $watermark = fopen($size['watermark'], 'r+');
                $watermark = Image::thumbnail($watermark, $image->getSize()->getWidth(), $image->getSize()->getHeight(), ManipulatorInterface::THUMBNAIL_OUTBOUND);
                $watermark = Image::crop($watermark, $image->getSize()->getWidth(), $image->getSize()->getHeight());

                $image = Image::watermark($image, $watermark);
            }

            $fileName = tempnam(sys_get_temp_dir(), 'test');
            $extensionAttribute = $this->extensionAttribute;
            $image->save($fileName, [
                'format' => $file->$extensionAttribute,
            ]);

            $thumbData = fopen($fileName, 'r+');
            $file->$thumbnailAttributeName = $thumbData;
        }
    }
}