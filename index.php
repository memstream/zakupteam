<?php require_once __DIR__ . '/php/p/begin_app.php'; ?>
<div id="content">
	<?php
	$tenders = R::findAll('tender', ' JOIN favorite ON tender.id = favorite.tender_id AND tender.tradedate IS NOT NULL AND tender.tradedate >= ? ORDER BY tender.tradedate ',
		[ date('Y-m-d H:i:s') ]
	);
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
