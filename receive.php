<?php

define('LIST_SERVICES', '#(?:services|help|aide)#');
define('SERVICE_SEND', '#(?:send|envoi|envoyer)#');
define('NUMBER_ADMIN', '06xxxxxxxx');
define('SALT_SEND', 'salt');
define('URL_SEND', 'http://localhost/sendsms.php');

$pdo = new PDO('mysql:host=localhost;dbname=gammu', 'root', '');
$pdo2 = new PDO('mysql:host=localhost;dbname=services_sms', 'root', '');

$messages_ = $pdo->query('SELECT CONCAT(LEFT(UDH, 10), "_", SenderNumber), i.* FROM inbox AS i WHERE Processed = "false" ORDER BY ReceivingDateTime ASC')->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_GROUP);
$services_ = $pdo2->query('SELECT s.code, s.* FROM services AS s ORDER BY s.only DESC')->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);

function repairNumber($number) {
        return str_replace('+33', '0', $number);
}

function secure($string) {
	return trim(htmlspecialchars(addslashes($string)));
}

function sendSms($number, $message) {
	$signature = md5(SALT_SEND.$number.$message);
	sendPost(URL_SEND, [
        	'number' => $number,
        	'message' => $message,
        	'signature' => $signature]);
}

function sendPost($url, $post = []) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	//curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$response = curl_exec($ch);
	return !curl_errno($ch) ? $response : curl_error($ch);
}

foreach ($services_ as $code => $service) {
	$only = explode(',', $service['only']);
        $only = array_map('trim', $only);
        $only = array_map('repairNumber', $only);
	$only = array_filter($only);
	$services_[$code]['only'] = $only;
}

$messages = [];
foreach ($messages_ as $group => $messages_group) {
	$group = explode('_', $group);

	if (empty($group[0])) {
		$messages = array_merge($messages, $messages_group);
	} else {
		$subgroups = [];
		$count = [];

		foreach ($messages_group as $message) {
			$num = (int) substr($message['UDH'], 10, 2);
			if (empty($count[$num]))
				$count[$num] = 0;

			$count[$num]++;
			$subgroups[$count[$num]][$num] = $message;
		}

		foreach ($subgroups as $subgroup) {
			ksort($subgroup);
			$message_subgroup = array_shift($subgroup);

			foreach ($subgroup as $message) {
				$message_subgroup['ID'] .= ','.$message['ID'];
				$message_subgroup['TextDecoded'] .= $message['TextDecoded'];
			}

			$messages[] = $message_subgroup;
		}
	}
}

//print_r($messages); die;
unset($message);
if (!empty($messages)) {
        foreach ($messages as $message) {
                $content = trim($message['TextDecoded']);
                $content = explode(' ', $content);
		$service = strtolower(array_shift($content));
              	$number = repairNumber($message['SenderNumber']);

		$default = null;
		$services = $services_;
		foreach ($services as $code => $serv) {
			if (count($serv['only']) &&
				!in_array($number, $serv['only'])) {
				unset($services[$code]);
				continue;
			}

 		       	if (empty($default) &&
				!empty($serv['default'])) {
				$default = $code;
				continue;
			}
		}

		if (empty($services[$service]))
			array_unshift($content, $service);

                if ($default === null && empty($services[$service]) || preg_match(LIST_SERVICES, $service)) {
			$str = 'Liste des services'."\n";
			$count = 0;
			foreach ($services as $code => $service) {
				if ($service['public']) {
					$str .= strtoupper($code).($default == $code ? ' (Défaut)' : '')."\n";
                        		$count++;
				}
			}

			if ($count === 0)
				$str .= "Aucun service public disponible";

			sendSms($number, $str);
		} else if (preg_match(SERVICE_SEND, $service) && $number == NUMBER_ADMIN) {
			if (empty($services[$service]))
				array_shift($content);

			if (!count($content) || empty($content[array_keys($content)[0]]))
				sendSms($number, 'Syntaxe : "'.SERVICE_SEND.' #telephone# #message#"');
			else {
				$to = array_shift($content);
				$content = implode(' ', $content);

				if (!preg_match("#^0[1-9][0-9]{8}$#", repairNumber($to)))
					sendSms($number, 'Numéro invalide : '.$to);

				else if (preg_match("#^08#", repairNumber($to)))
					sendSms($number, 'Numéro surtaxé : '.$to);

				else {
					sendSms($number, 'Message bien envoyé à '.$to);
					sendSms($to, $content);
				}
			}
                } else {
			$content = utf8_encode(implode(' ', $content));
			$service = !empty($services[$service]) ? $services[$service] : (!empty($default) && !empty($services[$default]) ? $services[$default] : null);

			if (!empty($service)) {
				$signature = md5($service['salt'].$number.$content);
        	                $ret = sendPost($service['url'], [
					'number' => $number,
					'message' => $content,
					'signature' => $signature]);
			}
		}

		$ids = explode(',', $message['ID']);
		foreach ($ids as $id)
	                $pdo->exec('UPDATE inbox SET Processed = "true" WHERE ID = '.$id);
        }
}
