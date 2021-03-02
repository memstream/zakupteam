<?php
require_once __DIR__ . '/simple_html_dom.php';
require_once __DIR__ . '/rb.php';

function multiexplode($delimiters, $string) {
	$ready = str_replace($delimiters, $delimiters[0], $string);
	$launch = explode($delimiters[0], $ready);
	return  $launch;
}

function replace_first($str_pattern, $str_replacement, $string){
	if(strpos($string, $str_pattern) !== false){
		$occurrence = strpos($string, $str_pattern);
		return substr_replace($string, $str_replacement, strpos($string, $str_pattern), strlen($str_pattern));
	}
	return $string;
}

function correct_date_format($str) {
	$str = str_replace('.', '-', $str);
	$str = str_replace('в ', '', $str);
	
	if(substr_count($str, ':') == 1) $str = trim($str) . ':00';
	if(strlen(explode('-', $str)[0]) != 4) { 
		$e = multiexplode(['-', ' '], trim($str));
		
		$str = replace_first($e[0], 'A', $str);
		$str = replace_first($e[1], 'B', $str);
		$str = replace_first($e[2], 'C', $str);
		
		
		$str = str_replace('A', $e[2], $str);
		$str = str_replace('B', $e[1], $str);
		$str = str_replace('C', $e[0], $str);
	}
	return $str;
}

function zakupki_curl($url) {
	$userAgent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FAILONERROR, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept-Language: ru']);
	return curl_exec($ch);
}

function zakupki_search_url($p, $i, $m, $n, $f) {
	$url = 'https://zakupki.gov.ru/epz/order/extendedsearch/results.html?searchString=';
	$url = $url . urlencode($i);
	$url = $url . '&morphology=on&search-filter=%D0%94%D0%B0%D1%82%D0%B5+%D1%80%D0%B0%D0%B7%D0%BC%D0%B5%D1%89%D0%B5%D0%BD%D0%B8%D1%8F&pageNumber='; 
	$url = $url . urlencode($p);
	$url = $url . '&sortDirection=false&recordsPerPage=_10&showLotsInfoHidden=false&savedSearchSettingsIdHidden=&sortBy=UPDATE_DATE&fz';
	$url = $url . urlencode($f);
	$url = $url . '=on&af=on&placingWayList=&selectedLaws=&priceFromGeneral=';
	$url = $url . urlencode($n);
	$url = $url . '&priceFromGWS=&priceFromUnitGWS=&priceToGeneral=';
	$url = $url . urlencode($m);
	$url = $url . '&priceToGWS=&priceToUnitGWS=&currencyIdGeneral=1&publishDateFrom=&publishDateTo=&applSubmissionCloseDateFrom=&applSubmissionCloseDateTo=&customerIdOrg=&customerFz94id=&customerTitle=&okpd2Ids=&okpd2IdsCodes=';
	return $url;
}

function zakupki_filter($zakupki, $exclude_text) {
	if($exclude_text === '') return $zakupki;
	$filtered = [];
	$exclude_words = explode(' ', $exclude_text);
	foreach($zakupki as $zakupka) {
		$text = $zakupka['org'] . ' ' . $zakupka['type'] . ' ' . $zakupka['info'];
		$ignore = false;
		foreach($exclude_words as $word) {
			if(mb_strpos(mb_strtoupper($text), mb_strtoupper($word)) !== FALSE) {
				$ignore = true;
				break;
			} 
		}
		if(!$ignore) {
			array_push($filtered, $zakupka);
		}
	}
	return $filtered;
}

function zakupki_search($p, $i, $e, $m, $n, $f) {
	$html = zakupki_curl(zakupki_search_url($p, $i, $m, $n, $f));
	$doc = str_get_html($html);
	
	$zakupki = [];
	foreach($doc->find('.search-registry-entry-block') as $block) {
		$href = 'https://zakupki.gov.ru' . $block->find('.registry-entry__header-mid__number a', 0)->href;
		$type = str_replace('44-ФЗ ', '', trim($block->find('.registry-entry__header-top__title', 0)->plaintext));
		if(strpos($type, 'Закрытый аукцион') !== false) continue;
		$zakupka = [
			'n' => trim(mb_substr(trim($block->find('.registry-entry__header-mid__number a', 0)->plaintext), 1)),
			'price' => trim($block->find('.price-block__value', 0)->plaintext),
			'info' => trim($block->find('.registry-entry__body-value', 0)->plaintext),
			'org' => trim($block->find('.registry-entry__body-href a', 0)->plaintext),
			'type' => $type,
			'href' => $href
		];
		
		$t = R::findOne('tender', ' n = ? ', [ $zakupka['n'] ]);
		if(!$t) {
			$t = R::dispense('tender');
			$t->n = $zakupka['n'];
			$t->price = $zakupka['price'];
			$t->info = $zakupka['info'];
			$t->org = $zakupka['org'];
			$t->type = $zakupka['type'];
			$t->href = $zakupka['href'];
			R::store($t);
		}
		
		$zakupka['id'] = $t->id;
		
		array_push($zakupki, $zakupka);
	}
	
	$pages = [];
	foreach($doc->find('.paginator-block .pages .link-text') as $page) {
		array_push($pages, trim($page->plaintext));
	}
	return [
		'zakupki' => zakupki_filter($zakupki, $e),
		'pages' => $pages
	];
}

function zakupki_subinfo($tid) {
	$t = R::findOne('tender', ' n = ? ', [ $tid ]);
	if(!$t) return null;
	
	$files = [];
	$tradedate = null; 
	$ending = $t->ending;
	if(!$ending) {
		$doc = str_get_html(zakupki_curl($t->href));
		foreach($doc->find('.blockInfo__section') as $section) {
			if(strpos($section->plaintext, 'Дата и время окончания срока подачи заявок') !== false) {
				$ending = correct_date_format($section->find('.section__info', 0)->plaintext);	
			} else if(strpos($section->plaintext, 'Дата проведения аукциона в электронной форме') !== false) {
				$tradedate = correct_date_format($section->find('.section__info', 0)->plaintext);
			}
		}
		
		$doc = str_get_html(zakupki_curl(str_replace('common-info', 'documents', $t->href)));
		foreach($doc->find('.closedInactiveDocuments') as $block) {
			foreach($block->find('.attachment') as $attach) {
				$file = [
					'img' => 'https://zakupki.gov.ru/' . $attach->find('img', 0)->src,
					'title' => $attach->find('.section__value a', 0)->plaintext,
					'href' => $attach->find('.section__value a', 0)->href
				];
				
				$attach = R::dispense('attach');
				$attach->tender_id = $t->id;
				$attach->img = trim($file['img']);
				$attach->title = trim($file['title']);
				$attach->href = trim($file['href']);
				R::store($attach);
				
				array_push($files, $file);
			}
		}
		
		$t->ending = $ending;
		$t->tradedate = $tradedate;
		R::store($t);
	} else {
		foreach(R::find('attach', ' tender_id = ? ', [ $t->id ]) as $file) {
			array_push($files, $file);
		}
	}
	
	return [
		'ending' => $ending,
		'files' => $files
	];
}
