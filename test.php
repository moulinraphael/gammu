<?php

require('functions.php');
define('SALT', 'salt');

if (empty($_POST['number']) ||
        !isset($_POST['message']) ||
        empty($_POST['signature']) ||
        $_POST['signature'] != md5(SALT.$_POST['number'].$_POST['message']))
        die('0');

define('SALT_SEND', 'salt');
define('URL_SEND', 'http://localhost/sendsms.php');

function sendSms($number, $message) {
    $signature = md5(SALT_SEND.$number.$message);
    echo sendPost(URL_SEND, [
            'number' => $number,
            'message' => $message,
            'signature' => $signature]);
}

sendSms($_POST['number'], "Merci pour ce message !!\nVoici le contenu : ".$_POST['message']);
