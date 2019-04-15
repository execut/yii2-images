<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 4/15/19
 * Time: 11:36 AM
 */

namespace execut\images\behaviors;


use execut\images\Module;
use Imagine\Image\ImageInterface;
use execut\files\models\File;
use Imagine\Image\ManipulatorInterface;
use yii\base\Exception;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\i18n\PhpMessageSource;
use yii\imagine\BaseImage;
use yii\imagine\Image;
use yii\web\Application;
class Behavior extends \yii\base\Behavior
{

    /**
     * @return Module
     */
    public function getModule() {
        return \yii::$app->getModule($this->owner->getImagesModuleId());
    }

    public function getDataAttribute() {
        return $this->getModule()->dataAttribute;
    }

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_UPDATE => 'onBeforeFileSave',
            ActiveRecord::EVENT_BEFORE_INSERT => 'onBeforeFileSave',
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'onBeforeValidate'
        ];
    }

    public function onBeforeValidate() {
        $md5Attribute = \yii::$app->getModule($this->owner->getModuleId())->getColumnName('file_md5');
        $nameAttribute = \yii::$app->getModule($this->owner->getModuleId())->getColumnName('name');
        $this->owner->$md5Attribute = null;
        $this->owner->$nameAttribute = null;
    }

    public function onBeforeFileSave() {
        $file = $this->owner;
        $sizes = $this->getSizes();
        $dataAttribute = $this->getDataAttribute();
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
        $box = $sourceImage->getSize();
        $file->width = $box->getWidth();
        $file->height = $box->getHeight();
        foreach ($sizes as $sizeName => $size) {
            if (!empty($size['width'])) {
                $width = $size['width'];
                if ($width < 0) {
                    $originalWidgth = $box->getWidth();
                    if ($originalWidgth > (-$width) * 2) {
                        $width = $originalWidgth + $width;
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
                    $originalHeight = $box->getHeight();
                    if ($originalHeight > (-$height) * 2) {
                        $height = $originalHeight + $height;
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
                $watermark = Image::resize($watermark, $image->getSize()->getWidth() * 2, $image->getSize()->getHeight() * 2, true, true);
                $watermark = Image::thumbnail($watermark, $image->getSize()->getWidth(), $image->getSize()->getHeight(), ManipulatorInterface::THUMBNAIL_OUTBOUND);
                $watermark = Image::crop($watermark, $image->getSize()->getWidth(), $image->getSize()->getHeight());

                $image = Image::watermark($image, $watermark);
            }

            $fileName = tempnam(sys_get_temp_dir(), 'test');
            $extensionAttribute = $this->getModule()->extensionAttribute;
            $image->save($fileName, [
                'format' => $file->$extensionAttribute,
            ]);

            $thumbData = fopen($fileName, 'r+');
            $file->$sizeName = $thumbData;
        }
    }

    public function getWidth($dataAttribute = null) {
        return $this->getSide('width', $dataAttribute);
    }

    public function getHeight($dataAttribute = null) {
        return $this->getSide('height', $dataAttribute);
    }

    protected function getSide($side, $dataAttribute = null) {
        if ($dataAttribute === null) {
            $dataAttribute = $this->getDataAttribute();
        }

        if ($dataAttribute === $this->getDataAttribute()) {
            return $this->owner->$side;
        }

        $sizes = $this->getSizes();
        if (empty($sizes[$dataAttribute])) {
            throw new Exception('Data attribute ' . $dataAttribute . ' is not configured');
        }

        $size = $sizes[$dataAttribute];
        if (!empty($size[$side])) {
            $width = $size[$side];
            if ($width < 0) {
                $originalWidgth = $this->owner->$side;
                if (-$originalWidgth < $width * 4) {
                    $width = $this->owner->$side + $width;
                } else {
                    $width = $originalWidgth;
                }
            }
        } else {
            $width = null;
        }

        return $width;
    }

    /**
     * @param $file
     * @return mixed
     */
    protected function getSizes()
    {
        return $this->getModule()->getSizes($this->owner);
    }
}