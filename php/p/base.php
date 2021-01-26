<?php
function _XSS($text) {
	return htmlentities($text);
} 

require_once __DIR__ . '/../config.php';
session_start();
require_once __DIR__ . '/../api.php';

require_once __DIR__ . '/../rb.php';

R::setup('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
R::useFeatureSet('novice/latest');
