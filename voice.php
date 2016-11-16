<?php
/**
 * Распознавание речи.
 *
 * Основано на https://github.com/yandex/speechkitcloud/tree/master/php
 *
 * @author MaximAL
 * @since 2016-11-16 Первая версия
 * @date 2016-11-16
 * @time 15:18
 * @link http://maximals.ru
 * @link https://github.com/maximal
 * @link https://github.com/yandex/speechkitcloud/tree/master/php
 * @copyright © MaximAL, Sijeko 2016
 */


if (count($argv) < 3) {
	echo '        Распознавание речи Яндексом       © MaximAL 2016        https://github.com/maximal', PHP_EOL;
	echo '        ===========================', PHP_EOL, PHP_EOL;
	echo 'Использование:  php ', $argv[0], '  <файл для распознавания.WAV>  <АПИ-ключ>  <язык>', PHP_EOL, PHP_EOL;
	echo 'Параметры: ', PHP_EOL;
	echo "\t", '<файл для распознавания.WAV>    в формате `audio/x-pcm`, 16 бит, 16000 Гц (один канал)', PHP_EOL;
	echo "\t", '<АПИ-ключ>                      можно получить по ссылке https://developer.tech.yandex.ru', PHP_EOL;
	echo "\t", '<язык>                          ru-RU (по умолчанию), uk-UK, en-US, tr-TR', PHP_EOL, PHP_EOL;
	echo 'Дополнительно: ', PHP_EOL;
	echo "\t", 'команда для конвертации MP3 в WAV: ', PHP_EOL;
	echo "\t\t", 'sox voice.mp3 -t wav -c 1 --rate 16000 -b 16 -e signed-integer voice.wav', PHP_EOL;
	echo "\t\t", 'Предварительно поставить: sudo apt install sox libsox-fmt-all', PHP_EOL;
	exit(1);
}


$filename = $argv[1];
$key = $argv[2];
$lang = count($argv) > 3 ? $argv[3] : 'ru-RU';

recognize($filename, $key, $lang);

echo 'Готово.', PHP_EOL;
exit(0);


function generateRandomSelection($min, $max, $count)
{
	$result = [];
	if ($min > $max) {
		return $result;
	}
	$count = min(max($count, 0), $max - $min + 1);
	while (count($result) < $count) {
		$value = rand($min, $max - count($result));
		foreach ($result as $used) {
			if ($used <= $value) {
				$value++;
			} else {
				break;
			}
		}
		$result [] = dechex($value);
		sort($result);
	}
	shuffle($result);
	return $result;
}


function recognize($file, $key, $lang = 'ru-RU')
{
	$uuid = generateRandomSelection(0, 30, 64);
	$uuid = implode($uuid);
	$uuid = substr($uuid, 1, 32);
	$curl = curl_init();
	$url = 'https://asr.yandex.net/asr_xml?' . http_build_query([
			'key' => $key,
			'uuid' => $uuid,
			'topic' => 'notes',
			'lang' => $lang,
		]);
	curl_setopt($curl, CURLOPT_URL, $url);

	$data = file_get_contents(realpath($file));

	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: audio/x-wav'));
	//curl_setopt($curl, CURLOPT_VERBOSE, true);
	$response = curl_exec($curl);
	$err = curl_errno($curl);
	curl_close($curl);
	if ($err) {
		throw new exception("curl err $err");
	}

	$results = new SimpleXMLElement($response);
	foreach ($results->variant as $variant) {
		echo 'Достоверность: ', ($variant['confidence'] * 100), '%', PHP_EOL;
		echo $variant, PHP_EOL, PHP_EOL;
	}
}
