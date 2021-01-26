function formBindEvents(form) {
	$(form).find('.btn-close').click(function() {
		$.post('notify.php', {
			csrf: csrf_token,
			action: 'delete_rule',
			rid: $(form).attr('id')
		}).done(function(data) {
			
		});
		$(form).remove();
	});
	$(form).find('.btn-open').click(function() {
		$('#' + $(form).attr('id') + '.notify_container').removeClass('notify_container_hidden');
	});
	
	function onChange(e) {
		$.post('notify.php', {
			csrf: csrf_token,
			action: 'rule_change',
			rid: $(form).attr('id'),
			field: $(e.target).attr('name'),
			value: $(e.target).val()
		}).done(function(data) {
			
		});
	}
	$(form).find('input').change(onChange);
	$(form).find('select').change(onChange);
}

$(document).ready(function() {
	$('.search_form').each(function(i, ef) {
		if(!$(ef).attr('id')) return;
		formBindEvents(ef);
		
		var exclude = $(ef).find('input[name="exclude"]').val().toUpperCase();
		var excludeWords = exclude.split(' ');
		$('#' + $(ef).attr('id') + '.notify_container .tender').each(function(i, et) {
			if(exclude.length) {
				var fullText = $(et).find('.org').text();
				fullText += ' ' + $(et).find('.info').text();
				fullText += ' ' + $(et).find('.type').text();
				fullText = fullText.toUpperCase();
				
				for(var i = 0;i < excludeWords.length;i++) {
					if(fullText.search(excludeWords[i]) != -1) {
						et.remove();
						return;
					}
				}
			}
		});
	});
	
	$('.btn-add').click(function() {
		var form = $('.search_form_parent').clone();
		form.removeClass('search_form_parent');
		form.removeClass('hidden');
		$('.container').append(form);
		
		$.post('notify.php', {
			csrf: csrf_token,
			action: 'add_rule'
		}).done(function(data) {
			form.attr('id', parseInt(data));
			formBindEvents(form);
		});
	});
	
	$('.notify_container').each(function(i, ec) {
		$(ec).find('.btn-clear').click(function() {
			$.post('notify.php', {
				csrf: csrf_token,
				action: 'clear_notify',
				rid: $(ec).attr('id')
			}).done(function(data) {
				
			});
			$('#' + $(ec).attr('id') + '.search_form .btn-open span').text('Тендеры (Пусто)');
			ec.remove();
		});
	});
});
