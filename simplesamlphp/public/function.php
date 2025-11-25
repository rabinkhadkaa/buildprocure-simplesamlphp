<?php
if (!function_exists('logSSOFlow')) {
    function logSSOFlow($msg)
    {
        $logDir = "/var/www/app/simplesamlphp/log";

        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $file = $logDir . "/sso_flow_log.log";
        file_put_contents($file, date("Y-m-d H:i:s") . " " . $msg . "\n", FILE_APPEND);
    }

}
?>
