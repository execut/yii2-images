<?php
/**
 */

namespace execut\images;


interface Plugin
{
    public function getSizes($file);

    public function getAttachedModels();
}