<?php

namespace Lib;

#[\AllowDynamicProperties]
class Session
{

    private static $ClassSession = null;

    public static function instance()
    {
        if (self::$ClassSession == null) {
            self::$ClassSession = new Session;
            self::$ClassSession->start();
        }
        return self::$ClassSession;
    }

    public function set_name_and_id(?string $name = null, ?string $id = null)
    {
        $name_backup = session_name();
        $id_backup = session_id();
        if ($name !== null) {
            $name_backup = $name;
        }
        if ($id !== null) {
            $id_backup = $id;
        }
        if ($this->status(false) === PHP_SESSION_ACTIVE) {
            $keys_values = $_SESSION ?? [];
            $this->destroy();
            session_name($name_backup);
            session_id($id_backup);
            $this->start();
            $this->set_userdata($keys_values);
        } else {
            session_name($name_backup);
            session_id($id_backup);
        }
        return $this;
    }

    public function name()
    {
        return session_name();
    }

    public function id()
    {
        return session_id();
    }

    public function status(bool $text = false)
    {
        /*
            session_status()
            PHP_SESSION_DISABLED = 0
            PHP_SESSION_NONE = 1
            PHP_SESSION_ACTIVE = 2
        */
        $status = session_status();
        if ($text) {
            switch ($status) {
                case 0:
                    return "disabled";
                case 1:
                    return "none";
                case 2:
                    return "active";
            }
        }
        return $status;
    }

    private function session_property()
    {
        if ($this->status(false) === PHP_SESSION_ACTIVE) {
            foreach ($_SESSION as $key => &$value) {
                $this->{$key} = &$value;
            }
        }
    }

    public function start()
    {
        if ($this->status(false) === PHP_SESSION_NONE) {
            session_start();
            $this->session_property();
        }
        return $this;
    }

    public function abort()
    {
        if ($this->status(false) === PHP_SESSION_ACTIVE) {
            session_abort();
        }
        return $this;
    }

    public function cache_expire(?int $min = null)
    {
        if ($min !== null) {
            if ($this->status(false) === PHP_SESSION_ACTIVE) {
                $name_backup = session_name();
                $id_backup = session_id();
                $keys_values = $_SESSION ?? [];
                $this->destroy();
                session_cache_expire($min);
                session_name($name_backup);
                session_id($id_backup);
                $this->start();
                $this->set_userdata($keys_values);
            } else {
                session_cache_expire($min);
            }
            return $this;
        }
        return session_cache_expire();
    }

    public function cache_limiter(?string $limiter = null)
    {
        if ($limiter !== null) {
            if ($this->status(false) === PHP_SESSION_ACTIVE) {
                $name_backup = session_name();
                $id_backup = session_id();
                $keys_values = $_SESSION ?? [];
                $this->destroy();
                session_cache_limiter($limiter);
                session_name($name_backup);
                session_id($id_backup);
                $this->start();
                $this->set_userdata($keys_values);
            } else {
                session_cache_limiter($limiter);
            }
            return $this;
        }
        return session_cache_limiter();
    }

    public function has(string $key): bool
    {
        if ($this->status(false) === PHP_SESSION_ACTIVE) {
            return isset($_SESSION[$key]);
        }
        return false;
    }

    public function get(?string $key = null, $defaultValue = null)
    {
        if ($this->status(false) === PHP_SESSION_ACTIVE) {
            if ($key !== null) {
                return $_SESSION[$key] ?? $defaultValue;
            } else {
                return $_SESSION ?? [];
            }
        }
        return $defaultValue;
    }

    public function __set(string $key, $value)
    {
        $this->set($key, $value);
    }

    public function set(string $key, $value)
    {
        if ($this->status(false) === PHP_SESSION_ACTIVE) {
            $_SESSION[$key] = $value;
            $this->{$key} = &$_SESSION[$key];
        }
        return $this;
    }

    public function set_userdata(array $keys_values)
    {
        foreach ($keys_values as $key => $value) {
            $this->set($key, $value);
        }
        return $this;
    }

    public function delete(string $key)
    {
        if ($this->has($key)) {
            $this->set($key, null);
            unset($_SESSION[$key]);
            unset($this->$key);
        }
        return $this;
    }

    public function reset()
    {
        if ($this->status(false) === PHP_SESSION_ACTIVE) {
            session_reset();
        }
        return $this;
    }

