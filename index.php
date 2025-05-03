<?php
require_once 'functions.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email'])) {
        $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
        if ($email) {
            $code = generateVerificationCode();
            saveVerificationCode($email, $code);
            if (sendVerificationEmail($email, $code)) {
                $msg = "Verification code sent to $email.";
            } else {
                $msg = "Failed to send verification code. Try again.";
            }
        } else {
            $msg = "Invalid email format.";
        }
    } elseif (isset($_POST['verification_code'], $_POST['verify_email'])) {
        $email = $_POST['verify_email'];
        $code = trim($_POST['verification_code']);
        if (verifyCode($email, $code)) {
            registerEmail($email);
            $msg = "Email verified and registered!";
        } else {
            $msg = "Incorrect verification code.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>XKCD Subscribe</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            max-width: 500px;
            margin: auto;
            padding: 20px;
            color: #333;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        input[type="email"],
        input[type="text"] {
            padding: 10px;
            width: 100%;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .success {
            color: green;
            text-align: center;
            margin-top: 20px;
        }

        .error {
            color: red;
            text-align: center;
            margin-top: 20px;
        }

        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <h2>Subscribe to XKCD Comics</h2>

    <?php if ($msg): ?>
        <p class="<?= strpos($msg, 'success') !== false ? 'success' : 'error' ?>"><?= $msg ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Enter your email" required>
        <button type="submit" id="submit-email">Submit</button>
    </form>

    <form method="POST">
        <input type="hidden" name="verify_email" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
        <input type="text" name="verification_code" maxlength="6" placeholder="Enter code" required>
        <button type="submit" id="submit-verification">Verify</button>
    </form>
</body>
</html>
