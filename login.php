<?php require_once __DIR__ . '/php/p/base.php'; 
	if(isset($_SESSION['auth'])) {
		api_redirect('index.html', array(), false);
	} 
	api_route(array(
		'@login#key,?to' => function($key, $to = null) {
			if($key === KEY) {
				$_SESSION['auth'] = true;
				api_redirect($to ? $to : 'index.php', array(), false);
			} 
			api_redirect('login.php', array(
				'error' => 'Неправильный ключ!'
			), false);
		}
	));
require_once __DIR__ . '/php/p/begin.php'; ?>
<div class="login_form">
	<div class="title">Вход в систему</div>
	<div class="body">
		<div class="icon"></div>
		<div class="content">
			<?php api_form('login.php', 'login'); ?>
				Введите ваш ключ доступа:<br>
				<input name="key" type="text" required></p>
				<input type="submit" value="Авторизоваться">
				<?php if(isset($_GET['to'])): ?>
					<?php api_hidden('to', $_GET['to']) ?>
				<?php endif; ?>
			<?php api_form_end(); ?>
		</div>
	</div>
	<?php if(isset($_GET['error'])): ?>
		<div class="error">
			<?= _XSS($_GET['error']) ?>
		</div>
	<?php endif; ?>
</div>
<?php require_once __DIR__ . '/php/p/end.php'; ?>

