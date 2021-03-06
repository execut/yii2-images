<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 7/13/18
 * Time: 4:54 PM
 */

namespace execut\images\widgets;


use execut\files\FormatConverter;
use execut\files\models\File;
use execut\yii\jui\Widget;
use yii\bootstrap\Carousel;
use yii\helpers\Html;

class Gallery extends Widget
{
    public $images = [];
    public $dataAttribute = 'data';
    public $thumbnailAttribute = 'data';
    public $description = null;
    public function run()
    {
        parent::run();
        $items = [];
        foreach ($this->images as $image) {
            /**
             * @var File $image
             */
            $img = '<picture>
    <source type="image/webp" srcset="' . $image->getUrl($this->thumbnailAttribute, FormatConverter::FORMAT_WEBP) . '">
    <source type="image/jp2" srcset="' . $image->getUrl($this->thumbnailAttribute, FormatConverter::FORMAT_JPEG2000) . '">
    <source type="image/jxr" srcset="' . $image->getUrl($this->thumbnailAttribute, FormatConverter::FORMAT_JPEG_XR) . '">
    ' . Html::img($image->getUrl($this->thumbnailAttribute), [
                    'class' => 'product-image',
//                    'alt' => $image->getAlt(),
                ]) . '
  </picture>';

            $module = \yii::$app->getModule($image->getImagesModuleId());
            if ($url = $module->getImageTargetUrl($image)) {
                $img = Html::a($img, $url, [
                    'title' => $image->getTitle(),
                    'data-pjax' => '0',
                    'target' => '_blank',
                ]);
            } else if ($this->dataAttribute !== $this->thumbnailAttribute) {
                $img = Html::a($img, $image->getUrl($this->dataAttribute), [
                    'title' => $image->getTitle(),
                    'data-pjax' => '0',
                    'target' => '_blank',
                    'rel' => 'fancybox',
                ]);
            }

            $items[] = [
                'content' => $img
            ];

            if ($this->thumbnailAttribute !== $this->dataAttribute) {
                echo \newerton\fancybox\FancyBox::widget([
                    'target' => 'a[rel=fancybox]',
                    'mouse' => true,
                    'config' => [
                        'maxWidth' => '100%',
                        'maxHeight' => '100%',
                        'playSpeed' => 7000,
                        'padding' => 0,
                        'fitToView' => true,
                        //                'width' => '70%',
                        //                'height' => '70%',
                        'autoSize' => false,
                        'closeClick' => false,
                        'openEffect' => 'elastic',
                        'closeEffect' => 'elastic',
                        'prevEffect' => 'elastic',
                        'nextEffect' => 'elastic',
                        'closeBtn' => true,
                        'openOpacity' => true,
                        'helpers' => [
                            'title' => ['type' => 'float'],
                            'buttons' => [],
                        ],
                    ],
                ]);
            }

            echo '<span itemprop="image" itemscope itemtype="http://schema.org/ImageObject" contentUrl="' . $image->getUrl($this->dataAttribute) . '">';
            if ($title = $image->getTitle()) {
                echo '<meta itemprop="name" content="' . $title . '">';
            }

            echo '<span itemprop="contentUrl" src="' . $image->getUrl($this->dataAttribute) . '" content="' . $image->getUrl($this->dataAttribute) . '"></span>
                    <meta itemprop="caption description" content="' . $this->description . '">
                    <meta itemprop="datePublished" content="' . str_replace(' ', 'T', $image->created) . '"/>';
            if ($width = $image->getWidth($this->dataAttribute)) {
                echo '<meta itemprop="width" content="' . $width . '">';
            }

            if ($height = $image->getHeight($this->dataAttribute)) {
                echo '<meta itemprop="height" content="' . $height . '">';
            }

            if ($this->thumbnailAttribute !== $this->dataAttribute) {
                echo '<span itemprop="thumbnail" itemscope itemtype="http://schema.org/ImageObject">';
                if ($title = $image->getTitle()) {
                    echo '<meta itemprop="name" content="' . $title . ' превью">';
                }

                echo '<span itemprop="contentUrl" src="' . $image->getUrl($this->thumbnailAttribute) . '" content="' . $image->getUrl($this->thumbnailAttribute) . '"></span>
                            <meta itemprop="caption description" content="' . $this->description . '">';
                if ($width = $image->getWidth($this->thumbnailAttribute)) {
                    echo '<meta itemprop="width" content="' . $width . '">';
                }

                if ($height = $image->getHeight($this->thumbnailAttribute)) {
                    echo '<meta itemprop="height" content="' . $height . '">';
                }

                echo '</span>';
            }

            echo '</span>';
        }

        if (count($items) === 1) {
            $result = $items[0]['content'];
        } else {
            $result = Carousel::widget([
                'options' => [
                    'class' => 'slide',
                ],
                'items' => $items,
                'controls' => [
                    '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>',
                    '<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>',
                ],
            ]);
        }

        $this->_registerBundle();

        return $this->_renderContainer($result);
    }
}
