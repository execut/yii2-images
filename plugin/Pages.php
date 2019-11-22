<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 11/22/19
 * Time: 9:41 AM
 */

namespace execut\images\plugin;


use execut\files\models\FileBase;
use execut\images\Plugin;
use yii\helpers\Url;

class Pages implements Plugin
{
    public function getSizes(FileBase $file = null)
    {
    }

    public function getImageTargetUrl(FileBase $image)
    {
        if (!$image->pages_page_id) {
            return;
        }

        return Url::to([
            '/pages/frontend/index',
            'id' => $image->pages_page_id,
        ]);
    }
}