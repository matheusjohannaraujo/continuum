<?php

/*
	GitHub: https://github.com/matheusjohannaraujo/makemvcss
	Country: Brasil
	State: Pernambuco
	Developer: Matheus Johann Araujo
	Date: 2020-06-23
*/

namespace Lib;

class AVStreamDataManager
{

    private $buffer = 1024;
    private $start = 0;
    private $end = 0;
    private $mimetype = "video/mp4";
    private $filename = "video";
    private $extension = ".mp4";
    private $dataManager = null;

    public function __construct($dataManager)
    {
        $this->dataManager = $dataManager;
    }

    public function mimetype($value = "video/mp4")
    {
        $this->mimetype = $value;
    }

    public function buffer($value = 512)
    {
        $this->buffer = $value;
        return $this;
    }

    public function name($value)
    {
        if ($value != "") {
            $value = pathinfo($value);
            $this->filename($value["filename"]);
            $this->extension("." . $value["extension"]);
            $this->mimetype(get_mime_type($value["extension"]));
        }
        return $this;
    }

    public function filename($value)
    {
        $this->filename = $value;
        return $this;
    }

    public function extension($value)
    {
        $this->extension = $value;
        return $this;
    }

    public function init()
    {
        $this->start = 0;
        $this->size = $this->dataManager->size;
        $this->dataManager->fseek($this->start);
        $this->end = $this->size - 1;
        set_time_limit(0);
        ob_get_clean();
        header('Content-Disposition: inline; filename="' . $this->filename . $this->extension . '"');
        header("Content-Type: $this->mimetype");
        header("Cache-Control: max-age=604800, public");
        header("Expires: " . gmdate('D, d M Y H:i:s', time() + 604800) . ' GMT');
        header("Accept-Ranges: 0-$this->end");
        if (isset($_SERVER['HTTP_RANGE'])) {
            $chunckStart = $this->start;
            $chunckEnd = $this->end;
            list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
            if (strpos($range, ',') !== false) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $this->start-$this->end/$this->size");
                exit;
            }
            if ($range == '-') {
                $chunckStart = $this->size - substr($range, 1);
            } else {
                $range = explode('-', $range);
                $chunckStart = $range[0];
                $chunckEnd = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $chunckEnd;
            }
            $chunckEnd = ($chunckEnd > $this->end) ? $this->end : $chunckEnd;
            if ($chunckStart > $chunckEnd || $chunckStart > $this->size - 1 || $chunckEnd >= $this->size) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $this->start-$this->end/$this->size");
                exit;
            }
            $this->start = $chunckStart;
            $this->end = $chunckEnd;
            $length = $this->end - $this->start + 1;
            //fseek($this->stream, $this->start);
            $this->dataManager->fseek($this->start);
            header('HTTP/1.1 206 Partial Content');
            header("Content-Length: $length");
            header("Content-Range: bytes $this->start-$this->end/$this->size");
        } else {
            header("Content-Length: " . $this->size);
        }
        $initialStart = $this->start;
        while (!$this->dataManager->feof() && $initialStart <= $this->end) {
            $bytesToRead = $this->buffer;
            if (($initialStart + $bytesToRead) > $this->end) {
                $bytesToRead = $this->end - $initialStart + 1;
            }
            echo $this->dataManager->fread($bytesToRead);
            flush();
            $initialStart += $bytesToRead;
        }
        $this->dataManager->fclose();
        die();
    }

}
