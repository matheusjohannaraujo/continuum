<?php

namespace Lib;

class SimpleKeycloak {

    private static $authServerUrl = null;
    private static $realm = null;
    private static $clientId = null;
    private static $clientSecret = null;
    private static $redirectUri = null;
    public static $connection = null;
    public static $token = null;
    
    public static function config(string $authServerUrl, string $realm, string $clientId, string $clientSecret, string $redirectUri)
    {
        self::$authServerUrl = $authServerUrl;
        self::$realm         = $realm;
        self::$clientId      = $clientId;
        self::$clientSecret  = $clientSecret;
        self::$redirectUri   = $redirectUri;
    }

    public static function open(string $redirectUri = null)
    {
        if (self::$connection === null) {
            error_reporting(E_ALL ^ E_DEPRECATED ^ E_WARNING);
            self::$connection = new \Stevenmaguire\OAuth2\Client\Provider\Keycloak([
                'authServerUrl' => self::$authServerUrl,
                'realm'         => self::$realm,
                'clientId'      => self::$clientId,
                'clientSecret'  => self::$clientSecret,
                'redirectUri'   => $redirectUri ?? self::$redirectUri
            ]);
        }
        return self::$connection;
    }

    public static function close()
    {
        unset($_SESSION['keycloak_oauth2state']);
        unset($_SESSION['keycloak_token']);
        if (self::$connection !== null) {
            self::$connection = null;            
            return true;
        }        
        return false;
    }

    public static function authRedirect(string $redirectUri = null)
    {
        $provider = self::open($redirectUri);
        // If we don't have an authorization code then get one
        $authUrl = $provider->getAuthorizationUrl();
        $_SESSION['keycloak_oauth2state'] = $provider->getState();
        header('Location: ' . $authUrl);
        exit;
    }

    public static function logoutRedirect(string $redirectUri = null)
    {
        $provider = self::open($redirectUri);
        $logoutUrl = $provider->getLogoutUrl();
        self::close();
        header('Location: ' . $logoutUrl);
        exit;
    }

    public static function verifyAuthState(string $state = null, string $oauth2state = null)
    {
        if ($state === null && isset($_GET['state']) && !empty($_GET['state'])) {
            $state = $_GET['state'];
        }
        if ($oauth2state === null && isset($_SESSION['keycloak_oauth2state']) && !empty($_SESSION['keycloak_oauth2state'])) {
            $oauth2state = $_SESSION['keycloak_oauth2state'];
        }
        // Check given state against previously stored one to mitigate CSRF attack
        if ($state !== $oauth2state || $state === null && $state === $oauth2state) {
            unset($_SESSION['keycloak_oauth2state']);
            //exit('Invalid state, make sure HTTP sessions are enabled.');
            return false;
        }
        self::getToken();
        return true;
    }

    public static function getToken(string $code = null)
    {
        if ($code === null && isset($_GET['code'])) {
            $code = $_GET['code'];
        }
        if (isset($_SESSION['keycloak_token']) && !empty($_SESSION['keycloak_token'])) {
            self::$token = $_SESSION['keycloak_token'];
        }
        if (self::$token === null && $code !== null) {
            // Try to get an access token (using the authorization coe grant)
            try {
                $provider = self::open();
                self::$token = $provider->getAccessToken('authorization_code', [
                    'code' => $code
                ]);
                $_SESSION['keycloak_token'] = self::$token;
            } catch (\Exception $e) {
                exit('Failed to get access token: ' . $e->getMessage());
            }
        }
        return self::$token;
    }

    public static function getUser()
    {
         // Optional: Now you have a token you can look up a users profile data
         try {
            $provider = self::open();
            $token = self::getToken();
            if ($token !== null) {
                // We got an access token, let's now get the user's details
                return $provider->getResourceOwner($token);
            }
        } catch (\Exception $e) {
            unset($_SESSION['keycloak_oauth2state']);
            unset($_SESSION['keycloak_token']);
            exit('Failed to get resource owner: ' . $e->getMessage());
        }
        return null;
    }

    public static function refreshToken()
    {
        $provider = self::open();
        if ($provider !== null && self::$token !== null) {
            self::$token = $provider->getAccessToken('refresh_token', [
                'refresh_token' => self::$token->getRefreshToken()
            ]);
            $_SESSION['keycloak_token'] = self::$token;
            return true;
        }
        return false;
    }

}
