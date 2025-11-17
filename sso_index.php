<?php
// No session_start() or echo before this point!

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Load SimpleSAMLphp autoload
require_once __DIR__ . '/simplesamlphp/vendor/autoload.php';

// Optional logging helper
include_once __DIR__ . '/lib/function.php'; // if you have your own logging functions

try {
    //writeLog('SSO index accessed.');

    // Initialize SimpleSAML SP
    $as = new \SimpleSAML\Auth\Simple('auth0');

    // Already logged in → show attributes
    if ($as->isAuthenticated()) {
       // writeLog('User is already authenticated.');
        $attributes = $as->getAttributes();

        echo "<pre>" . htmlspecialchars(print_r($attributes, true)) . "</pre>";
        exit;
    }

    // If user submits email → start SAML login
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['email'])) {
        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

        if ($email) {
           // writeLog("Processing login for email: $email");

            // Optional: store email for your app
            \SimpleSAML\Session::getSessionFromRequest()->setData('sso', 'email', $email);

            //writeLog('Redirecting to Auth0 SP.');

            // Redirect to Auth0 SAML login
            $as->requireAuth([]);
            exit;
        } else {
            $error = "Invalid email address.";
        }
    }

    // HTML form for email input
    ?>
    <!doctype html>
    <html>
    <head>
        <title>BuildProcure SSO Login</title>
    </head>
    <body>
        <form method="POST">
            <input type="email" name="email" placeholder="you@yourcompany.com" required>
            <button type="submit">Continue to SSO</button>
        </form>
        <?php if (!empty($error)) echo "<p style='color:red'>$error</p>"; ?>
    </body>
    </html>
    <?php

} catch (Exception $e) {
    error_log('SSO error: ' . $e->getMessage());
    echo 'Error: ' . htmlspecialchars($e->getMessage());
}
