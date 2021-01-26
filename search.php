<?php 
require_once __DIR__ . '/php/p/base.php';
require_once __DIR__ . '/php/zakupki.php';

$hide_search = true; 
$i = TEXT_INCLUDE;
$e = TEXT_EXCLUDE;
$m = MAX_PRICE;
$n = MIN_PRICE;
$f = FZ;
$p = 1;
if(isset($_GET['q'])) $i = $_GET['q'];
if(isset($_GET['i'])) $i = $_GET['i'];
if(isset($_GET['e'])) $e = $_GET['e'];
if(isset($_GET['m'])) $m = $_GET['m'];
if(isset($_GET['n'])) $n = $_GET['n'];
if(isset($_GET['f'])) $f = $_GET['f'];
if(isset($_GET['p'])) $p = $_GET['p'];

api_route(array(
	'subinfo#tid' => function($tid) {
		echo json_encode(zakupki_subinfo($tid));
		die;
	}
));

$css_files = [ 'css/search.css' ];
require_once __DIR__ . '/php/p/begin_app.php'; ?>
<form class="search_form" action="search.php" method="GET">
	<table>
		<tr>
			<td>Тест включает:</td>
			<td><input type="text" name="i" value="<?= _XSS($i) ?>"></td>
			<td>Текст исключает:</td>
			<td><input type="text" name="e" value="<?= _XSS($e) ?>"></td>
		</tr>
		
		<tr>
			<td>Мин.Цена:</td>
			<td><input type="number" min="0" name="n" value="<?= _XSS($n) ?>"></td>
			<td>Макс.Цена:</td>
			<td><input type="number" min="0" name="m" value="<?= _XSS($m) ?>"></td>
		</tr>
		<tr>
			<td><input type="submit" value="Поиск"></td>
			<td></td>
			<td>ФЗ:</td>
			<td>
				<select name="f">
					<option value="44" <?= $f == 44 ? 'selected' : '' ?>>44-ФЗ</option>
					<option value="223" <?= $f == 223 ? 'selected' : '' ?>>223-ФЗ</option>
				</select>
			</td>
		</tr>
	</table>
</form>
<?php 
$result = zakupki_search($p, $i, $m, $n, $f);
foreach($result['zakupki'] as $zakupka): ?>
	<?php include __DIR__ . '/php/p/zakupka.php'; ?>
<?php endforeach; ?>
<div class="pages">
	Страницы: 
	<?php 
	foreach($result['pages'] as $page): ?>
		<?php if($p == $page): ?>
			<b><?= $page ?></b>
		<?php else: ?>
			<a href="<?= api_to('search.php', array(
				'i' => $i,
				'e' => $e,
				'm' => $m,
				'n' => $n,
				'f' => $f,
				'p' => _XSS($page)
			), false) ?>"><?= _XSS($page) ?></a>
		<?php endif; ?>
		
	<?php endforeach; ?>
</div>
<script src="js/search.js"></script>
<script src="js/tender.js"></script>
<?php require_once __DIR__ . '/php/p/end.php'; ?>
