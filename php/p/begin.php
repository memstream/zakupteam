<?php require_once __DIR__ . '/base.php'; 
if(empty($_COOKIE['csrf'])) {
	setcookie('csrf', bin2hex(openssl_random_pseudo_bytes(16)));
}
?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="preconnect" href="https://fonts.gstatic.com">
		<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,400;0,700;1,400&family=Roboto+Condensed:wght@700&display=swap" rel="stylesheet"> 
		<link href="css/common.css" rel="stylesheet">
		<link rel="shortcut icon" type="image/ico" href="favicon.ico"/>
		<title>ZakupTeam</title>
	</head>
	<body>
		<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
		<script>var csrf_token = '<?= $_COOKIE['csrf'] ?>';</script>
		<div id="header">
			<div class="logo">
			
			</div>
			<div class="title">
				ZakupTeam
			</div>
			<?php if(api_auth() && empty($hide_search)): ?>
				<?php api_form('search.php', 'search', 'GET'); ?>
					<input name="q" type="text" class="query">
				<?php api_form_end(); ?>
			<?php endif ?>
		</div>
