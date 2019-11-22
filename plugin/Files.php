<?php
/**
 */

namespace execut\images\plugin;


use execut\files\models\File;
use execut\files\models\FileBase;
use execut\images\Module;
use execut\images\Plugin;
use Imagine\Image\ImageInterface;
use yii\base\Event;
use yii\imagine\BaseImage;
use yii\imagine\Image;


class Files implements Plugin
{
    public function __construct()
    {
        Event::on(File::class, File::EVENT_BEFORE_UPDATE, function ($e) {
            $file = $e->sender;
            $this->onBeforeFileSave($file, 'data');
        });
        Event::on(File::class, File::EVENT_BEFORE_INSERT, function ($e) {
            $file = $e->sender;
            $this->onBeforeFileSave($file, 'data');
        });
    }

    public function onBeforeFileSave($file, $dataAttribute)
    {
        $sizes = $this->getSizes();
        foreach ($sizes as $sizeName => $size) {
            $thumbnailAttributeName = $sizeName;
            $attributes = $file->getAttributes();
            $data = $file->$dataAttribute;
            if (is_string($data)) {
                $tempFile = tempnam('/tmp', 'temp_');
                file_put_contents($tempFile, $data);
                $data = fopen($tempFile, 'r+');
            }

            if (!empty($size['width'])) {
                $width = $size['width'];
            } else {
                $width = null;
            }

            if (!empty($size['height'])) {
                $height = $size['height'];
            } else {
                $height = null;
            }

            if (!empty($size['mode'])) {
                $mode = $size['mode'];
            } else {
                $mode = ImageInterface::THUMBNAIL_INSET;
            }

            BaseImage::$thumbnailBackgroundAlpha = 0;
            $image = Image::thumbnail($data, $width, $height, $mode);
            $fileName = tempnam(sys_get_temp_dir(), 'test');
            $image->save($fileName, [
                'format' => $file->extension,
            ]);

            $data = fopen($fileName, 'r+');
            $file->$thumbnailAttributeName = $data;
            $this->makeFormatsForSize($file, $thumbnailAttributeName, $data);

            if (!is_string($file->$dataAttribute)) {
                rewind($file->$dataAttribute);
            }
        }
    }

    public function getSizes(FileBase $file = NULL)
    {
        return [
            'size_sm' => [
                'width' => 200,
                'mode' => ImageInterface::THUMBNAIL_INSET,
            ],
            'size_m' => [
                'width' => 375,
                'mode' => ImageInterface::THUMBNAIL_INSET,
            ],
            'size_s' => [
                'width' => 67,
                'mode' => ImageInterface::THUMBNAIL_INSET,
            ],
        ];
    }

    public function getImageTargetUrl(FileBase $image)
    {
    }

    /**
     * @param $file
     * @param $thumbnailAttributeName
     * @param bool $data
     * @param $fileName
     */
    protected function makeFormatsForSize($file, $thumbnailAttributeName, $data)
    {
        foreach ($this->getFormats() as $format) {
            $formatAttributeName = Module::getFormatType();
            $fileNameNew = tempnam(sys_get_temp_dir(), 'test');
            imagewebp($data, $fileNameNew);
            $file->$formatAttributeName = fopen($fileName, 'r+');
        }
    }
}