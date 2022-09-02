<?php

namespace Kang\Libs\Helper;

class File{
    /**
     * @var 删除文件夹
     * @param $path
     */
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
    /**
     * @var 读取文件夹
     * @param $path
     */
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

    public static function fileLock($filePath, \Closure $closure){
        $bool = false;
        $file = fopen($filePath, 'w');
        if(flock($file, LOCK_EX)){
            $bool = $closure();
            flock($file, LOCK_UN);
        }

        fclose($file);
        return $bool;
    }
}