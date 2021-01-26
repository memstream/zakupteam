<?php
function api_auth() {
	return isset($_SESSION['auth']);
}

function api_to($url, $params = array(), $skipcsrf = true) {
	$skipcsrf = !$skipcsrf;
	
	$first = true;
	if(strstr($url, '?') !== false) {
		$first = false;
	}
	foreach($params as $k => $v) {
		if($k == 'csrf') $skipcsrf = true; 
		if($first) $url = $url . '?';
		else $url = $url . '&';
		
		$url = $url . urlencode($k) . '=' . urlencode($v);
		$first = false;
	}
	if(!$skipcsrf) {
		$url = $url . ($first ? '?csrf=' : '&csrf=') . urlencode($_COOKIE['csrf']);
	}
	return $url;
}

function api_redirect($url, $params = array(), $skipcsrf = true) {
	header('Location: ' . api_to($url, $params, $skipcsrf));
	die;
}

function api_route($actions) {
	$req = array_merge($_POST, $_GET);
	if(empty($req['action'])) return;
	
	if(empty($_COOKIE['csrf']) || ($_COOKIE['csrf'] !== $req['csrf'])) {
		http_response_code(400);
		die;
	}
	
	foreach($actions as $name => $fn) {
		$auth = true;
		$args = array();
		$e = explode('#', $name);
		if(count($e) > 1) {
			$name = $e[0];
			$args = explode(',', $e[1]);
		}
		
		if($name[0] == '@') {
			$name = substr($name, 1);
			$auth = false;
		}
		
		if($auth) {
			if(!api_auth()) {
				api_redirect('login.php');
			}
		}
		
		if($req['action'] === $name) {
			$params = array();
			foreach($args as $arg) {
				$need = true;
				$type = 'str';
				$e = explode(':', $arg);
				if(count($e) > 1) {
					$arg = $e[0];
					$type = $e[1];
				}
				
				if($arg[0] == '?') {
					$arg = substr($arg, 1);
					$need = false;
				}
				
				if(empty($req[$arg])) {
					if($need) {
						http_response_code(400);
						die;
					}
				} else {					
					if($type == 'str') {
						array_push($params, $req[$arg]);
					} else if($type == 'int') {
						array_push($params, intval($req[$arg]));
					} else if($type == 'float') {
						array_push($params, floatval($req[$arg]));
					} else {
						error_log('api_rute: bad type '. $type);
						http_response_code(500);
						die;
					}
				}
			}
			
			call_user_func_array($fn, $params);
			die;
		}
	}
}

function api_hidden($name, $value) {
	echo '<input type="hidden" name="' . $name . '" value="' . $value . '">';
}

function api_form($to, $action, $method = 'POST', $class = '') {
	echo '<form method="' . $method . '" action="' . $to . '" class="' . $class . '">';
	api_hidden('action', $action);
	api_hidden('csrf', $_COOKIE['csrf']);
}

function api_form_end() {
	echo '</form>';
}


