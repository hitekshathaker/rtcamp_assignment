<?php
require_once 'functions.php';

$msg = '';
$email = '';

// Step 1: Handle the GET request with the email parameter
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['email'])) {
    $email = filter_var(trim($_GET['email']), FILTER_VALIDATE_EMAIL);
    if ($email) {
        // If the email is valid, inform the user that they need to enter the code
        $msg = "Please enter the verification code sent to your email to unsubscribe.";
    } else {
        // If email is invalid, show an error message
        $msg = "Invalid email address.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Step 2: Handle the POST request for verification code and unsubscription
    if (isset($_POST['verification_code'], $_POST['unsubscribe_email'])) {
        $email = $_POST['unsubscribe_email'];
        $code = trim($_POST['verification_code']);

        // Step 3: Verify the code and unsubscribe if correct
        if (verifyCode($email, $code)) {
            unsubscribeEmail($email); // Unsubscribe the email
            $msg = "You have been unsubscribed successfully.";
        } else {
            $msg = "Incorrect verification code.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Unsubscribe</title>
    <style>
        body { font-family: Arial; max-width: 500px; margin: auto; padding: 20px; }
        input, button { margin: 10px 0; width: 100%; padding: 10px; }
        .message { padding: 15px; background-color: #f4f4f4; border: 1px solid #ccc; margin-top: 20px; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
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
<body>
    <h2>Unsubscribe from XKCD Comics</h2>

    <?php if ($msg): ?>
        <p class="<?= strpos($msg, 'success') !== false ? 'success' : 'error' ?>"><?= htmlspecialchars($msg) ?></p>
    <?php endif; ?>

    <?php if ($email): ?>
        <!-- Show form to enter the verification code for unsubscription -->
        <form method="POST">
            <input type="hidden" name="unsubscribe_email" value="<?= htmlspecialchars($email) ?>">
            <input type="text" name="verification_code" maxlength="6" placeholder="Enter verification code" required>
            <button type="submit">Unsubscribe</button>
        </form>
    <?php else: ?>
        <!-- Inform the user to visit the link with their email to unsubscribe -->
        <p>Please use the link with your email to unsubscribe.</p>
    <?php endif; ?>

    <p><a href="index.php">Back to Subscription Page</a></p>
</body>
</html>