    public function regenerate_id()
    {
        if ($this->status(false) === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
        return $this;
    }

    /*
        Define uma mensagem dentro do $_SESSION["__flash__"]
    */
    public function set_flash(string $key, $value)
    {
        $__flash__ = $this->get("__flash__", []);
        $__flash__[$key] = $value;
        $this->set("__flash__", $__flash__);
        return $this;
    }

    /*
        Define várias mensagens dentro do $_SESSION["__flash__"]
    */
    public function set_flashdata(array $keys_values)
    {
        foreach ($keys_values as $key => $value) {
            $this->set_flash($key, $value);
        }
        return $this;
    }

    /*
        Retorna uma ou todas as mensagens armazenadas em `$_SESSION["__flash__"]`
    */
    public function get_flash(?string $key = null, $defaultValue = null)
    {
        $__flash__ = $this->get("__flash__", []);
        if ($key !== null) {
            $content = $__flash__[$key] ?? $defaultValue;
            if (isset($__flash__[$key])) {
                $__flash__[$key] = null;
                unset($__flash__[$key]);
            }
        } else {
            $content = $__flash__;
            $this->delete("__flash__");
            $__flash__ = [];
        }
        $this->set("__flash__", $__flash__);
        return $content;
    }

    public function set_input(array &$input)
    {
        $session = session();
        foreach ($input as $key => &$value) {
            $session->set_flash("\$" . $key, $value);
        }
    }

    public function get_input(string $key, $defaultValue = null)
    {
        $session = session();
        $input = $session->get_flash("\$" . $key);
        return $input ?? $defaultValue;
    }

    public function clean_flash()
    {
        $this->delete("__flash__");
        $this->set("__flash__", []);
    }

    public function destroy()
    {
        if ($this->status(false) === PHP_SESSION_ACTIVE) {
            session_gc();
            session_unset();
            session_destroy();
            session_write_close();
            $this->start();
            $this->regenerate_id();
        }
        return $this;
    }

    /*
        Cria um nova sessão
    */
    public function auth_create()
    {
        $__auth__ = [
            "path" => [
                "refresh" => true,
                "init" => site_url(),
                "end" => site_url()
            ],
            "timer" => [
                "follow" => false,
                "expire" => 0,
                "seconds" => 0,
                "remaining" => 0
            ]
        ];
        $this->set("__auth__", $__auth__);
    }

    /*
        Se existir uma sessão, essa será retornada. No contrário o retorno será um booleano com o valor falso
    */
    public function auth_exist()
    {
        return $this->get("__auth__") ?? false;
    }

    /*
        Finaliza a sessão existente
    */
    public function auth_destroy()
    {
        $__auth__ = $this->get("__auth__");
        $this->delete("__auth__");
        $this->destroy();
        if ($__auth__ !== null) {
            return redirect()->to($__auth__["path"]["end"]);
        }
        dumpd("You are not logged in - <a href=\"" . action("home") . "\" style=\"color:red;\">Go Home</a>");
    }

    /*
        Libera ou nega o acesso a determinada parte do sistema, levando em conta se a sessão válida ou não
    */
    public function auth_verify()
    {
        if (!$this->auth_valid()) {
            return $this->auth_destroy();
        }
        $__auth__ = $this->get("__auth__");
        if ($__auth__ !== null && ($__auth__["path"]["refresh"] ?? false)) {
            $__auth__["path"]["refresh"] = false;
            $this->set("__auth__", $__auth__);
            return redirect()->to($__auth__["path"]["init"]);
        }
    }

    /*
        Inicia uma sessão com as informações de redirecionamento. A variável `$pathInit` armazena o
        nome da rota no qual o usuário deve ser redirecionado após fazer a autenticação, já `$pathEnd` contém
        o nome da rota em que o usuário deve ser redirecionado quando a sessão expirar.
    */
    public function auth_redirect(string $pathInit, string $pathEnd)
    {
        $__auth__ = $this->get("__auth__");
        if ($__auth__ !== null) {
            $__auth__["path"] = [
                "refresh" => true,
                "init" => $pathInit,
                "end" => $pathEnd
            ];
            $this->set("__auth__", $__auth__);
            $this->auth_verify();
        }
    }

    /*
        Define o tempo de expiração da sessão. Se o valor informado for "100" a sessão permanecerá ativa
        por cem segundos, para definir a sessão como sem tempo de expiração informe o valor "0". Para renovar
        o tempo em que a sessão deve está ativa informe o valor "-1", que faz com que seja recriado o tempo 
        total da sessão (time() + $seconds)    
    */
    public function auth_timer(?int $seconds = null)
    {
        $__auth__ = $this->get("__auth__");
        if ($__auth__ !== null) {
            if ($seconds !== null) {
                if ($seconds > 0) {
                    $__auth__["timer"]["follow"] = true;
                    $__auth__["timer"]["expire"] = time() + $seconds;
                    $__auth__["timer"]["seconds"] = $seconds;
                } else if ($seconds == 0) {
                    $__auth__["timer"]["follow"] = false;
                    $__auth__["timer"]["expire"] = 0;
                    $__auth__["timer"]["seconds"] = 0;
                } else {
                    $__auth__["timer"]["expire"] = time() + $__auth__["timer"]["seconds"];
                }
            }
            $time = time();
            $remaining = ($__auth__["timer"]["expire"] ?? $time) - $time;
            if ($remaining < 0) {
                $remaining = 0;
            }
            $__auth__["timer"]["remaining"] = $remaining;
            $this->set("__auth__", $__auth__);
        }
        return $__auth__["timer"] ?? null;
    }

    /*
        Retorna um valor booleano (verdadeiro ou falso), que diz se o estado da sessão é válida ou inválida
    */
    public function auth_valid()
    {
        $timer = $this->auth_timer();
        if ($timer !== null) {
            if (!$timer["follow"]) {
                return true;
            } else if ($timer["follow"] && $timer["remaining"] > 0) {
                return true;
            }
        }
        return false;
    }

    /*
        Se a sessão for válida, o tempo restante da sessão é resetado ao ínicio
    */
    public function auth_timer_reset()
    {
        $this->auth_verify();
        $timer = $this->auth_timer();
        if ($timer !== null && $timer["remaining"] > 0) {
            $this->auth_timer($timer['seconds']);
        }
    }
}
