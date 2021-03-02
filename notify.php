<?php 
require_once __DIR__ . '/php/p/base.php';

api_route([
	'add_rule' => function() {
		$d = R::dispense('notifyrule');
		$d->include = TEXT_INCLUDE;
		$d->exclude = TEXT_EXCLUDE;
		$d->max = MAX_PRICE;
		$d->min = MIN_PRICE;
		$d->fz = FZ;
		echo R::store($d);
	},
	'delete_rule#rid' => function($rid) {
		R::trash('notifyrule', $rid);
	},
	'clear_notify#rid' => function($rid) {
		R::trashAll(R::findAll('notify', ' notifyrule_id = ? ', [ $rid ]));
	},
	'rule_change#rid,field,value' => function($rid, $field, $value) {
		if(in_array($field, [ 'include', 'exclude', 'min', 'max', 'fz' ])) {
			$r = R::findOne('notifyrule', ' id = ? ', [ $rid ]);
			$r[$field] = $value;
			R::store($r);
		}
	}
]);

$css_files = ['css/search.css', 'css/notify.css'];
require_once __DIR__ . '/php/p/begin_app.php'; ?>
<div class="subheader">
	Здесь вы можете настроить систему подписки на интересующие вас тендеры.
	</p>
	<button class="image-button btn-add">
		<img src="image/plus.png">
		Добавить поисковое задание
	</button>
</div>
<div class="container">
	<?php foreach(R::findAll('notifyrule') as $nr): ?>
		<?php $notifys = R::findAll('notify', ' notifyrule_id = ? ', [ $nr->id ]); ?>
		<div class="search_form subheader" id="<?= $nr->id ?>">
			<table>
				<tr>
					<td>Тест включает:</td>
					<td><input type="text" name="include" value="<?= _XSS($nr->include) ?>"></td>
					<td>Текст исключает:</td>
					<td><input type="text" name="exclude" value="<?= _XSS($nr->exclude) ?>"></td>
				</tr>
				
				<tr>
					<td>Мин.Цена:</td>
					<td><input type="number" min="0" name="min" value="<?= _XSS($nr->min) ?>"></td>
					<td>Макс.Цена:</td>
					<td><input type="number" min="0" name="max" value="<?= _XSS($nr->max) ?>"></td>
				</tr>
				<tr>
					<td>
						<button class="btn-image btn-open">
							<img src="image/search.png"> 
							<span>
								Тендеры <?= count($notifys) ? ('(' . count($notifys) . ')') : '(Пусто)' ?>
							</span>
						</button>
					</td>
					<td>
						<button class="btn-image btn-close">
							<img src="image/close.png"> 
							Удалить
						</button>
					</td>
				</tr>
			</table>
		</div>
		<?php if(count($notifys)): ?>
			<div class="notify_container notify_container_hidden" id="<?= $nr->id ?>">
				<div class="left"></div>
				<div class="right">
					<div class="notify_header">
						<button class="btn-image btn-clear">
							<img src="image/close.png">
							Удалить все тендеры
						</button>
					</div>
					<?php foreach($notifys as $n): ?>
						<?php 
						$zakupka = R::findOne('tender', ' id = ? ', [ $n->tender_id ]); 
						$zakupka_view_subinfo = true;
						include __DIR__ . '/php/p/zakupka.php';
						?>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	<?php endforeach; ?>
</div>

<div class="search_form_parent search_form subheader hidden">
	<table>
		<tr>
			<td>Тест включает:</td>
			<td><input type="text" name="i" value="<?= TEXT_INCLUDE ?>"></td>
			<td>Текст исключает:</td>
			<td><input type="text" name="e" value="<?= TEXT_EXCLUDE ?>"></td>
		</tr>
		
		<tr>
			<td>Мин.Цена:</td>
			<td><input type="number" min="0" name="n" value="<?= MIN_PRICE ?>"></td>
			<td>Макс.Цена:</td>
			<td><input type="number" min="0" name="m" value="<?= MAX_PRICE ?>"></td>
		</tr>
		<tr>
			<td>
				<button class="btn-image btn-open">
					<img src="image/search.png"> 
					Тендеры
				</button>
			</td>
			<td>
				<button class="btn-image btn-close">
					<img src="image/close.png"> 
					Удалить
				</button>
			</td>
		</tr>
	</table>
</div>

<script src="js/notify.js"></script>
<script src="js/tender.js"></script>
<?php require_once __DIR__ . '/php/p/end.php'; ?>
