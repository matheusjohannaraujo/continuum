<?php

/*
	GitHub: https://github.com/matheusjohannaraujo/data_manager
	Country: Brasil
	State: Pernambuco
	Developer: Matheus Johann Araujo
	Date: 2020-11-11
*/

namespace Lib;

use ZipArchive;

class DataManager
{

    // INSTANCE
    public $stream = -1;
    public $size = 0;
    public $start = 0;
    public $bitrate = 1;
    public $fp = [];

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

    public function fopen($value, $mode = "rb")
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

    public function fmemory()
    {
        if (($fpMem = @fopen("php://memory", "w+") ?? false) && count($this->fp) > 0) {
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

    public function feof()
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

    public function fseek($index)
    {
        $this->start = $index;
        $this->setStream($index);
        if ($this->stream > -1) {
            fseek($this->fp[$this->stream]["fp"], $index);
        }
    }

    public function fgets()
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

    public function fread($buffer)
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

    private function setStream(&$start)
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

    public function fclose()
    {
        foreach ($this->fp as $key => $value) {
            fclose($value["fp"]);
            unset($this->fp[$key]);
        }
        $this->fp = [];
    }

    // STATIC

    public static function exist($path)
    {
        if (gettype($path) == "resource") {
            return "FP";
        }
        if (file_exists($path) && is_file($path)) {
            return "FILE";
        }
        if (file_exists($path) && is_dir($path)) {
            return "FOLDER";
        }
        if (($fp = @fopen($path, "rb") ?? false)) {
            fclose($fp);
            return "FP";
        }
        return false;
    }

    public static function rename($path, $newPath)
    {
        if (self::exist($path) && !self::exist($newPath)) {
            return rename($path, $newPath);
        }
        return false;
    }

    public static function delete($path)
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

    public static function perm($path, $permit = false)
    {
        if (self::exist($path) && !$permit) {
            return substr(sprintf('%o', fileperms($path)), -4);
        } else if (self::exist($path) && is_numeric($permit)) {
            return chmod($path, $permit);
        }
        return false;
    }

    public static function copy($path, $newPath)
    {
        if (self::exist($path) == "FILE") {
            if (self::exist($newPath) == "FOLDER") {
                $newPath = self::path($newPath) . pathinfo($path)['basename'];
            }
            return copy($path, $newPath);
        } else if (self::exist($path) == "FOLDER") {
            $path = self::path($path);
            $newPath = self::path($newPath);
            if (!self::exist($newPath)) {
                self::folderCreate($newPath);
            }
            $array = self::folderScan($path, true, true);
            foreach ($array as $key => $value) {
                if (self::exist($value) == "FOLDER") {
                    self::folderCreate(str_replace($path, $newPath, $value));
                } else if (self::exist($value) == "FILE") {
                    copy($value, str_replace($path, $newPath, $value));
                }
            }
            return true;
        }
        return false;
    }

    public static function move($path, $newPath)
    {
        if (self::copy($path, $newPath)) {
            return self::delete($path);
        }
        return false;
    }

    public static function time($path, $created_at = true)
    {
        if (self::exist($path)) {
            return date("Y-m-d H:i:s", $created_at ? filectime($path) : filemtime($path));            
        }
        return false;
    }

    private static function fpsize($fp)
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

    public static function size($path, $convert = true, $precision = true)
    {
        $bytes = 0;
        if (self::exist($path) == "FOLDER") {
            $array = self::folderScan($path, true, true);
            for ($i = count($array) - 1, $j = 0; $i >= $j; $i--) {
                if (self::exist($array[$i]) == "FILE") {
                    $x = self::fileSize($array[$i], $precision);
                    if ($x < 0) {
                        $x *= -1;
                    }
                    $bytes += $x;
                }
                unset($array[$i]);
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
            if (($fp = @fopen($path, "rb") ?? false)) {
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

    public static function path($path)
    {
        if (is_string($path)) {
            $path = str_replace(["\\", "/"], "/", $path);
        }

        if (self::exist($path) == "FOLDER") {
            if (substr($path, -1) != "/" && substr($path, -1) != "\\") {
                $path .= "/";
            }
        }

        return $path;
    }

    public static function fileCreate($path, $value = "", $append = false)
    {
        if (!self::exist($path) && (is_string($value) || is_numeric($value)) && is_bool($append)) {
            return (file_put_contents($path, $value, ($append ? FILE_APPEND : false)) >= 0) ? true : false;
        }
        return false;
    }

    public static function fileWrite($path, $write = "", $mode = "w")
    {
        if (self::exist($path) != "FOLDER" && $mode == "w" || $mode == "w+") {
            $handle = fopen($path, $mode);
            if (is_array($write)) {
                foreach ($write as $key => $value) {
                    fwrite($handle, $value);
                }
            } else if (is_string($write) || is_numeric($write)) {
                fwrite($handle, $write);
            }
            return fclose($handle);
        }
        return false;
    }

    private static $vetFileAppend = [];

    public static function fileAppend($path, $append = "", $close = true, $mode = "a")
    {
        if (self::exist($path) != "FOLDER" && ($mode == "a" || $mode == "a+")) {
            $faName = md5($path);
            if (!(self::$vetFileAppend[$faName] ?? false)) {
                self::$vetFileAppend[$faName] = fopen($path, $mode);
            }
            if (is_array($append)) {
                foreach ($append as $key => $value) {
                    fwrite(self::$vetFileAppend[$faName], $value);
                }
            } else if (is_string($append) || is_numeric($append)) {
                fwrite(self::$vetFileAppend[$faName], $append);
            }
            if ($close) {
                return self::fileAppendClose($path);
            }
            return true;
        }
        return false;
    }

    public static function fileAppendClose($path)
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

    public static function fileRead($path, $return = 1, $callback = false, $mode = "rb")
    {
        if ((self::exist($path) == "FILE" || self::exist($path) == "FP") && is_integer($return) && ($mode == "rb" || $mode == "r" || $mode == "r+")) {
            switch ($return) {
                case 1:
                    $handle = fopen($path, $mode);
                    $string = "";
                    while ($value = fgets($handle)) {
                        $string .= $value;
                    }
                    fclose($handle);
                    return $string;
                case 2:
                    return file_get_contents($path);
                case 3:
                    return file($path);
                case 4:
                    if ($callback) {                                                
                        foreach (file($path) as $key => $value) {
                            if (!empty($callback($key, $value))) {
                                break;
                            }
                        }
                        return true;
                    }
            }
        }
        return false;
    }

    private static function fileSize($path, $precision)
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
                return false;
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

    public static function fileEncodeBase64($path, $del = false)
    {
        if (self::exist($path) == "FILE") {
            $newName = "enc_" . uniqid() . "." . pathinfo($path)["extension"];
            self::fileRead($path, 4, function ($key, $value) use ($newName) {
                self::fileAppend($newName, base64_encode($value) . "\r\n", false);
            });
            self::fileAppendClose($newName);
            if ($del) {
                if (self::delete($path)) {
                    if (rename($newName, $path)) {
                        $newName = $path;
                    }
                }
            }

            return $newName;
        }
    }

    public static function fileDecodeBase64($path, $del = false)
    {
        if (self::exist($path) == "FILE") {
            $newName = "dec_" . uniqid() . "." . pathinfo($path)["extension"];
            self::fileRead($path, 4, function ($key, $value) use ($newName) {
                self::fileAppend($newName, base64_decode($value), false);
            });
            self::fileAppendClose($newName);
            if ($del) {
                if (self::delete($path)) {
                    if (rename($newName, $path)) {
                        $newName = $path;
                    }
                }
            }

            return $newName;
        }
    }

    public static function fileSplit($path, $buffer = 1)
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
                $basename = basename($path);
                $handle = fopen($path, 'rb');
                for ($i = 0; $i < $parts; $i++) {
                    $partPath = $store . "part$i";
                    self::fileCreate($partPath, fread($handle, $buffer));
                }
                fclose($handle);
                return $store;
            }
        }
        return false;
    }

    public static function fileJoin($path)
    {
        if (self::exist($path) == "FOLDER") {
            $pathinfo = pathinfo($path);
            $dirname = self::path($pathinfo["dirname"] . "/" . $pathinfo["basename"] . "/../");
            $files = self::folderScan($path, true);
            $file0 = pathinfo($files[0]);
            $newName = $dirname . str_replace("split_", "", $pathinfo["basename"]);
            for ($i = 0, $j = count($files); $i < $j; $i++) {
                self::fileRead($files[$i], 4, function ($key, $value) use ($newName) {
                    self::fileAppend($newName, $value, false);
                });
            }
            self::fileAppendClose($newName);
            return $newName;
        }
        return false;
    }

    public static function fileMd5($path)
    {
        return (self::exist($path) == "FILE") ? md5_file($path) : false;
    }

    public static function folderCreate($path, $permit = 0777, $recur = true)
    {
        if (!self::exist($path) && is_integer($permit) && is_bool($recur)) {
            return mkdir($path, $permit, $recur);
        }
        return false;
    }

    public static function folderScan($path = ".", $arrayClean = false, $recursive = false)
    {
        $array = glob(self::path($path) . "*");
        foreach (glob(self::path($path) . ".*") as $value) {
            $basename = pathinfo($value)["basename"];
            if ($basename != "." && $basename != ".." && (self::exist($value) == "FILE" || self::exist($value) == "FOLDER")) {
                $array[] = $value;
            }
        }
        sort($array, SORT_NATURAL);
        if ($recursive || !$arrayClean) {
            foreach ($array as $key => $value) {
                if (self::exist($value) == "FILE" && !$arrayClean) {
                    $array[$key] = [
                        "md5" => self::fileMd5($value),
                        "src" => pathinfo($value)["dirname"] . "/",
                        "name" => pathinfo($value)["basename"],
                        "path" => $value,
                        "type" => "FILE",
                        "perm" => self::perm($value),
                        "size" => self::size($value),
                        "created_at" => self::time($value),
                        "modified_in" => self::time($value, false)
                    ];
                } else if (self::exist($value) == "FOLDER") {
                    if (!$arrayClean) {
                        $array[$key] = [
                            "md5" => self::folderMd5($value),
                            "src" => pathinfo($value)["dirname"] . "/",
                            "name" => pathinfo($value)["basename"],
                            "path" => $value,
                            "type" => "FOLDER",
                            "perm" => self::perm($value),
                            "size" => self::size($value),
                            "created_at" => self::time($value),
                            "modified_in" => self::time($value, false)
                        ];
                    }
                    if ($recursive) {
                        $array = array_merge($array, self::folderScan($value, $arrayClean, $recursive));
                    }
                }
            }
        }
        return $array;
    }

    public static function folderMd5($path)
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
        return false;
    }

    public static function zipCreate($path, $array, $passZip = "")
    {
        $return = false;
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

    public static function zipExtract($pathZip, $pathExtract, $passZip = "")
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

    public static function zipList($pathZip, $mode = 1, $passZip = "")
    {
        $return = false;
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

    public static function zipUnzipFolder(string $path, string $mode, string $pass = "") {
        $mode = strtolower($mode);
        if ($mode == "unzip" || $mode == "zip") {        
            $scan = self::folderScan($path);
            // var_dump($scan);
            foreach ($scan as $key => $value) {
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
                        echo "[Unzip] ";
                        if ($md5Extract == $md5) {
                            echo "Success | Match MD5 | ", (self::delete($value["path"]) ? $pathExtract : "Error delete: " . $value["path"]), "\r\n";
                        } else {
                            echo "Error | No Match MD5 | ", (self::delete($pathExtract) ? $value["path"] : "Error delete: " . $pathExtract), "\r\n";
                        }
                    } else {
                        self::delete($pathExtract);
                    }        
                } else if ($mode == "zip" && $indexZip === false && ($value["type"] == "FILE" || $value["type"] == "FOLDER")) {
                    $zipName = $value["path"] . "_md5" . $value["md5"] . ".zip";
                    echo "[Zip] ";
                    if (self::zipCreate($zipName, $value["path"], $pass)) {
                        echo (self::delete($value["path"]) ? "Success: " : "Error: "), "$zipName\r\n";
                    }
                }   
            }
        }
    }

}
