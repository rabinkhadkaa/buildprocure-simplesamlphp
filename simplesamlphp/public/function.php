<?php
if (!function_exists('logSSOFlow')) {
    function logSSOFlow($message) {

       
    $logDir = "/var/www/app/simplesamlphp/log";
    $logFile = "$logDir/sso_flow_log.log";

    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }

    $timestamp = date("Y-m-d H:i:s");
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND | LOCK_EX);
    }
}
?>
