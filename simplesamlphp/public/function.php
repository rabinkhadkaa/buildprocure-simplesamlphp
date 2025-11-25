<?php
if(!function_exists('logSSOFlow')){
function logSSOFlow($message, $logFile = '/var/www/app/simplesamlphp/log/sso_flow_log.log') {
    // Ensure the logs directory exists
    if (!is_dir(dirname($logFile))) {
        mkdir(dirname($logFile), 0755, true);
    }

    // Add a timestamp to the log message
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message" . PHP_EOL;

    // Write the log message to the file
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}
}
?>
