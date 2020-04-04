<?php
include_once('Db.php');

$data = file_get_contents('php://input');
$data = json_decode($data, true);

if (empty($data['message']['chat']['id'])) {
	exit();
}

define('TOKEN', '1064258216:AAHAlRu3FTW3LSX1If4TykG0Bh1W8ccbFUk');

// Функция вызова методов API.
function sendTelegram($method, $response)
{
	$ch = curl_init('https://api.telegram.org/bot' . TOKEN . '/' . $method);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $response);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);
	$res = curl_exec($ch);
	curl_close($ch);

	return $res;
}

// Ответ на текстовые сообщения.
if (!empty($data['message']['text'])) {
	$user = $data['message']['chat']['id'];
	$chislo = 0;
	$kol = 0;

	$sql = "SELECT chislo,kol FROM chislo WHERE user='$user'";
	$result = $db->get($sql);

	if (isset($result) && isset($result[0]["chislo"])){
		$chislo = $result[0]["chislo"];
		$kol = $result[0]["kol"];
	}

	$text = trim($data['message']['text']);

  $commands = array('/help' => 'Мои комманды: Начать - начинает игру угадай число');

  if (isset($commands[$text])) {
    sendTelegram('sendMessage',array('chat_id' => $user,'text' => $commands[$text]));
		exit();
  }

  if ($text == 'Начать') {
		$result = 'Угадайте число от 0 до 100, введите Ваше число: ';
    sendTelegram('sendMessage',array('chat_id' => $user,'text' => $result));

		$sql = "SELECT chislo FROM chislo WHERE user='$user'";
		$result = $db->get($sql);

		$chislo = rand(0, 100);
		if (!$result){
			$sql = "INSERT INTO chislo SET chislo=$chislo, user='$user'";
		} else {
			$sql = "UPDATE chislo SET chislo=$chislo WHERE user='$user'";
		}
		$db->get($sql);
		exit();
  }

	if ($text && $chislo>0) {
		$kol++;
		$text = intval($text);
		if($chislo > $text){
			$result = 'Больше';
		} else {
			$result = 'Меньше';
		}
		if($chislo == $text){
			$result = 'Угадал! С попытки: '.$kol;
			$sql = "DELETE FROM chislo WHERE user='$user'";
		  $db->get($sql);
		} else {
			$sql = "UPDATE chislo SET kol=$kol WHERE user='$user'";
			$db->get($sql);
		}

		sendTelegram('sendMessage',array('chat_id' => $user,'text' => $result));
		exit();
	} else {
		sendTelegram('sendMessage',array('chat_id' => $user,'text' => 'Для начало игры введите: Начать'));
		exit();
	}



}
?>
