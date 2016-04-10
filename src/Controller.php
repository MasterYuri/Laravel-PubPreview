<?php

namespace MasterYuri\PubPreview;

use Illuminate\Http\Request;

use Log;
use App\Http\Requests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Filesystem\Filesystem;

use Intervention\Image\ImageManagerStatic as Image;

class Controller extends BaseController
{
    public function error($str)
    {
        Log::error($str);
    }

    public static function cfg($str)
    {
        return config('pub-preview.' . $str);
    }
    
    public static function previewUrl($path, $width, $height, $mode = "scale")
    {
        if ($path == "") $path = basename(self::cfg('no_image_path')); // Чтобы было хоть какое-то имя.
        
        $ret = route("pub_preview_url", ['path' => $path, 'width' => $width, 'height' => $height, 'mode' => $mode]);//action(self::class . '@getPreview', ['path' => $path, 'width' => $width, 'height' => $height, 'mode' => $mode]);
        //dd($ret);
        return $ret;
    }
    
    public function getPreview(Request $request, $width, $height, $mode, $path)
    {
        $fs = new Filesystem();
        $fullPath = NULL;

        $path = str_replace("..", "", $path); // protection
        $path = str_replace("~",  "", $path); // protection

        $path = str_replace("\\", "/", $path);
        $path = ltrim($path, "/");
        
        foreach ($this->cfg('directories') as $directory)
        {
            $directory = str_replace("\\", "/", $directory);
            $directory = rtrim($directory, "/");

            $p = "{$directory}/{$path}";
            if ($fs->isFile($p)) 
            {
                $fullPath = $p;
                break;
            }
        }

        $noImgPath = "_" . md5(__DIR__) . "_" . pathinfo($this->cfg('no_image_path'), PATHINFO_FILENAME) . "." . pathinfo($fullPath, PATHINFO_EXTENSION);
        
        if (!$fullPath)
        {
            $this->error("File doesn't exists");
            $fullPath = $this->cfg('no_image_path');
            $path = $noImgPath;
        }
        //if (!$fs->is_readable($fullPath))
        //{
        //    $this->error("Can't access to file");
        //    $fullPath = $this->cfg('no_image_path');
        //    $path = $noImgPath;
        //}
        
        $width  = intval($width);
        $height = intval($height);
        
        if ($width < 1)
        {
            abort(400, "Incorrect width");
        }
        if ($height < 1)
        {
            abort(400, "Incorrect height");
        }
        if (!in_array($mode, ["fitin", "fitout", "scale"]))
        {
            abort(400, "Incorrect mode");
        }

        $allowedList = $this->cfg('allowed');
        if (count($allowedList))
        {
            $allow = false;
            foreach ($allowedList as $a)
            {
                if (intval(@$a[0]) == $width && intval(@$a[1]) == $height && @$a[2] == $mode)
                {
                    $allow = true;
                    break;
                }
            }
            if (!$allow)
            {
                abort(400, "Resolution '{$width}x{$height}' for mode '{$mode}' is not allowed");
            }
        }
        
        //**
        
        $width  = min($width,  $this->cfg('max.width'));
        $height = min($height, $this->cfg('max.heigth'));
        
        if ($fullPath == $this->cfg('no_image_path') && $mode == "fitout")
        {
            // Делаем чуть симпотичнее, хотя необходимости нет.
            $mode = "fitin";
        }
        
        //return response()->file($fullPath);
        
        $finalPath = $this->cfg('store') . "{$width}/{$height}/{$mode}/{$path}";
        if (!$fs->isFile($finalPath))
        {
            $im = Image::make($fullPath);
            
            // http://zmicron.org/tpl/components/get_img/mode.png
            if ($mode == "scale")
            {
                $im->resize($width, $height, function ($c) 
                {
                    $c->aspectRatio();
                    $c->upsize();
                });
            }
            elseif ($mode == "fitout")
            {
                $im->fit($width, $height, function ($c) 
                {
                    $c->aspectRatio();
                    $c->upsize();
                });

                $canvas = Image::canvas($width, $height);
                $canvas->fill($this->cfg('fill_color'));

                $canvas->insert($im, 'center');
                $im = $canvas;
            }
            elseif ($mode == "fitin")
            {
                $im->resize($width, $height, function ($c) 
                {
                    $c->aspectRatio();
                    $c->upsize();
                });

                $canvas = Image::canvas($width, $height);
                $canvas->fill($this->cfg('fill_color'));

                $canvas->insert($im, 'center');
                $im = $canvas;
            }
            
            @$fs->makeDirectory(dirname($finalPath), 0755, true);
            $im->save($finalPath);
        }

        return response()->file($finalPath, $this->cfg('headers'));
        //return response()->download($finalPath, basename($finalPath), $this->cfg('headers'));
    }
}
