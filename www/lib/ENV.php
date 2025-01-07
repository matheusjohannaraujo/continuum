<?php

namespace Lib;

use Lib\DataManager;

/**
 * Classe responsável por gerenciar variáveis de ambiente através de arquivo .env.
 */
class ENV
{
    /**
     * @var string Armazena o conteúdo cru (raw) do arquivo .env
     */
    private $raw = "";

    /**
     * @var array Armazena as variáveis de ambiente carregadas
     */
    private $env = [];

    /**
     * @var string Caminho padrão para o arquivo .env
     */
    private $pathEnv = null;

    /**
     * Construtor: Define o caminho padrão do arquivo .env
     */
    public function __construct()
    {
        $this->pathEnv = __DIR__ . '/../.env';
    }

    /**
     * Retorna o valor de uma variável de ambiente específica.
     * Se nenhum parâmetro for passado, retorna o array completo de variáveis.
     *
     * @param  string|null $key           Chave da variável de ambiente
     * @param  mixed|null  $default_value Valor padrão caso a chave não exista
     * @return mixed
     */
    public function get(string $key = null, $default_value = null)
    {
        if ($key === null) {
            return $this->env ?? $default_value;
        }
        return $this->env[$key] ?? $default_value;
    }

    /**
     * Retorna o conteúdo cru do arquivo .env
     *
     * @return string
     */
    public function raw(): string
    {
        return $this->raw;
    }

    /**
     * Lê o arquivo .env e carrega as variáveis em $this->env.
     * Caso não exista, tenta criar o arquivo a partir de um .env.example.
     *
     * @param  string|null $customPath Caminho personalizado para o arquivo .env
     * @return array                   Retorna o array de variáveis de ambiente carregadas
     */
    public function read(string $customPath = null): array
    {
        $envContext = getenv() ?: [];  // Garantindo que seja array
        $path = $customPath ?? $this->pathEnv;

        // Se o arquivo .env não existir, tenta criar a partir de .env.example
        $this->handleEnvFileCreation($path, $envContext);

        // Inicia leitura do arquivo .env
        $this->raw = "";
        $envFileContent = DataManager::fileRead($path, 3);
        $parsedEnv = [];

        foreach ($envFileContent as $line) {
            $this->raw .= $line;
            $line = trim($line);

            // Identifica linhas que possuem "=" e não estejam comentadas
            if ($line !== "" && strpos($line, '#') !== 0 && strpos($line, '=') !== false) {
                $parts = explode('=', $line, 2); // separa apenas na primeira ocorrência
                $key = trim($parts[0]);
                $value = isset($parts[1]) ? trim($parts[1]) : "";

                if ($key !== "") {
                    // Converte string no tipo mais adequado (int, bool, etc.)
                    $parsedEnv[$key] = string_to_type($value);
                }
            }
        }

        // Mescla variáveis de ambiente do sistema (envContext) com as do arquivo
        $this->env = array_merge($parsedEnv, $envContext);

        return $this->env;
    }

    /**
     * Escreve no arquivo .env as chaves e valores do array informado.
     *
     * @param  array       $data       Array associativo contendo chaves e valores a serem escritos
     * @param  string|null $customPath Caminho personalizado para o arquivo .env
     * @return bool                    Retorna verdadeiro caso escrita e releitura ocorram com sucesso
     */
    public function write(array $data = [], string $customPath = null): bool
    {
        $path = $customPath ?? $this->pathEnv;
        $this->pathEnv = $path;  // Atualiza o path se for customizado

        $lines = [];
        foreach ($data as $key => $value) {
            $lines[] = trim($key) . '=' . trim(type_to_string($value));
        }

        $envString = implode("\r\n", $lines) . "\r\n";

        // Grava o arquivo e depois lê novamente para atualizar as variáveis em memória
        if (DataManager::fileWrite($path, $envString)) {
            $this->read($path);
            return true;
        }
        return false;
    }

    /**
     * Verifica se chaves mínimas obrigatórias estão presentes no arquivo .env
     * Caso alguma não exista, é disparado um dumpd() (para debug).
     *
     * @return void
     */
    public function required(): void
    {
        $envKeysRequired = [
            "ENV",
            "CSRF_REGENERATE",
            "JWT_SECRET",
            // Adicione demais chaves se necessário
        ];

        $envKeys = array_keys($this->env);

        foreach ($envKeysRequired as $key) {
            if (!in_array($key, $envKeys, true)) {
                dumpd("The definition of `$key` was not found in the `.env` file");
            }
        }
    }

    /**
     * Mescla as variáveis do objeto atual ($this->env) com o $_ENV global,
     * sobrescrevendo as anteriores em $_ENV.
     *
     * @return array Retorna o novo estado de $_ENV após a mescla
     */
    public function merge(): array
    {
        $_ENV = array_merge($_ENV, $this->env);
        $this->env = &$_ENV; // Aponta o array local para o global
        return $_ENV;
    }

    /**
     * Verifica se o arquivo .env existe, caso contrário, cria a partir de .env.example
     * e ajusta valores iniciais de algumas variáveis (por exemplo, secrets padrão).
     *
     * @param  string $path       Caminho do arquivo .env
     * @param  array  $envContext Variáveis de ambiente do sistema
     * @return void
     */
    private function handleEnvFileCreation(string $path, array $envContext): void
    {
        if (DataManager::exist($path)) {
            // Arquivo já existe, não faz nada
            return;
        }

        $pathEnvExample = __DIR__ . '/../.env.example';
        if (!DataManager::exist($pathEnvExample)) {
            dumpd("The `.env` and `.env.example` files were not found.");
        }

        // Tenta copiar .env.example para .env
        if (!DataManager::copy($pathEnvExample, '.env')) {
            dumpd("It was not possible to copy the `.env.example` file to create the `.env`.");
        }

        // Se o arquivo foi copiado com sucesso, ajusta chaves padrão se necessário
        if (DataManager::exist($path) === "FILE") {
            $tempEnv = new ENV();
            $arrayFromEnv = $tempEnv->read();

            // Remove variáveis que já existem em $envContext
            foreach ($arrayFromEnv as $key => $value) {
                if (isset($envContext[$key])) {
                    unset($arrayFromEnv[$key]);
                }
            }

            // Ajusta secrets padrão se ainda estiverem nos valores de exemplo
            if ($tempEnv->get("AES_256_SECRET") === "password12345") {
                $arrayFromEnv["AES_256_SECRET"] = hash_generate(uniqid());
            }
            if ($tempEnv->get("JWT_SECRET") === "password12345") {
                $arrayFromEnv["JWT_SECRET"] = hash_generate(uniqid());
            }
            if (empty($tempEnv->get("APP_URL"))) {
                $arrayFromEnv["APP_URL"] = !empty(site_url()) ? site_url() : "http://localhost/";
            }

            // Escreve de volta as variáveis ajustadas
            $tempEnv->write($arrayFromEnv);
        }
    }
}
