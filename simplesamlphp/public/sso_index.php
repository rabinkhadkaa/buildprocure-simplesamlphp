<?php


// Correct path to autoload
require_once __DIR__ . '/../vendor/autoload.php';

try {
    $as = new \SimpleSAML\Auth\Simple('auth0');

    if ($as->isAuthenticated()) {
        $attributes = $as->getAttributes();

        include_once  __DIR__ .'/function.php'; // Logging helper
        $message = "Received Attributes:\n";
        logSSOFlow($message . print_r($attributes, true));

        include_once __DIR__ . '/../../_dbconnect.php'; // Database connection
         // Save Attributes to a variable or process as needed
        $user_id = $attributes['http://schemas.auth0.com/user_id'][0] ?? '';
        $email = $attributes['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress'][0] ?? '';
        $username = $attributes['http://schemas.auth0.com/username'][0] ?? '';
        $roles = isset($attributes['http://schemas.auth0.com/roles']) ? implode(',', $attributes['http://schemas.auth0.com/roles']) : '';
        $connection = $attributes['http://schemas.auth0.com/identities/default/connection'][0] ?? '';
        $provider = $attributes['http://schemas.auth0.com/identities/default/provider'][0] ?? '';
        $created_at = isset($attributes['http://schemas.auth0.com/created_at'][0]) ? date('Y-m-d H:i:s', strtotime($attributes['http://schemas.auth0.com/created_at'][0])) : null;
        $updated_at = isset($attributes['http://schemas.auth0.com/updated_at'][0]) ? date('Y-m-d H:i:s', strtotime($attributes['http://schemas.auth0.com/updated_at'][0])) : null;
        $last_password_reset = isset($attributes['http://schemas.auth0.com/last_password_reset'][0]) ? date('Y-m-d H:i:s', strtotime($attributes['http://schemas.auth0.com/last_password_reset'][0])) : null;
        $email_verified = isset($attributes['http://schemas.auth0.com/email_verified'][0]) ? (int)$attributes['http://schemas.auth0.com/email_verified'][0] : 0;
        $phone_number = $attributes['http://schemas.auth0.com/phone_number'][0] ?? null;
        $phone_verified = isset($attributes['http://schemas.auth0.com/phone_verified'][0]) ? (int)$attributes['http://schemas.auth0.com/phone_verified'][0] : 0;
        $nickname = $attributes['http://schemas.auth0.com/nickname'][0] ?? null;
        $picture = $attributes['http://schemas.auth0.com/picture'][0] ?? null;
        $raw_attributes = json_encode($attributes, JSON_UNESCAPED_SLASHES);

       
        $stmt = $pdo->prepare("
            INSERT INTO saml_users
            (user_id, email, username, roles, connection, provider, created_at, updated_at, last_password_reset, email_verified, phone_number, phone_verified, nickname, picture, raw_attributes)
            VALUES
            (:user_id, :email, :username, :roles, :connection, :provider, :created_at, :updated_at, :last_password_reset, :email_verified, :phone_number, :phone_verified, :nickname, :picture, :raw_attributes)
            ON DUPLICATE KEY UPDATE
                email = VALUES(email),
                username = VALUES(username),
                roles = VALUES(roles),
                connection = VALUES(connection),
                provider = VALUES(provider),
                updated_at = VALUES(updated_at),
                last_password_reset = VALUES(last_password_reset),
                email_verified = VALUES(email_verified),
                phone_number = VALUES(phone_number),
                phone_verified = VALUES(phone_verified),
                nickname = VALUES(nickname),
                picture = VALUES(picture),
                raw_attributes = VALUES(raw_attributes)
        ");
        $stmt->execute([
            ':user_id' => $user_id,
            ':email' => $email,
            ':username' => $username,
            ':roles' => $roles,
            ':connection' => $connection,
            ':provider' => $provider,
            ':created_at' => $created_at,
            ':updated_at' => $updated_at,
            ':last_password_reset' => $last_password_reset,
            ':email_verified' => $email_verified,
            ':phone_number' => $phone_number,
            ':phone_verified' => $phone_verified,
            ':nickname' => $nickname,
            ':picture' => $picture,
            ':raw_attributes' => $raw_attributes,
        ]);

        $message = "User $username (ID: $user_id) processed and stored in database.";
        logSSOFlow($message);

        // Redirect to sso.php
        header('Location: https://buildprocure.com/sso.php?username=' . urlencode($username));
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
     <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            height: 100vh;
            margin: 0;
        }
        .left, .right {
            flex: 1;
            padding: 50px;
        }
        .left {
            background-color: #f0f0f0;
        }
        .right {
            background-color: #fff;
            border-left: 1px solid #ccc;
        }
        form {
            max-width: 400px;
            margin: auto;
        }
        input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
        }
        button {
            padding: 12px;
            background-color: #1e3a8a;
            color: white;
            border: none;
            width: 100%;
            cursor: pointer;
        }
        button:hover {
            background-color: #0f296c;
        }
    </style>
</head>
<body>
 <div class="left">
    <h1>Welcome to BuildProcure simplesamlphp SSO</h1>
    <p>Enter your work email to be routed to the correct login page for your organization.</p>
</div>
<div class="right">
<form method="POST">
    <input type="email" name="email" placeholder="you@yourcompany.com" required>
    <button type="submit">Continue to SSO</button>
</form>
</div>
<?php if (!empty($error)) echo "<p style='color:red'>$error</p>"; ?>
</body>
</html>

<?php
} catch (Exception $e) {
    echo 'Error: ' . htmlspecialchars($e->getMessage());
}
