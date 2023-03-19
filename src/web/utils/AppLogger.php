<?php
namespace tryout_google_auth\web\utils;
require_once (__DIR__ ."/../../../vendor/autoload.php");


use Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler as StreamHandler;

class AppLogger{
    private $logger;
    private $stream_handler;
    
    private function initializeLogger($loggerName){
        $this->logger = new Logger($loggerName);
        $this->stream_handler = new StreamHandler("/var/log/tryout-google-auth.log", Logger::DEBUG);
        $this->logger->pushHandler($this->stream_handler);
    }
    
    public function __construct($loggerName = "Logger"){
        $this->initializeLogger($loggerName);
    }
    
    public function writeLog($message){
        $this->logger->debug($message);
    }
}

