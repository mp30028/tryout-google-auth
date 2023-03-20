<?php
namespace tryout_google_auth\web\authentication;

require_once (__DIR__ . '/../../../vendor/autoload.php');
require_once (__DIR__ . '/../utils/AppLogger.php');

use Google\Client as GClient;
use tryout_google_auth\web\utils\AppLogger as AppLogger;

//Test URL: http://localhost/tryout-google-auth/web/page01.php

class LoginHandler{
//     private const LOGIN_HANDLER = "login.php";
    public const REDIRECT_URL_KEY = "redirect_url";
    private const CREDENTIALS_FILEPATH = '/../assets/credentials.json';
    private const APIKEY_FILEPATH = '/../assets/apiKey';
    private const LOGIN_PAGE = "/tryout-google-auth/web/authentication/login.php";
    private const SCOPES = 'email';
    private const CREDENTIAL_KEY = 'credential';
    private const USERNAME_KEY = 'email';    
    private AppLogger $appLogger;

    public function __construct() {
        $this->startSession();
        $this->username = NULL;
        $this->appLogger = new AppLogger(__CLASS__);
    }
    
    public function authenticate():bool{
        if ($this->isLogoutRequested()){
            return $this->doLogout();    
        }else{
            if ($this->isAuthenticated()){
                $this->appLogger->writeLog("Starting isAuthenticated flow");
                return true;
            }else if ($this->isAuthenticating()){
                $this->appLogger->writeLog("Starting isAuthenticating flow");
                return $this->doAuthentication();
            }else{
                $this->appLogger->writeLog("Starting Login From Html flow");
                $this->redirectToLoginHtml();
            }
        }
    }
    
    private function isLogoutRequested(){
        return (isset($_REQUEST['logout']));
    }
    
    private function doLogout():bool{
        unset($_SESSION[self::CREDENTIAL_KEY]);
        unset($_SESSION[self::USERNAME_KEY]);
        $client = $this->getGoogleClient();
        $client->revokeToken();
        $this->redirectToLoginHtml();
        return true;
    }
    
    public function getUsername():string{
        return $_SESSION[self::USERNAME_KEY];
    }
    
    private function redirectToLoginHtml(){       
        $authUrl =  $this->getCurrentProtocol() . '://' .                        
                    $_SERVER['HTTP_HOST'] .                    
                    self::LOGIN_PAGE;               
                    $_SESSION[self::REDIRECT_URL_KEY] = $this->getRedirectUrl();
        $this->appLogger->writeLog("Redirecting to login page at {$authUrl} from current page at {$this->getRedirectUrl()}");
        header("Location: " . $authUrl);
        exit();
    }
    
    private function isAuthenticated():bool{        
        return (!empty($_SESSION[self::CREDENTIAL_KEY]) && isset($_SESSION[self::CREDENTIAL_KEY]));
    }
    
    private function isAuthenticating():bool{
        return isset($_POST[self::CREDENTIAL_KEY]);
    }
    
    private function doAuthentication():bool{        
        $credential = $_POST[self::CREDENTIAL_KEY]; 
        $client = $this->getGoogleClient();
        $payload = $client->verifyIdToken($credential);                
        if ($payload) {
            $_SESSION[self::CREDENTIAL_KEY] = $credential;
            $_SESSION[self::USERNAME_KEY] = $payload[self::USERNAME_KEY];
            return TRUE;
        } else {
            unset($_SESSION[self::CREDENTIAL_KEY]);
            unset($_SESSION[self::USERNAME_KEY]);
            return FALSE;
        }
    }
    
    private function startSession(){
        if(!session_id()){
            session_start();
        }
    }
    
    private function getGoogleClient(){
        $client = new GClient();
        $client->setAuthConfig($this->getCredentials());
        $client->setRedirectUri($this->getRedirectUrl());
        $client->setScopes(self::SCOPES);
        $client->setDeveloperKey($this->getApiKey());
        return $client;
    }
    
    private function getRedirectUrl(){   
        $redirectUrl =  $this->getCurrentProtocol() . '://' . 
                        $_SERVER['HTTP_HOST'] . 
                        $_SERVER['PHP_SELF'];
        return $redirectUrl;
    }
        
    private function getCurrentProtocol(){
        $protocol = 'http'.(!empty($_SERVER['HTTPS']) ? 's' : '');
        return $protocol;
    }
    
    private function getCurrentPath(){
        $path = substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/'));
        return $path;
    }
    
    private function getCredentials(){
        $credentials = __DIR__ . self::CREDENTIALS_FILEPATH;        
        if (file_exists($credentials)) {
            return $credentials;
        }        
        return false;
    }
    
    private function getApiKey(){
        $file = __DIR__ . self::APIKEY_FILEPATH;
        if (file_exists($file)) {
            return file_get_contents($file);
        }
    }
}

