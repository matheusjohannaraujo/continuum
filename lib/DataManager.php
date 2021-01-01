<?php

/*
	GitHub: https://github.com/matheusjohannaraujo/data_manager
	Country: Brasil
	State: Pernambuco
	Developer: Matheus Johann Araujo
	Date: 2021-01-01
*/

namespace Lib;

error_reporting(E_ALL ^ E_WARNING);

use ZipArchive;

/**
 * **DataManager**
 *
 * @author Matheus Johann Araujo 
 * @package Lib 
 */
class DataManager
{

    // Attributes and methods below to be used in the class instance

    /**
     * 
     * @var int $stream
     */
    public $stream = -1;

    /**
     * 
     * @var int $size
     */
    public $size = 0;

    /**
     * 
     * @var int $start
     */
    public $start = 0;

    /**
     * 
     * @var int $bitrate
     */
    public $bitrate = 1;

    /**
     * 
     * @var array $fp
     */
    public $fp = [];

    /**
     * 
     * @param string|array $value [optional, default = null]
     * @return $this
     */
    public function __construct($value = null)
    {
        if (is_string($value)) {
            $this->fopen($value);
        } else if (is_array($value)) {
            for ($i = 0, $j = count($value); $i < $j; $i++) {
                $this->fopen($value[$i]);
            }
        }
    }

    /**
     * 
     * @param string|resource $value
     * @param string $mode [optional, default = "rb"]
     * @return void
     */
    public function fopen($value, string $mode = "rb") :void
    {
        if (is_string($value)) {
            $value = self::path($value);
            if (self::exist($value) == "FILE" || self::exist($value) == "FP") {
                $fp = fopen($value, $mode);
                $size = self::size($fp, false);
                $this->size += $size;
                $this->fp[] = [
                    "fp" => $fp,
                    "size" => $size,
                    "data" => stream_get_meta_data($fp),
                ];
                $mega = 1000000;// 1Mb
                if (count($this->fp) > 1) {
                    if ($this->bitrate > $size) {
                        $this->bitrate = $size;
                    }
                } else {
                    if ($size > $mega) {
                        $this->bitrate = $mega;
                    } else {
                        $this->bitrate = $size;
                    }
                }
            } else if (self::exist($value) == "FOLDER") {
                $folderScan = self::folderScan($value, true);
                foreach ($folderScan as $path) {
                    $this->fopen($path, $mode);
                }
            }
        } else if (gettype($value) == "resource") {
            $size = self::size($value, false);
            $this->fp[] = [
                "fp" => $value,
                "size" => $size,
                "data" => stream_get_meta_data($value),
            ];
        }
    }

    /**
     * 
     * @return bool
     */
    public function fmemory() :bool
    {
        if (($fpMem = fopen("php://memory", "w+") ?? false) && count($this->fp) > 0) {
            rewind($fpMem);
            $this->size = 0;
            foreach ($this->fp as $key => $value) {
                while (!feof($value["fp"])) {
                    $str = fread($value["fp"], 1024);
                    fwrite($fpMem, $str);
                    $this->size += strlen($str);
                }
                fclose($value["fp"]);
                unset($this->fp[$key]);
            }
            $this->fp = [];
            rewind($fpMem);
            $this->fp[] = [
                "fp" => $fpMem,
                "size" => $this->size,
                "path" => stream_get_meta_data($fpMem)["uri"],
            ];
            return true;
        }
        return false;
    }

    /**
     * 
     * @return bool
     */
    public function feof() :bool
    {
        if ($this->stream == -1) {
            $this->fseek(0);
        }
        $bool = feof($this->fp[$this->stream]["fp"]);
        if ($bool && $this->start < $this->size) {
            $bool = false;
        }
        return $bool;
    }

    /**
     * 
     * @param int $index
     * @return void
     */
    public function fseek(int $index) :void
    {
        $this->start = $index;
        $this->setStream($index);
        if ($this->stream > -1) {
            fseek($this->fp[$this->stream]["fp"], $index);
        }
    }

    /**
     * 
     * @return string
     */
    public function fgets() :string
    {
        $str = "";
        $start = $this->start;
        $this->setStream($start);
        if ($this->stream > -1) {
            $str = fgets($this->fp[$this->stream]["fp"]);
            $this->start += strlen($str);
            if ($str) {
                if (!preg_match("/\n/", $str)) {
                    if (!$this->feof()) {
                        $str .= $this->fgets();
                    }
                }
            }
        }
        return $str;
    }

