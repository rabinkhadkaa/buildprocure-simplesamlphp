<?php
if (!function_exists('logSSOFlow')) {
    function logSSOFlow($message) {

        // Correct log file path
        $logFile = __DIR__ . '/../log/sso_flow_log.log';

        // Ensure log directory exists
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }

        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message" . PHP_EOL;

        file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }
}
?>
