# Распознавание речи на PHP с помощью яндексовского SpeechKit Cloud API

Понимает русский, украинский, английский и турецкий языки.

```
Использование:  php voice.php  <файл для распознавания.WAV>  <АПИ-ключ>  <язык>

Параметры: 
	<файл для распознавания.WAV>    в формате `audio/x-pcm`, 16 бит, 16000 Гц (один канал)
	<АПИ-ключ>                      можно получить по ссылке https://developer.tech.yandex.ru
	<язык>                          ru-RU (по умолчанию), uk-UK, en-US, tr-TR

Дополнительно: 
	команда для конвертации MP3 в WAV: 
		sox voice.mp3 -t wav -c 1 --rate 16000 -b 16 -e signed-integer voice.wav
		Предварительно поставить: sudo apt install sox libsox-fmt-all
```
