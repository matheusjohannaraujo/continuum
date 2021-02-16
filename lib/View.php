<?php

/*
	GitHub: https://github.com/matheusjohannaraujo/makemvcss
	Country: Brasil
	State: Pernambuco
	Developer: Matheus Johann Araujo
	Date: 2021-02-16
*/

namespace Lib;

use Lib\Cache;
use Lib\DataManager;

/**
 *
 * **Class View**
 *
 * EN-US: It is a class responsible for making calls for processing vision templates.
 * 
 * PT-BR: É uma classe responsável por fazer chamadas para processamento de modelos de visão.
 * 
 * @author Matheus Johann Araujo 
 * @package Lib 
 */
class View
{

    /**
     * **Attribute -> folderView**
     * @var string $folderView
     * @access private
     */
    private $folderView = "";

    /**
     * **Attribute -> sections**
     * @var array $sections
     * @access private
     */
    private $sections = [];

    /**
     * **Attribute -> views**
     * @var array $views
     * @access private
     */
    private $views = [];

    /**
     * **Attribute -> cache**
     * @var Cache|null $cache
     * @access private
     */
    private $cache = null;

    /**
     * **Attribute -> recordCache**
     * @var bool $recordCache
     * @access private
     */
    private $recordCache = false;

    /**
     * @constructor
     * @return object View
     */
    public function __construct()
    {
        $this->folderView = __DIR__ . "/../app/" . input_env("NAME_FOLDER_VIEWS") . "/";
    }

    /**
     * 
     * **Method -> locationFile**
     *
     * EN-US: It is a function that allows you to return a file location within the Views folder.
     * 
     * PT-BR: É uma função que permite retornar a localização de um arquivo dentro da pasta Views.
     * 
     * @access public
     * @param string $_FILE
     * @return string|false
     */
    public function locationFile(string $_FILE)
    {
        $pathfile = realpath($this->folderView . "${_FILE}.php");
        if (!DataManager::exist($pathfile)) {
            $pathinfo = pathinfo($_FILE);
            $dirname = $pathinfo["dirname"];
            $filename = $pathinfo["filename"];        
            $pathfile = realpath($this->folderView . "${dirname}/avr-${filename}.php");
        }    
        if (DataManager::exist($pathfile) == "FILE") {
            return $pathfile;
        }
        return false;
    }

    /**
     * 
     * **Method -> cache**
     *
     * EN-US: It is a function that sets or returns a View cache.
     * 
     * PT-BR: É uma função que define ou retorna um cache de uma View.
     * 
     * @access public
     * @param string $name
     * @param int $time_seconds
     * @return string|null
     */
    public function cache(string $name, int $time_seconds)
    {
        $this->cache = new Cache;
        $hasCache = $this->cache->exist($name, $time_seconds);
        if ($hasCache) {
            $location = $this->cache->get_paths($name);
            return DataManager::fileRead($location["content"]);
        } else {
            $this->recordCache = $this->cache->init($name, $time_seconds);
        }
        return null;
    }

    /**
     * 
     * **Method -> template**
     *
     * EN-US: It is a function that returns a string that contains the result of processing a View.
     * 
     * PT-BR: É uma função que retorna uma string que contém o resultado do processamento de uma View.
     * 
     * @access public
     * @param string $_FILE
     * @param array $_ARGS [optional]
     * @param int $_CACHE_SECONDS [optional]
     * @return string [reference var]
     */
    public function & template(string $_FILE, $_ARGS = [], int $_CACHE_SECONDS = -1)
    {
        $location = $_FILE;
        $_FILE = $this->locationFile($_FILE);
        if (!$_FILE) {
            return null;
        }
        $result = $this->cache("V:${location}", $_CACHE_SECONDS);
        if ($result !== null) {
            return $result;
        }
        if (is_array($_ARGS) && count($_ARGS) > 0) {
            extract($_ARGS, EXTR_SKIP);
        }
        $val = "";
        if (ob_get_length() > 0) {
            $val = ob_get_clean();
        }
        ob_start();
        echo $val;
        try {
            include $_FILE;
            while (count($this->views) > 0) {
                foreach ($this->views as $key => $view) {
                    $_FILE = $this->locationFile($view);
                    unset($this->views[$key]);
                    unset($key);
                    unset($view);
                    if (!$_FILE) {
                        continue;
                    }
                    include $_FILE;
                }
                $this->views = array_values($this->views);
            }
        } catch (\Throwable $e) {
            ob_get_clean();
            ob_start();
            dumpl(["message" => $e->getMessage(), "line" => $e->getLine(), "file" => $e->getFile()]);
        }
        $result = ob_get_clean();
        if ($this->recordCache) {
            $this->cache->put($result);
        }
        return $result;
    }

    /**
     * 
     * **Method -> section**
     *
     * EN-US: Function that starts a session of content that will be saved later.
     * 
     * PT-BR: Função que inicia uma sessão de conteúdo que será salva posteriormente.
     * 
     * @access public
     * @param string $name
     * @return null
     */
    public function section(string $name)
    {
        ob_start();
        $this->sections[] = [
            "name" => $name,
            "content" => ""
        ];
    }

    /**
     * 
     * **Method -> endSection**
     *
     * EN-US: Function that ends a previously opened session and saves the result of the execution.
     * 
     * PT-BR: Função que finaliza uma sessão aberta anteriormente e salva o resultado da execução.
     * 
     * @access public
     * @return null
     */
    public function endSection()
    {
        $this->sections[count($this->sections) - 1]["content"] = ob_get_clean();
    }

    /**
     * 
     * **Method -> renderSection**
     *
     * EN-US: Function that returns the result of an already rendered session.
     * 
     * PT-BR: Função que retorna o resultado de uma sessão já renderizada.
     * 
     * @access public
     * @param string $name
     * @return string
     */
    public function renderSection(string $name)
    {
        $result = "";
        foreach ($this->sections as &$section) {
            if ($section["name"] == $name) {
                $result = &$section["content"];
                break;
            }
        }
        return $result;
    }

    /**
     * 
     * **Method -> layout**
     *
     * EN-US: Function that defines a layout (view) that will be processed later.
     * 
     * PT-BR: Função que define um layout (view) que será processado posteriormente.
     * 
     * @access public
     * @param string $path
     * @return null
     */
    public function layout(string $path)
    {
        $this->views[] = $path;
    }

}
