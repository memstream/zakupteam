<?php require_once __DIR__ . '/php/p/begin_app.php'; ?>
<div id="content">
	<?php 
	$favorites = R::findAll('favorite');
	$tenders = array();
	foreach($favorites as $f) {
		$tenders = array_merge($tenders, R::findAll(
			'tender', ' tradedate >= ? AND id = ? AND tradedate IS NOT NULL ORDER BY tradedate ', 
			[ date('Y-m-d H:i:s'), $f->tender_id ]
		));
	}
	if(count($tenders)):
	?>
		<div class="title-with-icon">
			<img src="image/trade.png" class="title-icon">
			Ближайшие торги
		</div>
		<ul>
			<?php foreach($tenders as $tender): ?>
				<li>[<?= substr($tender->tradedate, 0, -9) ?>] <a href="favorite.php#<?= _XSS($tender->n) ?>"><?= _XSS($tender->info) ?></a></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
<?php require_once __DIR__ . '/php/p/end.php'; ?>
