function openTab(url) {
  const link = document.createElement('a');
  link.href = url;
  link.target = '_blank';
  document.body.appendChild(link);
  link.click();
  link.remove();
}

function saveTable(et, table) {
	$(table).find('input').each(function(i, ei) {
		$(ei).attr('hidden-value', $(ei).val());
	});
	$.post('favorite.php', {
		csrf: csrf_token,
		action: 'save_table',
		tid: $(et).find('.btn_favorite').attr('tid'),
		table: '<table class="solution">' + $(table).html() + '</table>'
	}).done(function() {
		
	});
}

function recalcTable(et, table) {
	var priceText = $(et).find('.price').text();
	var price = '';
	for(var i = 0;i < priceText.length;i++) {
		var c = priceText.charAt(i);
		if((c >= '0' && c <= '9') || c == ',' || c == '.') {
			price = price + c;
		}
	}
	price = parseFloat(price);
	
	var col4 = $(table).find('tbody:last-child tr td:nth-child(4) input');
	var col5 = $(table).find('tbody:last-child tr td:nth-child(5) input');
	var sum = 0;
	for(var i = 0;i < col4.length;i++) {
		var v4 = parseFloat($(col4[i]).val() ? $(col4[i]).val() : 0);
		var v5 = parseFloat($(col5[i]).val() ? $(col5[i]).val() : 1);
		sum += v4 * v5;
	}
	$(et).find('.formula').text(price + '-' + sum + '=' + (price - sum));
	$(et).find('.profit').text(' +(' + (price - sum) + ')');
	
	saveTable(et, table);
}

function rebuildEvents(et, table) {
	$(table).find('img').each(function(i, img) {
		$(img).unbind('click');
		$(img).click(function() {
			openTab($(img).parent().find('input').val());
		});
	});
	$(table).find('input').each(function(i, ei) {
		$(ei).on('input', function() {
			recalcTable(et, table);
		});
	});
}

$(document).ready(function() {
	var focusN = window.location.hash.replace('#', '');
	
	$('#fullscreen_window .close').click(function() {
		$('#fullscreen_window').addClass('hidden');
	});
	
	$('.tender').each(function(i, et) {
		if($(et).find('.id').text() != focusN && focusN.length) {
			et.remove();
			return;
		}
		
		$(et).find('.commentary').on('input', function() {
			$.post('favorite.php', {
				csrf: csrf_token,
				action: 'save_commentary',
				tid: $(et).find('.btn_favorite').attr('tid'),
				commentary: $(et).find('.commentary').html()
			}).done(function() {
				
			});
		});
		$(et).find('.btn-show').click(function() {
			$('#fullscreen_window').removeClass('hidden');
			$('#fullscreen_window .content').html($(et).find('.commentary').html());
			$('#fullscreen_window .content').unbind('input');
			$('#fullscreen_window .content').on('input', function() {
				$(et).find('.commentary').html($('#fullscreen_window .content').html());
				$.post('favorite.php', {
					csrf: csrf_token,
					action: 'save_commentary',
					tid: $(et).find('.btn_favorite').attr('tid'),
					commentary: $('#fullscreen_window .content').html()
				}).done(function() {
					
				});
			});
		});
		
		var table = $(et).find('.solution');
		
		$(table).find('input').each(function(i, ei) {
			$(ei).val($(ei).attr('hidden-value'));
		});
		
		rebuildEvents(et, table);
		recalcTable(et, table);
		
		$(et).find('.btn-plus').click(function() {
			var n = $(table).find('tbody:last-child tr').length + 1;
			
			var row = $('<tr>');
			$(table).find('tbody:last-child').append(row);
			
			var cell = $('<td>');
			cell.text(n);
			row.append(cell);
			
			for(var i = 0;i < 4;i++) {
				cell = $('<td><input type="text"></td>');
				row.append(cell);
			}
			
			cell = $('<td><img src="image/link.png"><input type="text"></td>');
			row.append(cell);
	
			rebuildEvents(et, table);
		});
		$(et).find('.btn-minus').click(function() {
			table.find('tbody:last-child tr:last-child').remove();
			rebuildEvents(et, table);
		});
	});
});
