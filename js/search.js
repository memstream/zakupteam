$(document).ready(function() {
	var exclude = $('input[name="e"]').val().toUpperCase();
	var excludeWords = exclude.split(' ');
	$('.tender').each(function(i, et) {
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
		
		$.post('search.php', {
			'csrf': csrf_token,
			'action': 'subinfo',
			'tid': $(et).find('.id a').text()
		}).done(function(data) {
			$(et).find('.ending').text('Подача заявок до: ' + JSON.parse(data)['ending']);
			
			var docs = $(et).find('.docs');			
			$(JSON.parse(data).files).each(function(i, file) {
				var container = $('<div>', {
					'class': 'file'
				});
				
				var icon = $('<img>', {
					'class': 'text-icon',
					'src': file.img
				});
				
				var label = $('<a>');
				label.text(file.title);
				label.attr('href', file.href);
				
				var br = $('<br>');
				
				icon.appendTo(container);
				label.appendTo(container);
				br.appendTo(container);
				container.appendTo(docs);
			});
		});
	});
});