    /**
     * 
     * @param int $buffer
     * @return string
     */
    public function fread(int $buffer) :string
    {
        $str = "";
        $start = $this->start;
        $this->setStream($start);
        if ($this->stream > -1) {
            $str = fread($this->fp[$this->stream]["fp"], $buffer);
            $len = strlen($str);
            $this->start += $len;
            $buffer = $buffer - $len;
            if ($this->start < $this->size && $buffer > 0) {
                $str .= $this->fread($buffer);
            }
        }
        return $str;
    }

    /**
     * 
     * @param int &$start [reference]
     * @return void
     */
    private function setStream(&$start) :void
    {
        $size = 0;
        $i = 0;
        if ($this->start < $this->size) {
            $this->stream = -1;
            foreach ($this->fp as $key => $value) {
                $size += $value["size"];
                if ($start < $size) {
                    $this->stream = $key;
                    if ($i >= 1) {
                        $x = $size - $value["size"];
                        $start = $start - $x;
                    }
                    break;
                }
                $i++;
            }
        } else {
            if ($this->fp[$this->stream]["fp"] ?? false) {
                fseek($this->fp[$this->stream]["fp"], -1);
            }
        }
    }

    /**
     * 
     * @return void
     */
    public function fclose()
    {
        foreach ($this->fp as $key => $value) {
            fclose($value["fp"]);
            unset($this->fp[$key]);
        }
        $this->fp = [];
    }

    // Statistical methods below

    /**
     * 
     * @param string|resource $path
     * @return string|null
     */
    public static function exist($path) :?string
    {        
        try {
            if (gettype($path) == "resource") {
                return "FP";
            }
            if (file_exists($path) && is_file($path)) {
                return "FILE";
            }
            if (file_exists($path) && is_dir($path)) {
                return "FOLDER";
            }
            if (($fp = fopen($path, "rb") ?? false)) {
                fclose($fp);
                return "FP";
            }
        } catch (\Throwable $th) {}
        return null;
    }

    /**
     * 
     * @param string $path
     * @param string $newPath
     * @return bool
     */
    public static function rename(string $path, string $newPath) :bool
    {
        if (self::exist($path) && !self::exist($newPath)) {
            return rename($path, $newPath);
        }
        return false;
    }

    /**
     * 
     * @param string $path
     * @return bool
     */
    public static function delete(string $path) :bool
    {
        if (self::exist($path) == "FILE") {
            return unlink($path);
        } else if (self::exist($path) == "FOLDER") {
            $scanSuperficie = self::folderScan($path, true, false);
            for ($i = count($scanSuperficie) - 1, $j = 0; $i >= $j; $i--) {
                if (self::exist($scanSuperficie[$i]) == "FILE") {
                    unlink($scanSuperficie[$i]);
                } else if (self::exist($scanSuperficie[$i]) == "FOLDER") {
                    $scanProfundo = self::folderScan($scanSuperficie[$i], true, true);
                    for ($k = count($scanProfundo) - 1, $l = 0; $k >= $l; $k--) {
                        if (self::exist($scanProfundo[$k]) == "FILE") {
                            unlink($scanProfundo[$k]);
                        } else if (self::exist($scanProfundo[$k]) == "FOLDER") {
                            rmdir($scanProfundo[$k]);
                        }
                    }
                    rmdir($scanSuperficie[$i]);
                }
            }
            return rmdir($path);
        }
        return false;
    }

    /**
     * 
     * @param string $path
     * @param int $permit [optional, default = -8910]
     * @return string|null
     */
    public static function perm(string $path, int $permit = -8910) :?string
    {
        if (self::exist($path)) {
            if ($permit != -8910) {
                chmod($path, $permit);
            }
            return substr(sprintf('%o', fileperms($path)), -4);
        }
        return null;
    }

