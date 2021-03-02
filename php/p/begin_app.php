<?php require_once __DIR__ . '/base.php';
if(!api_auth()) api_redirect('login.php', [
	'to' => $_SERVER['REQUEST_URI']
]);
require_once __DIR__ . '/begin.php';
?>
<div id="container">
	<div id="side">
		<div class="navitem">
			<img src="image/search.png" class="icon">
			<a href="search.php">Поиск по тендерам</a>
		</div>
		<div class="navitem">
			<img src="image/favorite.png" class="icon">
			<a href="favorite.php">Избранные тендеры</a>
		</div>
		<div class="navitem">
			<img src="image/notify.png" class="icon">
			<a href="notify.php">Интересные тендеры</a>
		</div>
		<div class="navitem">
			<img src="image/archive.png" class="icon">
			<a href="archive.php">Прошедшие тендеры</a>
		</div>
		<div class="navitem">
			<img src="image/calendar.png" class="icon">
			<a href="index.php">Календарь событий</a>
		</div>
	</div>
	<div id="content_container">
