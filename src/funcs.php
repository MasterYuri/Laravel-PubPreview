<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Debug\Dumper;
use Illuminate\Contracts\Support\Htmlable;

use MasterYuri\PubPreview\Controller;

if (!function_exists('pub_preview')) 
{
    function pub_preview($path, $width, $height, $mode = "scale")
    {
        return Controller::previewUrl($path, $width, $height, $mode);
    }
}
