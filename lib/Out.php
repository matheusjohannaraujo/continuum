<?php

/*
	GitHub: https://github.com/matheusjohannaraujo/makemvcss
	Country: Brasil
	State: Pernambuco
	Developer: Matheus Johann Araujo
	Date: 2020-11-23
*/

namespace Lib;

use Lib\Cache;
use Lib\DataManager;
use Lib\AVStreamDataManager;

class Out
{

    private $status = 200;
    private $download = 0;
    private $filename = "index";
    private $extension = ".html";
    private $name;
    private $mimetype = "text/html; charset=utf-8";
    private $content = "";
    private $size = 0;
    private $kill = true;
    private $delay = 0;
    private $bitrate = 500000;// 500kb
    // private $bitrate = 1000000;// 1Mb
    private $contentDescription = "File Transfer";
    private $contentTransferEncoding = "binary";
    private $connection = "Keep-Alive";
    private $cacheControl = "must-revalidate, post-check=0, pre-check=0";
    private $expires = 0;
    private $stream = 0;
    private $pragma = "public";
    private $header = [];
    private $dataManager = null;

    public function __construct($text = "", $mimetype = "text/html; charset=utf-8")
    {
        $this->content($text);
        $this->mimetype($mimetype);
        $this->dataManager = new DataManager;
    }

    public function status($value)
    {
        $this->status = $value;
        return $this;
    }

