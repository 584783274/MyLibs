<?php

namespace MyLibs\Libs\Help;

use ZipArchive;
/**
 * @var 文件压缩，解压
 * Class Zip
 * @package MyLibs\Libs\Help
 */
class FileZip{
    /**
     * @var 压缩指定文件 为ZIP
     * @param array|string  $files 等待压缩的文件路径
     * @param string $destination    压缩文件->保存路径
     * @return bool
     */
    public function zipFiles($files, $destination = './zip.zip') {
        $zipFiles  = $this->checkFile($files);
        if(count($zipFiles)) {
            $zip = new ZipArchive();
            if($zip->open($destination, ZipArchive::CREATE) !== true) {
                return false;
            }

            $this->package($zipFiles, $zip);
            $zip->close();
            return file_exists($destination);
        }else{
            return false;
        }
    }
    /**
     * @var 压缩指定文件夹 为ZIP
     * @param string $dirPath 文件夹路径
     * @param string $destination  压缩文件->保存路径
     * @return bool
     */
    public function zipDir($dirPath, $destination = './zip.zip'){
        $zipFiles  = $this->getFileList($dirPath);
        if(count($zipFiles)) {
            $zip = new ZipArchive();
            if($zip->open($destination, ZipArchive::CREATE) !== true) {
                return false;
            }

            $this->package($zipFiles, $zip);
            $zip->close();
            return file_exists($destination);
        }else{
            return false;
        }
    }
    /**
     * @var zip文件解压
     * @param string $filePath 要解压的文件路径
     * @param string $savePath 要保存的文件路径
     * @return bool
     * @throws \Error
     */
    public function unzip($filePath, $savePath = './zip'){
        if(!file_exists($filePath)){
            throw new \Error($filePath . '文件不存在！');
        }

        if(is_dir($savePath) == false){
            if(!mkdir($savePath, 0775, true)){
                throw new \Error($savePath . '保存路径不存在！');
            }
        }

        $zip = new ZipArchive();
        if ($zip->open($filePath) === true) {
            $bool = $zip->extractTo($savePath);
            $zip->close();
            return $bool;
        }

        return false;
    }

    /**
     * @return Zip
     */
    public static function install() : Zip{
        if(empty(self::$install)){
            self::$install = new self();
        }

        return self::$install;
    }

    private function checkFile($zipFiles){
        $zipFiles = is_array($zipFiles) ? $zipFiles : [$zipFiles];
        foreach($zipFiles as $key => $zipFile){
            if(!is_file($zipFile)){
                unset($zipFiles[$key]);
            }
        }

        return $zipFiles;
    }

    /**
     * @var 文件进行打包
     * @param $zipFiles
     * @param ZipArchive $zip
     * @param string $basePath
     */
    private  function package($zipFiles, ZipArchive $zip, $basePath = '.'){
        $zipFiles = is_array($zipFiles) ? $zipFiles : [$zipFiles];
        foreach($zipFiles as $key => $zipFile){
            if(is_array($zipFile)){
                $this->package($zipFile, $zip, $basePath . DIRECTORY_SEPARATOR . $key);
            }else{
                $zip->addFile($zipFile, $basePath . DIRECTORY_SEPARATOR .  basename($zipFile));
            }
        }
    }
    /**
     * @var 获取要压缩文件路径的所有文件
     * @param $path
     * @return array
     */
    private function getFileList($path){
        if(is_file($path)){
            return [$path];
        }

        $files = [];
        $dir = opendir($path);
        while($file = readdir($dir)){
            if($file == '.' || $file == '..'){
                continue;
            }

            $filePath = $path . DIRECTORY_SEPARATOR . $file;
            if(is_dir($filePath)){
                $files[$file] = $this->getFileList($filePath);
            }else{
                $files[] = $filePath;
            }
        }

        return $files;
    }

    private static $install = null;
}