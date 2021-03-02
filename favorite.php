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
$zakupki = [];
$zakupka_show_newtab = true;
$zakupka_show_details = false;

if(empty($_GET['id'])) {
	foreach(R::findAll('favorite', ' order by created_at desc ') as $f) {
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
