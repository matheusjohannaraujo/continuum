<?php

namespace Lib;

use Lib\Route;

class Redirect
{

    /*
        Redireciona a página para o caminho informado
    */
    public function to(string $path)
    {
        header("Location: $path", true, 301);
        die;
    }

    /*
        Redireciona a página usando uma rota existente
    */
    public function action(string $path, ...$params)
    {
        $path = Route::link($path, $params);
        $this->to($path);
    }

    /*
        Redireciona a página usando uma rota existente
    */
    public function route(string $path, ...$params)
    {
        $this->action($path, ...$params);
    }

    /*
        Volta para a página anterior
    */
    public function back()
    {
        $no_referer = $_SERVER["HTTP_NO_REFERER"] ?? false;
        $path_back = $_SERVER["HTTP_REFERER"] ?? false;
        if ($path_back && !$no_referer) {
            $this->to($path_back);
            // header("Location:javascript://history.go(-1)");
            // header("Refresh: 5; URL=\"$back\"");
        } else {
            die(json_encode([
                "error" => "Redirect->back()",
                "input" => session()
            ]));
        }
    }

    public function withInput()
    {
        $session = session();
        $session->set_input($_REQUEST);
        return $this;
    }

    public function withInputGet()
    {
        $session = session();
        $session->set_input($_GET);
        return $this;
    }

    public function withInputPost()
    {
        $session = session();
        $session->set_input($_POST);
        return $this;
    }

}