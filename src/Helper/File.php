<?php

namespace Kang\Libs\Helper;

class File{
    public static function unlink($path){
        if(file_exists($path)){
            $dn = opendir($path);
            while($file = readdir($dn)){
                if($file == '.' || $file == '..'){
                    continue;
                }

                $file = $path . DIRECTORY_SEPARATOR . $file;
                if(is_dir($file)){
                    self::unlink($path);
                    rmdir($file);
                }else{
                    self::unlink($file);
                }
            }
        }
    }

    public static function readDir($path){
        $data = [];
        if(file_exists($path)){
            $dn = opendir($path);
            while($file = readdir($dn)){
                if($file == '.' || $file == '..'){
                    continue;
                }

                $filePath = $path . DIRECTORY_SEPARATOR . $file;
                if(is_dir($filePath)){
                    $data[$file] = self::readDir($filePath);
                }else{
                    $data[] = $filePath;
                }
            }
        }
    }
}