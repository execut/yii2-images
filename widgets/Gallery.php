<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 7/13/18
 * Time: 4:54 PM
 */

namespace execut\images\widgets;


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
            $img = Html::img($image->getUrl($this->thumbnailAttribute), [
                'class' => 'product-image',
//                    'alt' => $image->getAlt(),
            ]);

            if ($this->dataAttribute !== $this->thumbnailAttribute) {
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
            echo $items[0]['content'];
        } else {
            echo Carousel::widget([
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
    }
}

https://detalika.ru/zapchasti/poisk?searchType=&searchType=code&article=N%20%2090813202&brandId=1046&w9_input=&goodsTypeId=&goodsTypeId%5B%5D=2&goodsTypeId%5B%5D=1&shopId=&minPrice=&maxPrice=&deliveryPartnerSelectedId=&isTriplePass=0&isAvailable=0