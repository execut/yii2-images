<?php
/**
 */

namespace execut\images;


interface Plugin
{
    public function getSizes($file = null);

    public function getAttachedModels();
}