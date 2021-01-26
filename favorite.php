<?php require_once __DIR__ . '/php/p/base.php'; 
api_route(array(
	'in#tid' => function($tid) {
		if(R::findOne('favorite', ' tender_id = ? ', [ $tid ])) {
			echo 'true';
		} else {
			echo 'false';
		}
	},
	'add#tid' => function($tid) {
		$f = R::dispense('favorite');
		$f->tender_id = $tid;
		$f->created_at = date('Y-m-d H:i:s');
		R::store($f);
	},
	'delete#tid' => function($tid) {
		R::trash(R::findOne('favorite', ' tender_id = ? ',[ $tid ]));
	},
	'save_table#tid,table' => function($tid, $table) {
		$t = R::findOne('tender', ' id = ? ', [ $tid ]);
		$t->table = $table;
		R::store($t);
	},
	'save_commentary#tid,commentary' => function($tid, $commentary) {
		$t = R::findOne('tender', ' id = ? ', [ $tid ]);
		$t->commentary = $commentary;
		R::store($t);
	}
));
?>
<?php require_once __DIR__ . '/php/p/begin_app.php'; ?>
<?php 
$zakupki = array();
foreach(R::findAll('favorite', ' order by created_at desc ') as $f) {
	if(isset($is_archive)) {
		$zakupka = R::findOne('tender', ' ending <= ? and id = ? ', [ date('Y-m-d H:i:s'), $f->tender_id ]);
		if($zakupka) array_push($zakupki, $zakupka);
	} else {
		$zakupka = R::findOne('tender', ' ending >= ? and id = ? ', [ date('Y-m-d H:i:s'), $f->tender_id ]);
		if($zakupka) array_push($zakupki, $zakupka);
	}
}
foreach($zakupki as $zakupka): ?>
	<div class="tender">
		<div class="header">
			<span class="id"><a href="<?= $zakupka['href'] ?>"><?= $zakupka['n'] ?></a></span> |
			<span class="price"><?= $zakupka['price'] ?></span>
			<span class="profit"></span>
		</div>
		<div class="subinfo">
			<span class="type"><?= $zakupka['type'] ?></span> |
			<span class="ending">Подача заявок до: <?= $zakupka['ending'] ?> 
			<?php if($zakupka['tradedate']): ?>
				| Проведение: <?= substr($zakupka['tradedate'], 0, -9) ?></span>
			<?php endif; ?>
		</div>
		<span class="info"><?= $zakupka['info'] ?></span><br>
		<details class="docs">
			<summary>Документы</summary>
			<?php foreach(R::findAll('attach', ' tender_id = ? ', [ $zakupka['id'] ]) as $file): ?>
				<div class="file">
					<img class="text-icon" src="<?= $file['img'] ?>">
					<a href="<?= _XSS($file['href']) ?>"><?= _XSS($file['title']) ?></a>
				</div>
			<?php endforeach; ?>
		</details>
		<br>
		<details>
			<summary>Заметка</summary>
			<div class="commentary" contenteditable><?= $zakupka['commentary'] ? $zakupka['commentary'] : '' ?></div>
			<br>
			<button class="image-button btn-show">
				<img src="image/fullscreen.png" class="text-icon">
				Открыть в окне
			</button>
		</details>
		<br>
		<details>
			<summary>Решение</summary>
			<?php if($zakupka['table']): ?>
				<?= $zakupka['table'] ?>
			<?php else: ?>
				<table class="solution">
					<tbody>
						<tr>
							<td>№</td>
							<td>Наименование</td>
							<td>Ориг.Цена</td>
							<td>Наша цена</td>
							<td>Кол-во</td>
							<td>Ссылка</td>
						</tr>
					</tbody>
					<tbody></tbody>
				</table>
			<?php endif; ?>
			<br>
			<button class="image-button btn-plus">
				<img src="image/plus.png" class="text-icon">
				Добавить ряд
			</button>
			<button class="image-button btn-minus">
				<img src="image/minus.png" class="text-icon">
				Удалить ряд
			</button>
			</p>
			<h4 class="formula"></h4>
		</details>
		<span class="org"><?= $zakupka['org'] ?></span>
		</p>
		<div>
			<button class="btn_favorite" tid="<?= $zakupka['id'] ?>"><img src="image/favorite.png">В избранное</button>
		</div>
	</div>
<?php endforeach; ?>

<div id="fullscreen_window" class="hidden">
	<div class="title">
		Редактирование заметки
		<img src="image/close.png" class="close">
	</div>
	<div class="content" contenteditable></div>
</div>

<script src="js/tender.js"></script>
<script src="js/favorite.js"></script>
<?php require_once __DIR__ . '/php/p/end.php'; ?>
