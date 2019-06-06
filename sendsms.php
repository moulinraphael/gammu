<?php

require('functions.php');

define('SEED', 'seed');

if (empty($_POST['number']) ||
	!isset($_POST['message']) ||
	empty($_POST['signature']) ||
	$_POST['signature'] != md5(SEED.$_POST['number'].$_POST['message']))
	die('0');

$pdo = new PDO('mysql:host=localhost;dbname=gammu', 'root', '');
$pdo->exec("set names 'utf8'");

$num = secure(repairNumber($_POST['number']));
$message = utf8_decode(addslashes($_POST['message']));
$messages = str_split($message, 153); //On fait des blocs de 153 caractères pour Gammu
$message = array_shift($messages);
$ref = sprintf("%02x", rand(1, 255));
$nb = sprintf("%02d", 1 + count($messages));

//Envoi du SMS demandé
$pdo->exec('INSERT INTO outbox SET '.
	'DestinationNumber = "'.$num.'", '.
	'UDH = "050003'.$ref.$nb.'01", '.
	'MultiPart = "'.(count($messages) > 0 ? 'true' : 'false').'", '.
	'TextDecoded = "'.$message.'", '.
	'CreatorID = "", '.
	'Class = "-1"');

$id = $pdo->lastInsertId();
$i = 1;
foreach ($messages as $message) {
	$pdo->exec('INSERT INTO outbox_multipart SET '.
		'SequencePosition = "'.++$i.'", '.
		'UDH = "050003'.$ref.$nb.sprintf("%02x", $i).'", '.
		'TextDecoded = "'.$message.'", '.
		'ID = "'.$id.'", '.
		'Class = "-1"');
}

die('1');
