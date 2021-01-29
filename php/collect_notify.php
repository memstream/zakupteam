<?php
if($argv[1] !== 'cron') die;

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/rb.php';
require_once __DIR__ . '/zakupki.php';

R::setup('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
R::useFeatureSet('novice/latest');

function handle_page($rule, $zakupki) {
	$zakupki = zakupki_filter($zakupki, $rule->exclude); 
	foreach($zakupki as $zakupka) {
		if(!R::find('notify', ' tender_id = ? ', [ $zakupka['id'] ])) {
			zakupki_subinfo($zakupka['n']);
			$n = R::dispense('notify');
			$n->notifyrule_id = $rule->id;
			$n->tender_id = $zakupka['id'];
			R::store($n);
		}
	}
}

foreach(R::findAll('notifyrule') as $rule) {
	$r = zakupki_search(1, $rule->include, $rule->exclude, $rule->max, $rule->min, $rule->fz);
	if(!$r['pages']) continue;
	handle_page($rule, $r['zakupki']);
	$max_page = $r['pages'][count($r['pages'])-1];
	for($p = 2;$p < $max_page+1 && $p < MAX_NOTIFY_PAGE;$p++) {
		sleep(NOTIFY_SLEEP);
		$r = zakupki_search($p, $rule->include, $rule->exclude, $rule->max, $rule->min, $rule->fz);
		handle_page($rule, $r['zakupki']);
	}
}
