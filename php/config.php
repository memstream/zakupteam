<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'zakupteam');

// Общий ключ входа 
define('KEY', 'Letovist');

// Настройки поиска по умолчанию
define('TEXT_INCLUDE', '');
define('TEXT_EXCLUDE', '');
define('MIN_PRICE', 0);
define('MAX_PRICE', 350000);
define('FZ', 44); 

// Максимальное кол-во страниц обхода подписок на тендеры
define('MAX_NOTIFY_PAGE', 2);

// Задержка на страницу в секундах (что бы не получить бан)
define('NOTIFY_SLEEP', 5);
