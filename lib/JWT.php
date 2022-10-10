<?php

/*
	GitHub: https://github.com/matheusjohannaraujo/continuum
	Country: Brasil
	State: Pernambuco
	Developer: Matheus Johann Araujo
	Date: 2021-03-24
*/

// Implementado apartir dos sites abaixo
// https://imasters.com.br/back-end/entendendo-o-jwt
// https://dev.to/robdwaller/how-to-create-a-json-web-token-using-php-3gml
// https://www.jsonwebtoken.io
// https://tools.ietf.org/html/rfc7519#section-4.1

namespace Lib;

class JWT
{

    // alg - Algoritmo usado na criação da assinatura, no caso HS256 (HMAC SHA-256)
    // typ - Tipo do token, no caso JWT
    // iss - O domínio da aplicação geradora do token
    // sub - É o assunto do token, mas é muito utilizado para guarda o ID do usuário
    // aud - Define quem pode usar o token
    // exp - Data para expiração do token
    // nbf - Define uma data para qual o token não pode ser aceito antes dela
    // iat - Data de criação do token
    // jti - O id do token
    // name - Nome do usuário
    private $hash = "sha256"; // Algoritmo de criptografia utilizado na assinatura do token, padrão HMAC SHA-256   
    private $valid = false; // Guarda o estado atual de validação do token
    private $token = null; // Token Encoded Base64
    private $secret = null; // Chave de criação da assinatura do token
    private $header = null; // Cabeçalho do token
    private $payload = null; // Corpo do token
    private $signature = null; // Assinatura do token   

    public function __construct(string $token = null)
    {        
        $this->secret(input_env("JWT_SECRET", "JWT"));
        if ($token !== null) {
            $this->token($token);
            return;
        }
        $this->valid = false;
        $this->hash("sha256");
        $this->header([
            "alg" => "HS256",
            "typ" => "JWT",
        ]);
        $time = time();
        $uid = md5(uniqid() . hash_generate(uniqid()));
        $this->payload([
            "iss" => "localhost",
            "sub" => "JWT Credential",
            "aud" => "http://localhost/continuum",
            "iat" => $time,
            "exp" => $time + (60 * 30), // Meia hora de validade para o token
            "jti" => $uid,
            "name" => "Continuum",
        ]);
    }

    public function hash(string $value = null) :?string
    {
        if ($value !== null) {
            $this->hash = $value;
        }
        return $this->hash;
    }

    public function secret(string $value = null) :?string
    {
        if ($value !== null) {
            $this->secret = $value;
        }
        return $this->secret;
    }

    public function header(array $value = null) :?array
    {
        if ($value !== null) {
            $this->header = $value;
        }
        return $this->header;
    }

    private function headerKV(string $key, $value = null)
    {
        if ($value !== null) {
            $this->header[$key] = $value;
        }
        return $this->header[$key] ?? null;
    }

    public function alg(string $value = null) :?string
    {
        return $this->headerKV("alg", $value);
    }

    public function typ(string $value = null) :?string
    {
        return $this->headerKV("typ", $value);
    }

    public function payload($body = null)
    {
        if (is_array($body)) {
            $this->payload = $body;
        } else if (is_string($body)) {
            return $this->payload[$body] ?? null;
        }
        return $this->payload;
    }

    private function payloadKV(string $key, $value)
    {
        if ($value === null) {// Remove Claim Value
            unset($this->payload[$key]);
        } else if ($value !== "") {// Set Claim Value
            $this->payload[$key] = $value;
        }
        return $this->payload[$key] ?? null;// Return Clain Value
    }

    public function iat(?int $value = 0) :?int
    {
        $value = (($value === null) ? null : ($value > 0 ? $value : ""));
        return $this->payloadKV("iat", $value);
    }

    public function nbf(?int $value = 0) :?int
    {
        $value = (($value === null) ? null : ($value > 0 ? $value : ""));
        return $this->payloadKV("nbf", $value);
    }

    public function exp(?int $value = 0) :?int
    {
        $value = (($value === null) ? null : ($value > 0 ? $value : ""));
        return $this->payloadKV("exp", $value);
    }

    public function iss(?string $value = "") :?string
    {
        return $this->payloadKV("iss", $value);
    }

    public function sub(?string $value = "") :?string
    {
        return $this->payloadKV("sub", $value);
    }

    public function aud(?string $value = "") :?string
    {
        return $this->payloadKV("aud", $value);
    }    

    public function jti(?string $value = "") :?string
    {
        return $this->payloadKV("jti", $value);
    }

    public function name(?string $value = "") :?string
    {
        return $this->payloadKV("name", $value);
    }

    public function claim(string $key, $value = "")
    {
        return $this->payloadKV($key, $value);
    }

    public function signature() :string
    {
        return $this->signature;
    }

    private function encode() :void
    {
        $header = base64_url_encode(json_encode($this->header()));
        $payload = base64_url_encode(json_encode($this->payload()));
        $this->signature = base64_url_encode(hash_hmac($this->hash, "$header.$payload", $this->secret, true));
        $this->token = "$header.$payload.$this->signature";
    }

    private function decode() :void
    {
        $parts = explode(".", $this->token);
        if (count($parts) == 3) {
            $this->header(json_decode(base64_url_decode($parts[0]), true));
            $this->payload(json_decode(base64_url_decode($parts[1]), true));
            $this->signature = $parts[2];
        }
    }

    public function token(string $value = null) :?string
    {
        if ($value !== null) {
            $this->token = $value;
            $this->decode();
        } else {
            $this->encode();            
        }
        $this->valid();
        return $this->token;
    }

    public function valid() :bool
    {
        $this->valid = false;
        $header = base64_url_encode(json_encode($this->header()));
        $payload = base64_url_encode(json_encode($this->payload()));
        $signature = base64_url_encode(hash_hmac($this->hash, "$header.$payload", $this->secret, true));
        if ($this->token == "$header.$payload.$signature") {
            $nbf = $this->payload("nbf");
            $exp = $this->payload("exp");
            if (empty($nbf) && empty($exp)) {// NBF, EXP = empty
                return $this->valid = true;
            }
            $time = time();
            if (!empty($nbf) && empty($exp) && $time > $nbf) {// EXP = empty
                return $this->valid = true;
            }
            if (empty($nbf) && !empty($exp) && $time < $exp) {// NBF = empty
                return $this->valid = true;
            }
            if (!empty($nbf) && !empty($exp) && $time > $nbf && $time < $exp) {// NBF, EXP = not empty
                return $this->valid = true;
            }            
        }
        return $this->valid;
    }

    public function __toString()
    {
        return $this->token();
    }

}
