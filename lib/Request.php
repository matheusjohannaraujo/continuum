<?php

/*
	GitHub: https://github.com/matheusjohannaraujo/makemvcss
	Country: Brasil
	State: Pernambuco
	Developer: Matheus Johann Araujo
	Date: 2022-04-04
*/

namespace Lib;

use Lib\ENV;
use Lib\JWT;

class Request
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
    private $method = null;
    private $keysOnly = [];

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
        $METHOD = strtoupper($this->paramServer("REQUEST_METHOD"));
        $METHOD = strtoupper($this->paramReq("_method", $METHOD));
        $METHOD = strtoupper($this->paramJson("_method", $METHOD));
        $this->setMethod($METHOD);
        if (\Lib\Route::$in !== null) {
            $this->setArg(\Lib\Route::$in->paramArg());
        }
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

    private function setMethod(string &$value)
    {
        $this->method = $value;
        return $this;
    }

    public function getMethod()
    {
        return $this->method;
    }

    private function setJWT()
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
            $this->jwt = new JWT($this->auth);
        } else {
            $this->auth = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJsb2NhbGhvc3QiLCJzdWIiOiJKV1QgQ3JlZGVudGlhbCIsImF1ZCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvbWFrZW12Y3NzIiwiZXhwIjoxNjE2NTk3ODQyLCJuYmYiOjE2MTY1OTc4MjcsImlhdCI6MTYxNjU5NzgyNywianRpIjoiMzgxN2Y2NTNhMTVlNjI5ZTM4ZWY0ZDI0ODA0NjcwYjQiLCJuYW1lIjoiTWFrZU1WQ1NTIiwiaW5mbyI6eyJhdXRvciI6Ik1hdGhldXMgSm9oYW5uIEFyYVx1MDBmYWpvIiwiY291bnRyeSI6IkJyYXNpbCIsInN0YXRlIjoiUGVybmFtYnVjbyIsImRhdGUiOiIyMDIxLTAzLTI0In19.h5wkoZy0VdxErsNHgEf4vx0bWhy4MqgcV6wPCKbD3ms";
            $this->jwt = new JWT($this->auth);
        }
        $this->jwt->valid();
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
        $this->setJWT();
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

    public function only(array $keys = [])
    {
        $this->keysOnly = $keys;
        return $this;
    }

    public function getParameter($param, $key = null, $valueDefault = null)
    {
        if ($key === null) {
            if (count($this->keysOnly) === 0) {
                return $this->$param;
            }
            $parameter = [];
            foreach ($this->keysOnly as $key => $value) {
                if (is_int($key)) {
                    $key = $value;
                    $valueDefault = null;
                } else {
                    $valueDefault = $value;
                }
                $parameter[$key] = $this->$param[$key] ?? $valueDefault;
            }
            //$this->keysOnly = [];
            return $parameter;
        } else {
            if (is_array($key)) {
                return $this->only($key)->getParameter($param);
            }
            return $this->$param[$key] ?? $valueDefault;
        }
        return false;
    }

    public function get($key = null, $valueDefault = null)
    {
        if (is_array($key)) {
            $this->only($key);
            $key = null;
        }
        $array = [];
        $array[] = $this->paramFile($key);
        $array[] = $this->paramJson($key);
        $array[] = $this->paramPost($key);
        $array[] = $this->paramGet($key);
        $array[] = $this->paramReq($key);
        $values = [];
        for ($i = 0; $i < count($array); $i++) { 
            if ($array[$i] !== null) {
                if ($key !== null) {
                    $values = $array[$i];
                    break;
                }
                foreach ($array[$i] as $akey => $avalue) {
                    $values[$akey] = $avalue;
                }
            }
        }
        unset($array);
        if (is_array($values) && count($values) === 0) {
            return $valueDefault;
        }
        return $values;
    }

    public function paramArg($key = null, $valueDefault = null)
    {
        return $this->getParameter("arg", $key, $valueDefault);
    }

    public function paramGet($key = null, $valueDefault = null)
    {
        return $this->getParameter("get", $key, $valueDefault);
    }

    public function paramReq($key = null, $valueDefault = null)
    {
        return $this->getParameter("req", $key, $valueDefault);
    }

    public function paramJwt()
    {
        return $this->jwt;
    }

    public function paramAuth()
    {
        return $this->auth;
    }

    public function paramPost($key = null, $valueDefault = null)
    {
        return $this->getParameter("post", $key, $valueDefault);
    }

    public function paramJson($key = null, $valueDefault = null)
    {
        return $this->getParameter("json", $key, $valueDefault);
    }

    public function paramFile($key = null, $valueDefault = null)
    {
        return $this->getParameter("file", $key, $valueDefault);
    }

    public function paramEnv($key = null, $valueDefault = null)
    {
        return $this->getParameter("env", $key, $valueDefault);
    }

    public function paramServer($key = null, $valueDefault = null)
    {
        return $this->getParameter("server", $key, $valueDefault);
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
            "server" => &$this->server,
            "method" => &$this->method
        ];
    }

}
