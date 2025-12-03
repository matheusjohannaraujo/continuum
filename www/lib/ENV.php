<?php

namespace Lib;

use MJohann\Packlib\DataManager;

class ENV
{
    private $raw = "";
    private $env = [];
    private $pathEnv = null;

    public function __construct()
    {
        $this->pathEnv = __DIR__ . '/../.env';
    }

    public function get(?string $key = null, $default_value = null)
    {
        if ($key === null) {
            return $this->env ?? $default_value;
        }
        return $this->env[$key] ?? $default_value;
    }

    public function raw(): string
    {
        return $this->raw;
    }

    /**
     * Lê o arquivo .env, realiza a substituição de variáveis referenciadas
     * e retorna o array carregado.
     */
    public function read(?string $customPath = null): array
    {
        $envContext = getenv() ?: [];
        $path = $customPath ?? $this->pathEnv;

        // Cria o .env se não existir
        $this->handleEnvFileCreation($path, $envContext);

        $this->raw = "";
        $envFileContent = DataManager::fileRead($path, 3);
        $parsedEnv = [];

        foreach ($envFileContent as $line) {
            $this->raw .= $line;
            $line = trim($line);

            // Ignora linhas vazias ou comentadas
            if ($line === '' || strpos($line, '#') === 0) {
                continue;
            }

            // Separa em 2 partes no "=", ignorando linhas sem "="
            $parts = explode('=', $line, 2);
            if (count($parts) < 2) {
                continue;
            }

            $key = trim($parts[0]);
            $value = trim($parts[1]);

            // Remove aspas duplas ou simples do início/fim, se houver
            if (preg_match('/^"(.*)"$/', $value, $matches)) {
                $value = $matches[1];
            } elseif (preg_match("/^'(.*)'$/", $value, $matches)) {
                $value = $matches[1];
            }

            // Converte string para tipo (bool, int etc.)
            $parsedEnv[$key] = string_to_type($value);
        }

        // Mescla variáveis do sistema
        $this->env = array_merge($parsedEnv, $envContext);

        // Expande referências do tipo ${VARIAVEL}
        $this->env = $this->expandEnvVariables($this->env);

        return $this->env;
    }

    /**
     * Escreve as variáveis no arquivo .env
     */
    public function write(array $data = [], ?string $customPath = null): bool
    {
        $path = $customPath ?? $this->pathEnv;
        $this->pathEnv = $path;

        $lines = [];
        foreach ($data as $key => $value) {
            $lines[] = trim($key) . '=' . trim(type_to_string($value));
        }

        $envString = implode("\r\n", $lines) . "\r\n";

        if (DataManager::fileWrite($path, $envString)) {
            $this->read($path);
            return true;
        }
        return false;
    }

    /**
     * Verifica se algumas chaves obrigatórias existem no .env
     */
    public function required(): void
    {
        $envKeysRequired = [
            "ENV",
            "CSRF_REGENERATE",
            "JWT_SECRET",
        ];

        $envKeys = array_keys($this->env);

        foreach ($envKeysRequired as $key) {
            if (!in_array($key, $envKeys, true)) {
                dumpd("The definition of `$key` was not found in the `.env` file");
            }
        }
    }

    /**
     * Mescla as variáveis carregadas com o $_ENV global
     */
    public function merge(): array
    {
        $_ENV = array_merge($_ENV, $this->env);
        $this->env = &$_ENV;
        return $_ENV;
    }

    /**
     * Cria o arquivo .env caso não exista, com base no .env.example,
     * e ajusta alguns valores padrão, se necessário.
     */
    private function handleEnvFileCreation(string $path, array $envContext): void
    {
        if (DataManager::exist($path)) {
            return;
        }

        $pathEnvExample = __DIR__ . '/../.env.example';
        if (!DataManager::exist($pathEnvExample)) {
            dumpd("The `.env` and `.env.example` files were not found.");
        }

        if (!DataManager::copy($pathEnvExample, '.env')) {
            dumpd("It was not possible to copy the `.env.example` file to create the `.env`.");
        }

        if (DataManager::exist($path) === "FILE") {
            $tempEnv = new ENV();
            $arrayFromEnv = $tempEnv->read();

            // Remove variáveis que já existam no contexto do sistema
            foreach ($arrayFromEnv as $key => $value) {
                if (isset($envContext[$key])) {
                    unset($arrayFromEnv[$key]);
                }
            }

            // Ajusta chaves padrão se necessário
            if ($tempEnv->get("AES_256_SECRET") === "password12345") {
                $arrayFromEnv["AES_256_SECRET"] = hash_generate(uniqid());
            }
            if ($tempEnv->get("JWT_SECRET") === "password12345") {
                $arrayFromEnv["JWT_SECRET"] = hash_generate(uniqid());
            }
            if (empty($tempEnv->get("APP_URL"))) {
                $arrayFromEnv["APP_URL"] = !empty(site_url()) ? site_url() : "http://localhost/";
            }

            $tempEnv->write($arrayFromEnv);
        }
    }

    /**
     * Faz a substituição de variáveis no formato ${VARIAVEL} pelo valor contido em $this->env
     *
     * @param  array $env Array de variáveis de ambiente já parseadas
     * @return array      Array com as referências substituídas
     */
    private function expandEnvVariables(array $env): array
    {
        // Para cada chave, substitui referências dentro do valor, se for string
        foreach ($env as $key => $value) {
            if (is_string($value)) {
                $env[$key] = preg_replace_callback(
                    '/\${([^}]+)}/',
                    function ($matches) use ($env) {
                        $varName = $matches[1];
                        // Se existir no env, substitui; caso contrário, mantém o original.
                        return $env[$varName] ?? $matches[0];
                    },
                    $value
                );
            }
        }

        return $env;
    }
}
