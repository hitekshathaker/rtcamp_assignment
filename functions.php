<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer-master/src/SMTP.php';
require_once __DIR__ . '/PHPMailer-master/src/Exception.php';



function generateVerificationCode() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

function registerEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    if (!in_array($email, file($file, FILE_IGNORE_NEW_LINES))) {
        file_put_contents($file, $email . PHP_EOL, FILE_APPEND);
    }
}

function unsubscribeEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    $emails = file($file, FILE_IGNORE_NEW_LINES);
    $emails = array_filter($emails, fn($e) => trim($e) !== trim($email));
    file_put_contents($file, implode(PHP_EOL, $emails) . PHP_EOL);
}

function sendVerificationEmail($email, $code) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'hitekshathaker6@gmail.com'; // Your Gmail
        $mail->Password = 'jxiy aomk chvi tflo';   // App password from Google
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('hitekshathaker6@gmail.com', 'XKCD Bot');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Your Verification Code';
        $mail->Body = "<p>Your verification code is: <strong>$code</strong></p>";
 
        $unsubscribeLink = "http://localhost/rtcamp/src/unsubscribe.php?email=" . urlencode($email);

        $mail->Body = "
            <p>Your verification code is: <strong>$code</strong></p>
            <p>If you want to unsubscribe, click here: <a href=\"$unsubscribeLink\">Unsubscribe</a></p>
        ";
    
        

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
    
}

function verifyCode($email, $code) {
    $codeFile = __DIR__ . '/codes/' . md5($email) . '.txt';
    if (!file_exists($codeFile)) return false;
    $storedCode = trim(file_get_contents($codeFile));
    return $storedCode === $code;
}

function saveVerificationCode($email, $code) {
    $dir = __DIR__ . '/codes/';
    if (!is_dir($dir)) mkdir($dir);
    file_put_contents($dir . md5($email) . '.txt', $code);
}

function fetchAndFormatXKCDData() {
    $randomID = rand(1, 2800); // XKCD current range
    $json = file_get_contents("https://xkcd.com/$randomID/info.0.json");
    $data = json_decode($json, true);
    return "<h2>XKCD Comic</h2>
            <img src='{$data['img']}' alt='XKCD Comic'>
            <p><a href='http://yourdomain.com/src/unsubscribe.php?email={EMAIL}' id='unsubscribe-button'>Unsubscribe</a></p>";
}

function sendXKCDUpdatesToSubscribers() {
    $file = __DIR__ . 'src\registered_emails.txt';
    $emails = file($file, FILE_IGNORE_NEW_LINES);
    foreach ($emails as $email) {
        $content = str_replace('{EMAIL}', urlencode($email), fetchAndFormatXKCDData());
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'hitekshathaker6@gmail.com';
            $mail->Password = 'jxiy aomk chvi tflo';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('hitekshathaker6@gmail.com', 'XKCD Bot');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Your XKCD Comic';
            $mail->Body = $content;
            $mail->send();
        } catch (Exception $e) {
            error_log("XKCD send error: " . $e->getMessage());
        }
    }
}

