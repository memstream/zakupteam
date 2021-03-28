<?php require_once __DIR__ . '/php/p/base.php'; 
api_route([
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
]);

$css_files = [ 'css/favorite.css' ];
require_once __DIR__ . '/php/p/begin_app.php'; ?>

<?php
$sort_order_options = [
	[
		'value' => 'asc',
		'title' => 'От меньшего к большему'
	],
	[
		'value' => 'desc',
		'title' => 'От большего к меньшему'
	]
];

$sort_by_options = [
	[
		'value' => 'created_at',
		'title' => 'Дата добавления в избранное'
	],
	[
		'value' => 'ending',
		'title' => 'Дата окончания подачи заявок'
	],
	[
		'value' => 'tradedate',
		'title' => 'Дата проведения'
	]
];

$sort_order = $_GET['sort_order'] ?? $sort_order_options[0]['value'];
$sort_by = $_GET['sort_by'] ?? $sort_by_options[0]['value'];

if(!in_array($sort_order, array_column($sort_order_options, 'value'))) {
	$sort_order = $sort_order_options[0]['value'];
}

if(!in_array($sort_by, array_column($sort_by_options, 'value'))) {
	$sort_by = $sort_by_options[0]['value'];
}

if($sort_by == 'created_at') {
	$sort_query = "ORDER BY created_at $sort_order";
} else {
	$sort_query = "JOIN tender ON tender.id = favorite.tender_id ORDER BY tender.$sort_by $sort_order";
}
?>

<form class="subheader" action="<?= isset($is_archive) ? 'archive.php' : 'favorite.php' ?>" method="GET">
	<table>
		<tr>
			<td>В порядке:</td>
			<td>
				<select name="sort_by" onchange="this.form.submit()">
					<?php foreach($sort_by_options as $option): ?>
						<option 
							value="<?= $option['value'] ?>" 
							<?= $option['value'] == $sort_by ? 'selected' : '' ?>
						>
							<?= $option['title'] ?>
						</option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Сортировать по:</td>
			<td>
				<select name="sort_order" onchange="this.form.submit()">
					<?php foreach($sort_order_options as $option): ?>
						<option 
							value="<?= $option['value'] ?>" 
							<?= $option['value'] == $sort_order ? 'selected' : '' ?>
						>
							<?= $option['title'] ?>
						</option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
	</table>
</form>

<?php 
$zakupki = [];
$zakupka_show_newtab = true;
$zakupka_show_details = false;

if(empty($_GET['id'])) {
	foreach(R::findAll('favorite', $sort_query) as $f) {
		if(isset($is_archive)) {
			$zakupka = R::findOne('tender', ' ending <= ? and id = ? ', [ date('Y-m-d H:i:s'), $f->tender_id ]);
			if($zakupka) array_push($zakupki, $zakupka);
		} else {
			$zakupka = R::findOne('tender', ' ending >= ? and id = ? ', [ date('Y-m-d H:i:s'), $f->tender_id ]);
			if($zakupka) array_push($zakupki, $zakupka);
		}
	}
} else {
	array_push($zakupki, R::findOne('tender', ' id = ? ', [ $_GET['id'] ]));
	$zakupka_show_newtab = false;
	$zakupka_show_details = true;
	
}
foreach($zakupki as $zakupka): ?>
	<?php $zakupka_full_view_style = true;
	include __DIR__ . '/php/p/zakupka.php'; ?>
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
