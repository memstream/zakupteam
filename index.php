<?php require_once __DIR__ . '/php/p/begin_app.php'; ?>
<div id="content">
	<?php
	$tenders_trade = R::findAll('tender', ' JOIN favorite ON tender.id = favorite.tender_id AND tender.tradedate IS NOT NULL AND tender.tradedate >= ? ORDER BY tender.tradedate ',
		[ date('Y-m-d H:i:s') ]
	);
	$tenders_entry = R::findAll('tender', ' JOIN favorite ON tender.id = favorite.tender_id AND tender.ending IS NOT NULL AND tender.ending >= ? ORDER BY tender.ending ',
		[ date('Y-m-d H:i:s') ]
	);
	?>
	
	<?php if(count($tenders_entry)): ?>
		<div class="title-with-icon">
			<img src="image/entry.png" class="title-icon">
			Ближайшие тендеры с окончанием подачи заявок
		</div>
		<ul>
			<?php foreach($tenders_entry as $tender): ?>
				<li>[<?= substr($tender->ending, 0, -9) ?>] 
					<a href="<?= $starturl ?>#<?= _XSS($tender->n) ?>"><?= _XSS($tender->info) ?></a>
					</p>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php if(count($tenders_trade)): ?>
		<div class="title-with-icon">
			<img src="image/trade.png" class="title-icon">
			Ближайшие торги
		</div>
		<ul>
			<?php foreach($tenders_trade as $tender): ?>
				<?php $in_archive = $tender->ending < date('Y-m-d H:i:s'); ?>
				<?php $starturl = $in_archive ? 'archive.php' : 'favorite.php'; ?>
				<li>[<?= substr($tender->tradedate, 0, -9) ?>] 
					<a 
						<?php if($in_archive): ?>
							class="archive-link"
						<?php endif; ?>
						href="<?= $starturl ?>#<?= _XSS($tender->n) ?>"><?= _XSS($tender->info) ?>
					</a>
					</p>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
	
	<?php endif; ?>
</div>
<?php require_once __DIR__ . '/php/p/end.php'; ?>