    /**
     * 
     * @param string $path
     * @param string $newPath
     * @return bool
     */
    public static function copy(string $path, string $newPath) :bool
    {
        if (self::exist($path) == "FILE") {
            if (self::exist($newPath) == "FOLDER") {
                $newPath = self::path($newPath) . pathinfo($path)['basename'];
            }
            return copy($path, $newPath);
        } else if (self::exist($path) == "FOLDER") {
            $result = true;
            $path = self::path($path);
            $newPath = self::path($newPath);
            if (!self::exist($newPath)) {
                $result = $result && self::folderCreate($newPath);
            }
            $array = self::folderScan($path, true, true);
            foreach ($array as $key => $value) {
                if (self::exist($value) == "FOLDER") {
                    $result = $result && self::folderCreate(str_replace($path, $newPath, $value));
                } else if (self::exist($value) == "FILE") {
                    $result = $result && copy($value, str_replace($path, $newPath, $value));
                }
            }
            return $result;
        }
        return false;
    }

    /**
     * 
     * @param string $path
     * @param string $newPath
     * @return bool
     */
    public static function move(string $path, string $newPath) :bool
    {
        if (self::copy($path, $newPath)) {
            return self::delete($path);
        }
        return false;
    }

    /**
     * 
     * @param string $path
     * @param bool $created_at [optional, default = true]
     * @return string|null
     */
    public static function time(string $path, bool $created_at = true) :?string
    {
        if (self::exist($path)) {
            return date("Y-m-d H:i:s", $created_at ? filectime($path) : filemtime($path));            
        }
        return null;
    }

    /**
     * 
     * @param resource $fp
     * @return int
     */
    private static function fpsize($fp) :int
    {
        $data = stream_get_meta_data($fp);
        // var_export($data);
        $bytes = 0;
        if ($data["seekable"] === true) {
            fseek($fp, 0);
            while (!feof($fp)) {
                $bytes += strlen(fgets($fp));
            }
            fseek($fp, 0);
        }
        return $bytes;
    }