    public function download($value)
    {
        $this->download = $value;
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

    public function mimetype($value)
    {
        $this->mimetype = $value;
        return $this;
    }

    public function name($value)
    {
        $this->name = $value;
        $value = pathinfo($value);
        $this->filename($value["filename"]);
        $this->extension("." . $value["extension"]);
        $this->mimetype(get_mime_type($value["extension"]));
        return $this;
    }

    private $cache = null;
    private $recordCache = false;

    public function cache(string $name, int $time)
    {
        $this->cache = new Cache;
        $hasCache = $this->cache->exist($name, $time);
        if ($hasCache) {
            $location = $this->cache->get_paths($name);
            $mimetype = $this->cache->env()->get("CONTENT_TYPE");
            if ($mimetype !== null) {
                $this->mimetype($mimetype);
            }
            $this
                ->fopen($location["content"])
                ->go();
        } else {
            $this->recordCache = $this->cache->init($name, $time);
        }
    }

    public function content($value)
    {
        if ($value === null) {
            $value = "";
        }
        $this->content = $value;
        if (is_object($this->content)) {
            $this->content = parse_array_object_to_array(object_to_array($this->content));
        }
        if (is_array($this->content)) {
            $this->name($this->filename . ".json");
            $this->content = json_encode($this->content);
        }
        $this->size = strlen($this->content);
        return $this;
    }

    public function size($value)
    {
        $this->size = $value;
        return $this;
    }

    public function kill($value)
    {
        $this->kill = $value;
        return $this;
    }

    public function delay($value)
    {
        $this->delay = $value;
        return $this;
    }

    public function bitrate($value)
    {
        $this->bitrate = $value;
        return $this;
    }

    public function fopen($value)
    {
        $this->dataManager->fopen($value);
        $this->stream = 1;
        $this->size = $this->dataManager->size;
        $this->bitrate = $this->dataManager->bitrate;
        return $this;
    }

    public function memory()
    {
        $this->dataManager->fmemory();
        return $this;
    }

    public function stream($value)
    {
        $this->stream = $value;
        return $this;
    }

    public function header($key, $value)
    {
        $this->header[] = "$key: $value";
        return $this;
    }

    public function contentDescription($value)
    {
        $this->contentDescription = $value;
        return $this;
    }

    public function contentTransferEncoding($value)
    {
        $this->contentTransferEncoding = $value;
        return $this;
    }

    public function connection($value)
    {
        $this->connection = $value;
        return $this;
    }

    public function expires($value)
    {
        $this->expires = $value;
        return $this;
    }

    public function cacheControl($value)
    {
        $this->cacheControl = $value;
        return $this;
    }

    public function pragma($value)
    {
        $this->pragma = $value;
        return $this;
    }

    public function streamText()
    {
        if ($this->recordCache) {
            $info = $this->cache->info();
            $info["CONTENT_TYPE"] = $this->mimetype;
            $this->cache->info($info);
        }
        for ($i = 0; $i < $this->size; $i += $this->bitrate) {
            if ($this->recordCache) {
                $this->cache->append(substr($this->content, $i, $this->bitrate));
            }
            print(substr($this->content, $i, $this->bitrate));
            flush();
            if ($this->delay > 0) {
                usleep($this->delay);
            }
        }
    }

    public function streamFile()
    {
        while (!$this->dataManager->feof()) {
            print($this->dataManager->fread($this->bitrate));
            flush();
            if ($this->delay > 0) {
                usleep($this->delay);
            }
        }
    }

    public function streamAV()
    {
        $avs = new AVStreamDataManager($this->dataManager);
        $avs->name($this->name);
        $avs->mimetype($this->mimetype);
        $avs->buffer($this->bitrate);
        $avs->init();
    }

    public function prepare()
    {
        $filename = iconv("UTF-8", "ISO-8859-1", $this->filename . $this->extension);
        switch ($this->download) {
            case 1:
                $this->header("Content-Disposition", 'attachment; filename="' . $filename . '"');
                break;
            case 2:
                $this->header("Content-Disposition", 'attachment; filename="' . $filename . '"');
                $this->mimetype = "application/octet-stream";
                // $this->mimetype = "application/force-download";
                // $this->mimetype = "application/download";
                break;
            default:
                // No Download
                $this->header("Content-Disposition", 'inline; filename="' . $filename . '"');
                break;
        }
        $this->header("Content-Type", $this->mimetype);
        $this->header("Content-Description", $this->contentDescription);
        $this->header("Content-Transfer-Encoding", $this->contentTransferEncoding);
        $this->header("Connection", $this->connection);
        $this->header("Expires", $this->expires);
        $this->header("Cache-Control", $this->cacheControl);
        $this->header("Pragma", $this->pragma);
        $this->header("Content-Length", $this->size);
        http_response_code($this->status);
        foreach ($this->header as $value) {
            @header($value);
        }
        if (ob_get_length() > 0)
            ob_clean();
        flush();
    }

    public function go($value = false)
    {
        if ($value) {
            $this->stream(2);
        }
        switch ($this->stream) {
            case 1:
                $this->prepare();
                $this->streamFile();
                break;
            case 2:
                $this->streamAV();
                break;
            default:
                $this->prepare();
                $this->streamText();
        }
        if ($this->kill) {
            die();
        }
    }

    public function page404()
    {
        if (input()->contentTypeIsJSON()) {
            $this->content([
                "error" => "NOT FOUND",
                "status" => 404,
                "message" => "Sorry, an error has occured, Requested page not found!"
            ]);
        } else {
            $this->content(view("page_message", [
                "title" => "404 - NOT FOUND",
                "body" => "<h1>STATUS CODE: 404 - NOT FOUND</h1>
                <marquee behavior=\"alternate\"><h1>Sorry, an error has occured, Requested page not found!</h1></marquee>",
            ]));
        }
        $this
            ->status(404)
            ->go();
    }

    public function pageCSRF()
    {
        if (input()->contentTypeIsJSON()) {
            $this->content([
                "error" => "CSRF FORBIDDEN",
                "status" => 403,
                "message" => "Sorry, an error occurred. The CSRF Token informed is not valid!"
            ]);
        } else {
            $this->content(view("page_message", [
                "title" => "403 - CSRF FORBIDDEN",
                "body" => "<h1>STATUS CODE: 403 - CSRF FORBIDDEN</h1>
                <marquee behavior=\"alternate\"><h1>Sorry, an error occurred. The CSRF Token informed is not valid!</h1></marquee>",
            ]));
        }
        $this
            ->status(403)
            ->go();
    }

    public function pageJWT()
    {
        if (input()->contentTypeIsJSON()) {
            $this->content([
                "error" => "JWT UNAUTHORIZED",
                "status" => 401,
                "message" => "Sorry, an error occurred. The JWT Token informed is not valid!"
            ]);
        } else {
            $this->content(view("page_message", [
                "title" => "401 - JWT UNAUTHORIZED",
                "body" => "<h1>STATUS CODE: 401 - JWT UNAUTHORIZED</h1>
                <marquee behavior=\"alternate\"><h1>Sorry, an error occurred. The JWT Token informed is not valid!</h1></marquee>",
            ]));
        }
        $this
            ->status(401)
            ->go();
    }

}
