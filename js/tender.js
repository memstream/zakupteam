function btn_set_style(btn, style) {
	if(style == 'add') {
		$(btn).text('В избранное');
		$('<img>', {
			src: 'image/favorite.png'
		}).prependTo($(btn));
		$(btn).unbind('click');
		$(btn).click(on_favorite_add);
	} else {
		$(btn).text('Удалить из избранного');
		$('<img>', {
			src: 'image/delete.png'
		}).prependTo($(btn));
		$(btn).unbind('click');
		$(btn).click(on_favorite_del);
	}
}

function on_favorite_add(e) {
	btn_set_style(e.target, 'delete');
	
	$.post('favorite.php', {
		'csrf': csrf_token,
		'action': 'add',
		'tid': $(e.target).attr('tid')
	}).done(function(data) {
		
	});
}

function on_favorite_del(e) {
	btn_set_style(e.target, 'add');
	
	$.post('favorite.php', {
		'csrf': csrf_token,
		'action': 'delete',
		'tid': $(e.target).attr('tid')
	}).done(function(data) {
		
	});
}

$(document).ready(function() {
	$('.tender').each(function(i, et) {
		var btn = $(et).find('.btn_favorite');
		
		btn_set_style(btn, 'add');
		
		$.post('favorite.php', {
			'csrf': csrf_token,
			'action': 'in',
			'tid': $(btn).attr('tid')
		}).done(function(data) {
			if(JSON.parse(data)) {
				btn_set_style(btn, 'delete');
			}
		});
		
	});
});