    /**
     * 
     * @param string|int|resource $path
     * @param bool $convert [optional, default = true]
     * @param bool $precision [optional, default = false]
     * @return string|int
     */
    public static function size($path, bool $convert = true, bool $precision = false)
    {
        $bytes = 0;
        if (self::exist($path) == "FOLDER") {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS));
            foreach ($iterator as $file) {
                if (self::exist($file) == "FILE") {
                    $x = self::fileSize($file, $precision);
                    if ($x < 0) {
                        $x *= -1;
                    }
                    $bytes += $x;
                }
            }
        } else if (self::exist($path) == "FILE") {
            $bytes = self::fileSize($path, $precision);
            if ($bytes < 0) {
                $bytes *= -1;
            }
        } else if (is_numeric($path)) {
            $bytes = $path;
            if ($bytes < 0) {
                $bytes *= -1;
            }
        } else if (gettype($path) == "resource") {
            $bytes = self::fpsize($path);
        } else if (self::exist($path) == "FP") {
            if (($fp = fopen($path, "rb") ?? false)) {
                $bytes = self::fpsize($fp);
                fclose($fp);
            }
        }
        if ($convert) {
            if ($bytes >= 1073741824) {
                $bytes = number_format($bytes / 1073741824, 2) . ' GB';
            } else if ($bytes >= 1048576) {
                $bytes = number_format($bytes / 1048576, 2) . ' MB';
            } else if ($bytes >= 1024) {
                $bytes = number_format($bytes / 1024, 2) . ' KB';
            } else if ($bytes > 1) {
                $bytes = $bytes . ' bytes';
            } else if ($bytes == 1) {
                $bytes = $bytes . ' byte';
            } else {
                $bytes = '0 bytes';
            }
        }
        return $bytes;
    }

    /**
     * 
     * @param string $path
     * @return string
     */
    public static function path(string $path) :string
    {
        $path = str_replace(["\\", "/"], "/", $path);
        if (self::exist($path) == "FOLDER") {
            if (substr($path, -1) != "/" && substr($path, -1) != "\\") {
                $path .= "/";
            }
        }
        return $path;
    }

    /**
     * 
     * @param string $path
     * @param string $value [optional, default = ""]
     * @param bool $append [optional, default = false]
     * @return bool
     */
    public static function fileCreate(string $path, string $value = "", bool $append = false) :bool
    {
        if (self::exist($path) === null) {
            return (file_put_contents($path, $value, ($append ? FILE_APPEND : false)) >= 0);
        }
        return false;
    }

    /**
     * 
     * @param string $path
     * @param string $value
     * @param string $mode [optional, default = "w"]
     * @return bool
     */
    public static function fileWrite(string $path, string $value, string $mode = "w") :bool
    {
        if (self::exist($path) != "FOLDER" && $mode == "w" || $mode == "w+") {
            return ($handle = fopen($path, $mode)) && (fwrite($handle, $value) >= 0) && fclose($handle);
        }
        return false;
    }

    private static $vetFileAppend = [];

    /**
     * 
     * @param string $path
     * @param string $value
     * @param bool $close [optional, default = true]
     * @param string $mode [optional, default = "a"]
     * @return bool
     */
    public static function fileAppend(string $path, string $value, bool $close = true, string $mode = "a") :bool
    {
        if (self::exist($path) != "FOLDER" && ($mode == "a" || $mode == "a+")) {
            $faName = md5($path);
            if (!(self::$vetFileAppend[$faName] ?? false)) {
                self::$vetFileAppend[$faName] = fopen($path, $mode);
            }
            fwrite(self::$vetFileAppend[$faName], $value);
            if ($close) {
                return self::fileAppendClose($path);
            }
            return true;
        }
        return false;
    }

    /**
     * 
     * @param string $path
     * @return bool
     */
    public static function fileAppendClose(string $path) :bool
    {
        $faName = md5($path);
        $handle = self::$vetFileAppend[$faName] ?? false;
        if ($handle) {
            if (fclose($handle)) {
                unset(self::$vetFileAppend[$faName]);
                return true;
            }
        }
        return false;
    }

    /**
     * 
     * @param string $path
     * @param int $type [optional, default = 1, possible values = 1...4]
     * @param int $length [optional, default = 1024]
     * @param string $mode [optional, default = "rb"]
     * @return string|array|Generator|null
     */
    public static function fileRead(string $path, int $type = 1, int $length = 1024, string $mode = "rb")
    {
        $gen = self::fileReadGenerator($path, $type, $length, $mode);
        if ($type === 1 || $type === 2) {
            return $gen->current();
        }
        return $gen;
    }

    /**
     * 
     * @param string $path
     * @param int $type [optional, default = 1, possible values = 1...4]
     * @param int $length [optional, default = 1024]
     * @param string $mode [optional, default = "rb"]
     * @return Generator
     * 
     * Using: 
     * ------ https://www.php.net/manual/pt_BR/class.generator.php
     * ------ https://pt.stackoverflow.com/questions/108082/qual-a-diferen%C3%A7a-entre-yield-e-return-no-php
     */
    private static function fileReadGenerator(string $path, int $type = 1, int $length = 1024, string $mode = "rb") :\Generator
    {
        if (self::exist($path) == "FILE" || self::exist($path) == "FP") {
            switch ($type) {                   
                case 1:
                    yield file_get_contents($path);
                case 2:
                    yield file($path);                    
                case 3:
                case 4:
                    if ($mode == "rb" || $mode == "r" || $mode == "r+") {
                        $handle = fopen($path, $mode);
                        if ($type === 3) {
                            while ($value = fgets($handle)) {
                                yield $value;
                                unset($value);
                            }
                        } else if ($type === 4) {
                            while (!feof($handle)) {
                                yield fread($handle, $length);
                            }
                        }
                        fclose($handle);
                    }
                    break;
            }
        }
    }

    /**
     * 
     * @param string $path
     * @param bool $precision
     * @return int
     */
    private static function fileSize(string $path, bool $precision) :int
    {
        if (self::exist($path) == "FILE") {
            $size = filesize($path);
            if (!$precision) {
                return $size;
            }
            if (!($file = fopen($path, 'rb'))) {
                return false;
            }
            if ($size >= 0) { // Check if it really is a small file (< 2 GB)
                if (fseek($file, 0, SEEK_END) === 0) { // It really is a small file
                    fclose($file);
                    return $size;
                }
            }
            // Quickly jump the first 2 GB with fseek. After that fseek is not working on 32 bit php (it uses int internally)
            $size = PHP_INT_MAX - 1;
            if (fseek($file, PHP_INT_MAX - 1) !== 0) {
                fclose($file);
                return 0;
            }
        }
        $read = "";
        $length = 8192;
        while (!feof($file)) { // Read the file until end
            $read = fread($file, $length);
            $size = bcadd($size, $length);
        }
        $size = bcsub($size, $length);
        $size = bcadd($size, strlen($read));
        fclose($file);
        return $size;
    }

    /**
     * 
     * @param string $path
     * @param bool $del [optional, default = false]
     * @return string|null
     */
    public static function fileEncodeBase64(string $path, bool $del = false) :?string
    {
        if (self::exist($path) == "FILE") {
            $newName = "enc_" . uniqid() . "." . pathinfo($path)["extension"];
            $gen = self::fileRead($path, 3);
            foreach ($gen as $value) {
                self::fileAppend($newName, base64_encode($value) . "\r\n", false);
            }
            self::fileAppendClose($newName);
            if (self::exist($newName) == "FILE" && $del) {
                if (self::delete($path)) {
                    if (rename($newName, $path)) {
                        $newName = $path;
                    }
                }
            }
            return $newName;
        }
        return null;
    }

    /**
     * 
     * @param string $path
     * @param bool $del [optional, default = false]
     * @return string|null
     */
    public static function fileDecodeBase64(string $path, bool $del = false) :?string
    {
        if (self::exist($path) == "FILE") {
            $newName = "dec_" . uniqid() . "." . pathinfo($path)["extension"];
            $gen = self::fileRead($path, 3);
            foreach ($gen as $value) {
                self::fileAppend($newName, base64_decode($value), false);
            }
            self::fileAppendClose($newName);
            if (self::exist($newName) == "FILE" && $del) {
                if (self::delete($path)) {
                    if (rename($newName, $path)) {
                        $newName = $path;
                    }
                }
            }
            return $newName;
        }
        return null;
    }

    /**
     * 
     * @param string $path
     * @param float $buffer [optional, default = 1]
     * @return string|null
     */
    public static function fileSplit(string $path, float $buffer = 1) :?string
    {
        if (self::exist($path) == "FILE") {
            $pathinfo = pathinfo($path);
            $store = self::path($pathinfo["dirname"] . "/split_" . $pathinfo["basename"] . "/");
            if (self::exist($store) == "FOLDER") {
                self::delete($store);
            }
            if (self::folderCreate($store)) {
                $buffer = 1024 * 1024 * $buffer;
                $parts = self::size($path, false) / $buffer;
                $handle = fopen($path, 'rb');
                for ($i = 0; $i < $parts; $i++) {
                    $partPath = $store . "part$i";
                    self::fileCreate($partPath, fread($handle, $buffer));
                }
                fclose($handle);
                return $store;
            }
        }
        return null;
    }

    /**
     * 
     * @param string $path
     * @return string|null
     */
    public static function fileJoin(string $path) :?string
    {
        if (self::exist($path) == "FOLDER") {
            $pathinfo = pathinfo($path);
            $dirname = self::path($pathinfo["dirname"] . "/" . $pathinfo["basename"] . "/../");
            $files = self::folderScan($path, true);
            $newName = $dirname . str_replace("split_", "", $pathinfo["basename"]);
            if (self::fileWrite($newName, "")) {
                $newName = realpath($newName);
                for ($i = 0, $j = count($files); $i < $j; $i++) {
                    $gen = self::fileRead($files[$i], 3);
                    foreach ($gen as $value) {
                        self::fileAppend($newName, $value, false);
                    }
                }
                self::fileAppendClose($newName);
                return $newName;
            }            
        }
        return null;
    }


    /**
     * 
     * @param string $path
     * @return string|null
     */
    public static function fileMd5(string $path) :?string
    {
        return (self::exist($path) == "FILE") ? md5_file($path) : null;
    }

    /**
     * 
     * @param string $path
     * @param int $permit [optional, default = 0777]
     * @return bool
     */
    public static function folderCreate(string $path, int $permit = 0777, bool $recur = true) :bool
    {
        if (!self::exist($path)) {
            return mkdir($path, $permit, $recur);
        }
        return false;
    }

    /**
     * 
     * @param string $path
     * @param bool $arrayClean [optional, default = false]
     * @param bool $recursive [optional, default = false]
     * @return array|null
     */
    public static function folderScan(string $path, bool $arrayClean = false, bool $recursive = false) :?array
    {
        $result = [];
        $path = realpath($path);
        if (self::exist($path) != "FOLDER") {
            return null;
        }
        if (!$recursive) {
            $files = scandir($path);
            foreach($files as $value){            
                $file = self::path(realpath($path . "/" . $value));
                if (self::exist($file) == "FOLDER" && (basename($value) == ".." || $path == realpath($file))) {
                    continue;
                }
                $result[] = $file;
            }
        } else {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
            foreach ($iterator as $file) {
                if ($file->isDir()) {
                    if (basename($file) == ".."  || $path == realpath($file)) {
                        continue;
                    }
                    if (basename($file) == ".") {
                        $file = substr($file, 0, -1);
                    }
                }
                $result[] = self::path($file);
            }
        }
        sort($result, SORT_NATURAL);        
        if (!$arrayClean) {
            foreach ($result as $key => $value) {
                if (self::exist($value) == "FOLDER") {
                    $result[$key] = [
                        "src" => self::path(pathinfo($value)["dirname"] . "/"),
                        "name" => pathinfo($value)["basename"],
                        "path" => self::path($value),
                        "type" => "FOLDER",
                        "perm" => self::perm($value),
                        "size" => self::size($value),
                        "time" => self::time($value),
                    ];
                } else if (self::exist($value) == "FILE") {
                    $result[$key] = [
                        "src" => self::path(pathinfo($value)["dirname"] . "/"),
                        "name" => pathinfo($value)["basename"],
                        "path" => self::path($value),
                        "type" => "FILE",
                        "perm" => self::perm($value),
                        "size" => self::size($value),
                        "time" => self::time($value),
                    ];
                }
            }
        }
        return $result;
    }

    /**
     * 
     * @param string $path
     * @return string|null
     */
    public static function folderMd5(string $path) :?string
    {
        $array = self::folderScan($path, true, true);
        foreach ($array as $key => $value) {
            if (self::exist($value) == "FILE") {
                $value = md5(md5($value) . self::fileMd5($value));
                $array[$key] = $value;
            } else if (self::exist($value) == "FOLDER") {
                $array[$key] = md5($value);
            }
        }
        if (is_array($array) && count($array) > 0) {
            return md5(implode('', $array));
        }
        return null;
    }

    /**
     * 
     * @param string $path
     * @param string|array $array
     * @param string $passZip [optional, default = ""]
     * @return int
     */
    public static function zipCreate(string $path, $array, string $passZip = "") :int
    {
        $return = 0;
        $zip = new ZipArchive();
        if (is_string($array)) {
            $array = [$array];
        }
        if ($zip->open($path, ZIPARCHIVE::CREATE) && is_array($array)) {
            $passStatus = false;
            if ($passZip != "") {
                $passStatus = $zip->setPassword($passZip);
            }
            $fANON = function ($zip, $path, $fANON, $passStatus, $dir = "") {
                if (is_dir($path)) {
                    $name = pathinfo($path)["basename"] . "/";
                    $array = glob($path . "/" . "*");
                    foreach (glob($path . "/" . ".*") as $value) {
                        $basename = pathinfo($value)["basename"];
                        if ($basename != "." && $basename != ".." && (self::exist($value) == "FILE" || self::exist($value) == "FOLDER")) {
                            $array[] = $value;
                        }
                    }
                    sort($array, SORT_NATURAL);
                    foreach ($array as $key => $value) {
                        $zipValue = $dir . $name;
                        if (file_exists($value) && is_file($value)) {
                            //echo "FILE: ", $value, " = ", $zipValue . pathinfo($value)["basename"], "<br>";
                            $zip->addFile($value, $zipValue . pathinfo($value)["basename"]);
                            if ($passStatus) {
                                $zip->setEncryptionName($zipValue . pathinfo($value)["basename"], ZipArchive::EM_AES_256);
                            }
                        } else if (file_exists($value) && is_dir($value)) {
                            //var_dump($value);
                            //echo "DIR: ", $value, " = ", $zipValue . pathinfo($value)["basename"], "<br>";
                            $fANON($zip, $value, $fANON, $passStatus, $zipValue);
                        }
                    }
                }
            };
            foreach ($array as $value) {
                if (is_string($value)) {
                    if (file_exists($value) && is_file($value)) { // FILE
                        $zip->addFile($value, pathinfo($value)['basename']);
                        if ($passStatus) {
                            $zip->setEncryptionName($value, ZipArchive::EM_AES_256);
                        }
                    } else if (file_exists($value) && is_dir($value)) { // FOLDER
                        $fANON($zip, $value, $fANON, $passStatus);
                    }
                } else if (is_array($value)) {
                    if (!file_exists($value[1]) && !is_file($value[1])) { // FILE AND STRING
                        $zip->addFromString($value[0], $value[1]);
                        if ($passStatus) {
                            $zip->setEncryptionName($value[0], ZipArchive::EM_AES_256);
                        }
                    }
                }
            }
            $return = $zip->numFiles;
            $zip->close();
        }
        return $return;
    }

    /**
     * 
     * @param string $pathZip
     * @param string $pathExtract
     * @param string $passZip [optional, default = ""]
     * @return bool
     */
    public static function zipExtract(string $pathZip, string $pathExtract, string $passZip = "") :bool
    {
        $return = false;
        $zip = new ZipArchive();
        if ($zip->open($pathZip)) {
            if ($passZip != "") {
                $zip->setPassword($passZip);
            }
            $return = $zip->extractTo($pathExtract);
            $zip->close();
        }
        return $return;
    }

    /**
     * 
     * @param string $pathZip
     * @param int $mode [optional, default = 1, possible values = 1...3]
     * @param string $passZip [optional, default = ""]
     * @return array|null
     */
    public static function zipList(string $pathZip, int $mode = 1, string $passZip = "") :?array
    {
        $return = null;
        $zip = new ZipArchive();
        if ($zip->open($pathZip)) {
            if ($passZip != "") {
                $zip->setPassword($passZip);
            }
            $return = [];
            for ($i = 0, $j = $zip->numFiles; $i < $j; $i++) {
                $status = $zip->statIndex($i);
                $status["size"] = self::size($zip->getStream($status["name"]), false);
                switch ($mode) {
                    case 1:
                        $return[] = $status;
                        break;
                    case 2:
                        $return[] = [$status, $zip->getStream($status["name"])];
                        break;
                    case 3:
                        $return[] = [$status, $zip->getFromName($zip->getNameIndex($i))];
                }
            }
            $zip->close();
        }
        return $return;
    }

    /**
     * 
     * @param string $path
     * @param string $mode [possible values = "zip" or "unzip"]
     * @param string $pass [optional, default = ""]
     * @return string|null
     */
    public static function zipUnzipFolder(string $path, string $mode, string $pass = "") :?string
    {
        $result = null;
        $mode = strtolower($mode);
        if ($mode == "unzip" || $mode == "zip") {
            $scan = self::folderScan($path);
            if (count($scan) > 0) {
                $result = "";
            }
            // var_dump($scan);
            foreach ($scan as $value) {
                // var_dump($value);
                $name = $value["name"];
                $indexMd5 = strpos($name, "_md5");
                $indexZip = strpos($name, ".zip");
                if ($mode == "unzip" && $indexMd5 !== false && $indexMd5 >= 0 && $indexZip !== false && $indexZip >= 0 && $value["type"] == "FILE") {
                    $md5 = substr($name, 0, $indexZip);
                    $md5 = substr($md5, $indexMd5 + 4);
                    $name = substr($name, 0, $indexMd5);
                    // echo $indexMd5, " | ", $indexZip, " | ", $name, " | ", $md5;
                    $pathExtract = self::path($path . $name);
                    if (self::zipExtract($value["path"], $path, $pass)) {
                        $md5Extract = uniqid();
                        if (self::exist($pathExtract) == "FILE") {
                            $md5Extract = self::fileMd5($pathExtract);
                        } else if (self::exist($pathExtract) == "FOLDER") {
                            $md5Extract = self::folderMd5($pathExtract);
                        }
                        $result .= "[Unzip] ";
                        if ($md5Extract == $md5) {
                            $result .= "Success | Match MD5 | " . (self::delete($value["path"]) ? $pathExtract : "Error delete: " . $value["path"]) . "\r\n";
                        } else {
                            $result .= "Error | No Match MD5 | " . (self::delete($pathExtract) ? $value["path"] : "Error delete: " . $pathExtract) . "\r\n";
                        }
                    } else {
                        self::delete($pathExtract);
                    }        
                } else if ($mode == "zip" && $indexZip === false && ($value["type"] == "FILE" || $value["type"] == "FOLDER")) {
                    $zipName = $value["path"] . "_md5" . $value["md5"] . ".zip";
                    $result .= "[Zip] ";
                    if (self::zipCreate($zipName, $value["path"], $pass)) {
                        $result .= (self::delete($value["path"]) ? "Success: " : "Error: ") . "$zipName\r\n";
                    }
                }   
            }
        }
        return $result;
    }

}
