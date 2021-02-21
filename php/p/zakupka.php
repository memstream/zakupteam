<div class="tender">
	<div class="header">
		<span class="id"><a href="<?= $zakupka['href'] ?>"><?= $zakupka['n'] ?></a></span> |
		<span class="price"><?= $zakupka['price'] ?></span>
		<span class="profit"></span>
	</div>
	<div class="subinfo">
		<span class="type"><?= $zakupka['type'] ?></span> |
		<span class="ending">
			<?php if(isset($zakupka_full_view_style) || isset($zakupka_view_subinfo)): ?>
				Подача заявок до: <?= $zakupka['ending'] ?> 
				<?php if($zakupka['tradedate']): ?>
					| Проведение: <?= substr($zakupka['tradedate'], 0, -9) ?>
				<?php endif; ?>
			<?php endif; ?>
		</span>
	</div>
	<span class="info"><?= $zakupka['info'] ?></span><br>
	<details class="docs" <?= !empty($zakupka_show_details) ? 'open' : '' ?>>
		<summary>Документы</summary>
		<?php if(isset($zakupka_full_view_style) || isset($zakupka_view_subinfo)): ?>
			<?php foreach(R::findAll('attach', ' tender_id = ? ', [ $zakupka['id'] ]) as $file): ?>
				<div class="file">
					<img class="text-icon" src="<?= $file['img'] ?>">
					<a href="<?= _XSS($file['href']) ?>"><?= _XSS($file['title']) ?></a>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	</details>
	<?php if(isset($zakupka_full_view_style)): ?>
		<br>
		<details <?= !empty($zakupka_show_details) ? 'open' : '' ?>>
			<summary>Заметка</summary>
			<div class="commentary" contenteditable><?= $zakupka['commentary'] ? $zakupka['commentary'] : '' ?></div>
			<br>
			<button class="image-button btn-show">
				<img src="image/fullscreen.png" class="text-icon">
				Открыть в окне
			</button>
		</details>
		<br>
		<details <?= !empty($zakupka_show_details) ? 'open' : '' ?>>
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
	<?php endif; ?>
	<span class="org"><?= $zakupka['org'] ?></span>
	</p>
	<div>
		<?php if(isset($zakupka_show_newtab) && $zakupka_show_newtab): ?>
			<a href="favorite.php?id=<?= $zakupka['id'] ?>" target="_blank" class="a-button">
				<button class="image-button">
					<img src="image/fullscreen.png" class="text-icon">
					Открыть в отдельной вкладке
				</button>
			</a>
		<?php endif; ?>
		<button class="image-button btn_favorite" tid="<?= $zakupka['id'] ?>"><img src="image/favorite.png">В избранное</button>
	</div>
</div>
