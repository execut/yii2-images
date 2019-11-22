<?php
/**
 */

namespace execut\images;


use execut\files\models\FileBase;

interface Plugin
{
    public function getSizes(FileBase $file = null);
    public function getImageTargetUrl(FileBase $image);
}