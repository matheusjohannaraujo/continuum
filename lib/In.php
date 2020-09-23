<?php

/*
	GitHub: https://github.com/matheusjohannaraujo/makemvcss
	Country: Brasil
	State: Pernambuco
	Developer: Matheus Johann Araujo
	Date: 2020-11-01
*/

namespace Lib;

use Lib\ENV;
use Lib\JsonWT;

class In
{

    private $arg = [];
    private $req = [];
    private $get = [];
    private $post = [];
    private $json = [];
    private $file = [];
    private $env = [];
    private $auth = "";
    private $jwt = [];
    private $server = [];

    public function __construct()
    {
        $this
            ->setEnv()
            ->setServer($_SERVER)
            ->setReq($_REQUEST)
            ->setGet($_GET)
            ->setPost($_POST)
            ->setFile($_FILES)
            ->setJson();
    }

    private function headerAuthorization()
    {
        $headers = $this->server['Authorization'] ?? ($this->server['HTTP_AUTHORIZATION'] ?? false);
        if (!$headers && function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            $headers = $requestHeaders['Authorization'] ?? false;
        }
        if ($headers) {
            $headers = trim($headers);
        }
        return $headers;
    }

    private function setJsonWT()
    {
        $headers = $this->headerAuthorization();
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                $this->auth = $matches[1];
            }
        } else {
            $this->auth = $this->paramReq("_jwt", $this->paramJson("_jwt", ""));
        }
        if ($this->auth) {
            $this->jwt = new JsonWT($this->auth);
            $this->jwt->secret($this->paramEnv("JWT_SECRET"));
            $this->jwt->valid();
        } else {
            $this->auth = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJsb2NhbGhvc3QiLCJzdWIiOiJBdXRoIEpXVCBTeXN0ZW0iLCJhdWQiOiJjbGllbnQiLCJleHAiOjE1ODg5NjY3OTAsIm5iZiI6MTU4ODk2MzE5MCwiaWF0IjoxNTg4OTYzMTkwLCJqdGkiOiI1ZWI1YTc3NjMxNTZhIiwibmFtZSI6IkpXVENsYXNzIn0=.T7ty+OSJ7tsbtQlTsUpyY5feYeTPpYH/kWrGW/1tg2I=";
            $this->jwt = new JsonWT($this->auth);
        }
        return $this;
    }

    private function setEnv()
    {
        $env = new ENV;
        $env->read();
        $env->required();
        $this->env = $env->merge();
        return $this;
    }

    public function setArg(array &$value)
    {
        $this->arg = $value;
        return $this;
    }

    public function setReq(array &$value)
    {
        $this->req = $value;
        return $this;
    }

    public function setGet(array &$value)
    {
        $this->get = $value;
        return $this;
    }

    public function setPost(array &$value)
    {
        $this->post = $value;
        return $this;
    }

    public function contentTypeIsJSON()
    {
        return ($this->server['CONTENT_TYPE'] ?? '') == 'application/json';
    }

    public function setJson()
    {
        if ($this->contentTypeIsJSON()) {
            $putData = @fopen("php://input", "r");
            if ($putData) {
                $this->json = "";
                while ($data = fread($putData, 1024)) {
                    $this->json .= $data;
                }
                fclose($putData);
                $json = json_decode($this->json, true);
                if ($json === null) {
                    $this->json = json_decode(str_replace("'", '"', $this->json), true);
                } else {
                    $this->json = $json;
                }
                if ($this->json === null) {
                    $this->json = json_decode($this->paramReq("_json", "[]"), true);
                }
            }
            if (empty($this->json) || $this->json === null) {
                $this->json = [];
            }
        }
        $this->setJsonWT();
        return $this;
    }

    private function procFiles()
    {
        foreach ($this->file as $key => $files) {
            if (is_string($files['name'])) {
                unset($this->file[$key]);
                $this->file[$key][] = $files;
            } else if (is_array($files['name'])) {
                $count = (count($files['name']) + count($files['type']) + count($files['tmp_name']) + count($files['error']) + count($files['size'])) / 5;
                if (count($files['name']) == $count && $count > 0) {
                    $arrFiles = [];
                    for ($i = 0; $i < $count; $i++) {
                        $arrFiles[] = [
                            "name" => &$files['name'][$i],
                            "type" => &$files['type'][$i],
                            "size" => &$files['size'][$i],
                            "error" => &$files['error'][$i],
                            "tmp_name" => &$files['tmp_name'][$i],
                        ];
                    }
                    $this->file[$key] = $arrFiles;
                    unset($arrFiles);
                }
            }
        }
    }

    public function setFile(array &$value)
    {
        $this->file = $value;
        $this->procFiles();
        return $this;
    }

    public function setServer(array &$value)
    {
        $this->server = $value;
        return $this;
    }

    public function getParameter($param, $value = null, $valueDefault = null)
    {
        if ($value === null) {
            return $this->$param;
        } else {
            return $this->$param[$value] ?? $valueDefault;
        }
        return false;
    }

    public function paramArg($value = null, $valueDefault = null)
    {
        return $this->getParameter("arg", $value, $valueDefault);
    }

    public function paramGet($value = null, $valueDefault = null)
    {
        return $this->getParameter("get", $value, $valueDefault);
    }

    public function paramReq($value = null, $valueDefault = null)
    {
        return $this->getParameter("req", $value, $valueDefault);
    }

    public function paramJwt()
    {
        return $this->jwt;
    }

    public function paramAuth()
    {
        return $this->auth;
    }

    public function paramPost($value = null, $valueDefault = null)
    {
        return $this->getParameter("post", $value, $valueDefault);
    }

    public function paramJson($value = null, $valueDefault = null)
    {
        return $this->getParameter("json", $value, $valueDefault);
    }

    public function paramFile($value = null, $valueDefault = null)
    {
        return $this->getParameter("file", $value, $valueDefault);
    }

    public function paramEnv($value = null, $valueDefault = null)
    {
        return $this->getParameter("env", $value, $valueDefault);
    }

    public function paramServer($value = null, $valueDefault = null)
    {
        return $this->getParameter("server", $value, $valueDefault);
    }

    public function params()
    {
        return [
            "arg" => &$this->arg,
            "req" => &$this->req,
            "get" => &$this->get,
            "post" => &$this->post,
            "json" => &$this->json,
            "file" => &$this->file,
            "env" => &$this->env,
            "auth" => &$this->auth,
            "jwt" => object_to_array($this->jwt),
            "server" => &$this->server
        ];
    }

}
