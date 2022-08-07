<?php
namespace Kang\Libs\Helper;

define('FeleCacheLogPath', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR);
/**
 * @var 文件缓存
 * Class FileCache
 * @package MyLibs\Library
 */
class FileCache{
    const FORMAT = '.back';
    public $file_logs = FeleCacheLogPath;
    public $file_suffix =self::FORMAT;

    public static function getInstall($file_logs_path = FeleCacheLogPath){
        if(self::$_install == null){
            self::$_install = new static();
            self::$_install->setFileLogsPath($file_logs_path);
        }

        return self::$_install;
    }

    private function __construct(){}

    public function setFileLogsPath($path){
        $this->file_logs = $path;
        if(!is_dir($this->file_logs)){
            mkdir($this->file_logs, 0775, true);
        }
    }
    /**
     * @var 获取缓存
     * @param $key
     * @return array|null
     */
    public function get($key){
        if(! $this->has($key)){
            return null;
        }

       return $this->readData($key);
    }
    /**
     * @var 设置缓存
     * @param $key  缓存的key值
     * @param $data 要缓存的数据
     * @param int $expiredTime
     * @return bool
     */
    public function set($key, $data, $expiredTime = 0){
        $expiredTime += $expiredTime > 0 ? time() : 0;
        $filePath = $this->file_logs . $key . $this->file_suffix;
        $json = [
            'data'          => $data,
            'expired_time' => $expiredTime
        ];

        return $this->writeData($filePath, $json);
    }
    /**
     * @var 判断缓存文件是否存在
     * @param $alias
     * @return bool
     */
    public function has($alias){
        $path = $this->file_logs . $alias . $this->file_suffix;
        return file_exists($path);
    }

    private function writeData($filePath, $data){
        $bool = false;
        $data['write_time'] = time();
        $data = json_encode($data, true);
        $file = fopen($filePath, 'w');
        if(flock($file, LOCK_EX)){
            $bool = fwrite($file, $data);
            flock($file, LOCK_UN);
        }

        fclose($file);
        if($bool === false){
            return false;
        }

        return true;
    }
    /**
     * @var 获取文件保存的数据
     * @param string $key
     * @return array|null
     */
    private function readData($key){
        $path = $this->file_logs . $key . $this->file_suffix;
        if(!file_exists($path)){
            return null;
        }

        $data = null;
        $file = fopen($path, 'r');
        if(flock($file, LOCK_SH)){
            $data = null;
            if($size = filesize($path)){
                $data = fread($file, filesize($path));
            }

            flock($file, LOCK_UN);
        }

        fclose($file);
        if(!$data){
            return null;
        }

        $data = (array)json_decode($data, true);
        if($data['expired_time'] > 0 && $data['expired_time'] < time()){
            $this->removeFile($path);
            return null;
        }

        if($data['data'] instanceof \stdClass){
           return (array)$data['data'];
        }

        return $data['data'];
    }
    /**
     * 清楚缓存
     * */
    private function clean(){
        if($dir = opendir($this->file_logs)){
            while($file = readdir($dir)){
                if(!($file === '.' || $file === '..')){
                    $this->removeFile($this->file_logs . $file);
                }
            }
        }
    }

    private function removeFile($path){
        unlink($path);
    }


    private static $_install = null;
}