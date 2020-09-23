<?php

/*
	GitHub: https://github.com/matheusjohannaraujo/aes_256_cbc_or_gcm
	Country: Brasil
	State: Pernambuco
	Developer: Matheus Johann Araujo
	Date: 2020-11-20
*/

namespace Lib;

class AES_256 {

    // The key defined here will be used in the AES 256 CBC and AES 256 GCM methods

    private $key = "AES_256";

    public function setKey(string $key)
    {
        $this->key = $key;
        return $this;
    }

    public function getKey()
    {
        return $this->key;
    }  

    // Below are the methods for working with AES 256 with CBC

    private function enc_cbc(string &$text, string $key) {
        $key = substr(hash('sha256', $key, true), 0, 32);
        $cipher = 'aes-256-cbc';
        $iv_len = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($iv_len);
        $text = openssl_encrypt(base64_encode($text), $cipher, $key, OPENSSL_RAW_DATA, $iv);
        return bin2hex(base64_encode($iv . $text));
    }
    
    private function dec_cbc(string &$text, string $key) {
        $key = substr(hash('sha256', $key, true), 0, 32);
        $cipher = 'aes-256-cbc';
        $iv_len = openssl_cipher_iv_length($cipher);
        $text = base64_decode(hex2bin($text));
        $iv = substr($text, 0, $iv_len);
        $text = substr($text, $iv_len);    
        return base64_decode(openssl_decrypt($text, $cipher, $key, OPENSSL_RAW_DATA, $iv));
    }

    public function encrypt_cbc(string $text) :string
    {
        return $this->enc_cbc($text, $this->key);
    }

    public function decrypt_cbc(string $text) :string
    {
        return $this->dec_cbc($text, $this->key);
    }
 
    // Below are the methods for working with AES 256 with GCM

    private function enc_gcm(string &$text, string $key, string &$tag) {
        $key = substr(hash('sha256', $key, true), 0, 32);
        $cipher = 'aes-256-gcm';
        $iv_len = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($iv_len);
        $tag_length = 16;
        $text = openssl_encrypt(base64_encode($text), $cipher, $key, OPENSSL_RAW_DATA, $iv, $tag, "", $tag_length);
        return bin2hex(base64_encode($iv . $tag. $text));
    }
    
    private function dec_gcm(string &$text, string $key, string $tag) {
        if (empty($tag)) {
            return null;
        }
        $tag = hex2bin($tag);
        $key = substr(hash('sha256', $key, true), 0, 32);
        $cipher = 'aes-256-gcm';
        $iv_len = openssl_cipher_iv_length($cipher);
        $tag_length = 16;
        $text = base64_decode(hex2bin($text));
        $iv = substr($text, 0, $iv_len);
        $tag = substr($text, $iv_len, $tag_length);
        $text = substr($text, $iv_len + $tag_length);    
        return base64_decode(openssl_decrypt($text, $cipher, $key, OPENSSL_RAW_DATA, $iv, $tag));
    }

    public function encrypt_gcm(string $text, string &$tag) :string
    {
        $text = $this->enc_gcm($text, $this->key, $tag);
        $tag = bin2hex($tag);
        return $text;
    }

    public function decrypt_gcm(string $text, string $tag) :string
    {
        return $this->dec_gcm($text, $this->key, $tag);
    }

}
