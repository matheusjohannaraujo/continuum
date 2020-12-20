<?php

/*
	GitHub: https://github.com/matheusjohannaraujo/makemvcss
	Country: Brasil
	State: Pernambuco
	Developer: Matheus Johann Araujo
	Date: 2020-12-31
*/

// Implementado apartir dos sites abaixo
// https://imasters.com.br/back-end/entendendo-o-jwt
// https://www.jsonwebtoken.io

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
    private $hash; // Algoritmo de criptografia utilizado na assinatura do token, padrão HMAC SHA-256   
    private $valid; // Guarda o estado atual de validação do token
    private $token; // Token Encoded Base64
    private $secret; // Chave de criação da assinatura do token
    private $header; // Cabeçalho do token
    private $payload; // Corpo do token
    private $signature; // Assinatura do token   

    public function __construct($value = "")
    {
        $this->valid = false;
        $this->hash("sha256");
        $this->secret(input_env("JWT_SECRET", "JWT"));
        $this->header([
            "alg" => "HS256",
            "typ" => "JWT",
        ]);
        $time = time();
        $this->setPayload([
            "iss" => "localhost",
            "sub" => "Auth JWT System",
            "aud" => "client",
            "exp" => $time + (60 * 60), // 1 hora de validade para o token
            "nbf" => $time,
            "iat" => $time,
            "jti" => uniqid(),
            "name" => "JWTClass",
        ]);
        if ($value !== "") {
            $this->token($value);
        }
    }

    public function hash($value = "")
    {
        if ($value !== "") {
            $this->hash = $value;
            return $this;
        }
        return $this->hash;
    }

    public function secret($value)
    {
        $this->secret = $value;
        return $this;
    }

    public function header($value = "")
    {
        if ($value !== "") {
            $this->header = $value;
            return $this;
        }
        return $this->header;
    }

    private function headerKV($key, $value)
    {
        if ($value !== "") {
            $this->header[$key] = $value;
            return $this;
        }
        return $this->header[$key];
    }

    public function alg($value = "")
    {
        return $this->headerKV("alg", $value);
    }

    public function typ($value = "")
    {
        return $this->headerKV("typ", $value);
    }

    public function setPayload($value)
    {
        $this->payload = $value;
        return $this;
    }

    public function payload($key = "")
    {
        if ($key !== "") {
            return $this->payload[$key] ?? "";
        }
        return $this->payload;
    }

    private function payloadKV($key, $value)
    {
        if ($value !== "") {
            $this->payload[$key] = $value;
            return $this;
        }
        return $this->payload[$key];
    }

    public function iss($value = "")
    {
        return $this->payloadKV("iss", $value);
    }

    public function sub($value = "")
    {
        return $this->payloadKV("sub", $value);
    }

    public function aud($value = "")
    {
        return $this->payloadKV("aud", $value);
    }

    public function exp($value = "")
    {
        return $this->payloadKV("exp", $value);
    }

    public function nbf($value = "")
    {
        return $this->payloadKV("nbf", $value);
    }

    public function iat($value = "")
    {
        return $this->payloadKV("iat", $value);
    }

    public function jti($value = "")
    {
        return $this->payloadKV("jti", $value);
    }

    public function name($value = "")
    {
        return $this->payloadKV("name", $value);
    }

    public function claim($key, $value = "")
    {
        return $this->payloadKV($key, $value);
    }

    public function signature()
    {
        return $this->signature;
    }

    private function encode()
    {
        $header = base64_encode(json_encode($this->header()));
        $payload = base64_encode(json_encode($this->payload()));
        $this->signature = base64_encode(hash_hmac($this->hash, "$header.$payload", $this->secret, true));
        $this->token = "$header.$payload.$this->signature";
    }

    private function decode()
    {
        $part = explode(".", $this->token);
        if (count($part) == 3) {
            $this->header(json_decode(base64_decode($part[0]), true));
            $this->setPayload(json_decode(base64_decode($part[1]), true));
            $this->signature = $part[2];
        }
    }

    public function token($value = "")
    {
        if ($value !== "") {
            $this->token = $value;
            $this->decode();
        } else {
            $this->encode();
            $this->valid();
        }
        return $this->token;
    }

    public function valid()
    {
        $this->valid = false;
        $header = base64_encode(json_encode($this->header()));
        $payload = base64_encode(json_encode($this->payload()));
        $signature = base64_encode(hash_hmac($this->hash, "$header.$payload", $this->secret, true));
        if ($this->token == "$header.$payload.$signature") {
            $time = time();
            $iat = $this->payload("iat");
            $nbf = $this->payload("nbf");
            $exp = $this->payload("exp");
            if ($iat < $exp && $iat <= $nbf && $nbf < $exp && $time >= $iat && $time >= $nbf && $time <= $exp) {
                $this->valid = true;
            }
        }
        return $this->valid;
    }

}
