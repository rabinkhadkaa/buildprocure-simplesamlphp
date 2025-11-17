<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Correct path to autoload
require_once __DIR__ . '/../vendor/autoload.php';

try {
    $as = new \SimpleSAML\Auth\Simple('auth0');

    if ($as->isAuthenticated()) {
        $attributes = $as->getAttributes();
        echo "<pre>" . htmlspecialchars(print_r($attributes, true)) . "</pre>";
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['email'])) {
        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

        if ($email) {
            \SimpleSAML\Session::getSessionFromRequest()
                ->setData('sso', 'email', $email);

            $as->requireAuth();
            exit;
        } else {
            $error = "Invalid email address.";
        }
    }
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
    echo 'Error: ' . htmlspecialchars($e->getMessage());
}
