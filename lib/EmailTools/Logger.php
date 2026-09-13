<?php
class Logger 
{
    private static $logFile = '/var/log/email_sync.log';
    
    public static function info($message) 
    {
        self::log('INFO', $message);
    }
    
    public static function error($message) 
    {
        self::log('ERROR', $message);
    }
    
    public static function warning($message) 
    {
        self::log('WARNING', $message);
    }
    
    private static function log($level, $message) 
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] {$level}: {$message}" . PHP_EOL;
        file_put_contents(self::$logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }
}