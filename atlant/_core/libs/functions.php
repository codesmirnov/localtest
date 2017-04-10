<?php

function display_errors() { 
  ini_set('display_errors',1);
  ini_set('display_startup_errors',1);
  error_reporting(-1);
}

function debug($val = null, $backtrace = true) {
  if ($backtrace) {
  	$calledFrom = debug_backtrace();
  	echo '<strong>' . substr(str_replace(ROOT, '', $calledFrom[0]['file']), 1) . '</strong>';
  	echo ' (line <strong>' . $calledFrom[0]['line'] . '</strong>)';
  }
  echo '<pre>';
  print_r($val);
  echo '</pre>';
}

function getMicrotime() {
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}

function h($text, $charset = null) {
	if (is_array($text)) {
		return array_map('h', $text);
	}
	if (empty($charset)) {
		#$charset = Configure::read('App.encoding');
	}
	if (empty($charset)) {
		$charset = 'UTF-8';
	}
	return htmlspecialchars($text, ENT_QUOTES, $charset);
}

function startTimeDebug() {
  global $getMicrotimeGlobal;
  $getMicrotimeGlobal = getMicrotime();
}

function stopTimeDebug($showOut = true) {
  global $getMicrotimeGlobal;
  $out = getMicrotime() - $getMicrotimeGlobal; 
  if ($showOut)
    debug($out*1000, true, false);
  return $out;
}

function camelize($lowerCaseAndUnderscoredWord) {
	return str_replace(" ", "", ucwords(str_replace("_", " ", $lowerCaseAndUnderscoredWord)));
}

function underscore($camelCasedWord) {
	return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $camelCasedWord));
}

function parseArrayString($string = '') {
  if (! empty($string)) {
    $temp  = explode(',', str_replace(', ', ',', $string));
    $array = array();
    foreach ($temp as $value) {
      list($key, $value) = explode('=', $value);
      if (! $value)
        $array[] = $key;
      else
        $array[$key] = $value;
    }
    
    return $array;
  }
  
  return array();
}

function array_to_params($array) {
	$out = '';
	foreach ($array as $key => $param) {
		$out .= $key . '=' . $param . '&';
	}
	return $out;
}

function array_next($array, $current) {  
  $aliases = array_keys($array);
  $pos = array_flip($aliases);
  
  if (! isset($pos[$current]))
    return false;
  
  if (! isset($aliases[$pos[$current] + 1]))
    return false;
  
  return $aliases[$pos[$current] + 1];
}

function array_prev($array, $current) {  
  $aliases = array_keys($array);
  $pos = array_flip($aliases);
  
  if (! isset($pos[$current]))
    return false;
  
  if (! isset($aliases[$pos[$current] - 1]))
    return false;
  
  return $aliases[$pos[$current] - 1];
}

function parse_get_params($str) {
	$result = array();
	$params = explode('&', $str);
	foreach ($params as $param) {
		$param = explode('=', $param);
		$result[$param[0]] = $param[1];
	}
	return $result;
}

function keyValue($arr = array()) {
  $out = array();
  foreach ($arr as $value)
    $out[$value] = $value;
  return $out;
}

function translit($st) {
	$st = trim($st);

	$st = strtr($st, "àáâãäå¸çèéêëìíîïðñòóôõúûý",
			"abvgdeeziyklmnoprstufh'iei");
	$st = strtr($st, "ÀÁÂÃÄÅ¨ÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÚÛÝ",
			"ABVGDEEZIYKLMNOPRSTUFH'IEI");

	$st = strtr($st,
	array("æ" => "zh", "ö" => "ts", "÷" => "ch", "ø" => "sh",
		"ù" => "shch", "ü" => "", "þ" => "yu", "ÿ" => "ya",
		"Æ" => "ZH", "Ö" => "TS", "×" => "CH", "Ø" => "SH",
		"Ù" => "SHCH", "Ü" => "", "Þ" => "YU", "ß" => "YA",
		"¿" => "i", "¯" => "Yi", "º" => "ie", "ª" => "Ye"
		)
	);

	$st = strtolower(str_replace(" ", "_", $st));
	$st = strtolower(str_replace("/", "_", $st));
	$st = preg_replace('/([^0-9a-z_])+/', '', $st);
	$st = str_replace(array('(', ')', '\'', '"'), '', $st);

	return $st;
}

function translit_utf8($str) {
	$str = iconv('UTF-8', 'WINDOWS-1251', $str);
	$str = translit($str);
	$str = iconv('WINDOWS-1251', 'UTF-8', $str);
	return $str;
}

function tolower_utf8($str) {
  $str = iconv('utf-8', 'windows-1251', $str);
  $str = strtolower($str);
  $str = iconv('windows-1251', 'utf-8', $str);
  return $str;
}

function array_keys_exist($a, $keys = array()) {
  if (empty($keys)) {
    return false;
  }

  foreach ($keys as $key) {
    if (isset($a[$key])) {
      return true;
    }
  }

  return false;
}

function array_extract($array, $key, $object = false, $unique = false, $uniqueKey = '') {
  $result = array();
  foreach ($array as $a) {
    if ($unique) {
      $val = $object ? $a->{$key} : $a[$key];
      $ukey = $object ? $val->{$uniqueKey} : $val[$uniqueKey];
      $result[$ukey] = $val;
    } else {
      $result[] = $object ? $a->{$key} : $a[$key];
    }
  }

  return $result;
}

function textlimiter($text, $limit) {
	$text = strip_tags($text);

	if (strlen($text) >= $limit) {
		$words = explode(' ', $text);
		$text = '';
		foreach ($words as $word)
			if (strlen($text . ' ' . $word) < $limit)
				$text .= ' ' . $word;
			else break;

    $text = preg_replace('~\.(.+)$~', '.', $text);
    $text .= '...';
	}

	return $text;
}

function prep($array = array()) {
  if (empty($array))
      return false;

  if (is_array($array)) {   
    reset($array);
    $key = key($array);
    if (is_numeric($key)) {
      foreach ($array as $i => $a) {
        $array[$i] = prep($a);
      }
    } else{
      $obj = new stdClass();
      foreach ($array as $key => $value) {
        if (is_array($value))  
          $obj->{$key} = prep($value);
        else
          $obj->{$key} = $value;   
      }
      
      return $obj;
    }
  }
  
  return $array;
}

function FormatByType($value, $type) {
	switch ($type) {
		case 'a': // array
			if (is_array($value))
				return $value;
			else
				return array();
		case 'b': return (!empty($value) ? true : false); // boolean
		case 'd': // date dd.mm.yyyy
			if (preg_match("/([0-3]?[0-9])\D*([0-1]?[0-9])\D*([0-9]{4})/", $value, $a))
				return $a[1] . '.' . $a[2] . '.' . $a[3];
			elseif (preg_match("/([0-9]{4})\D*([0-1]?[0-9])\D*([0-3]?[0-9])/", $value, $a))
				return $a[3] . '.' . $a[2] . '.' . $a[1];
			else
				return false;
		case 'f': return floatval($value); // float
		case 'i': return intval($value); // integer
		case 'n': return preg_replace("/\D+/", '', $value); // digits only string (without "-")
		case 's': // slashed trimmed string
			if (get_magic_quotes_gpc())
				return trim($value);
			else
				return trim(addslashes($value));
		case 't': // trimmed text
		default:
			if (get_magic_quotes_gpc())
				return trim(stripslashes($value));
			else
				return trim($value);
	}
}

function Arg($argName, $type = '', $order = array('_GET', '_POST', '_COOKIE', '_SESSION')) {
	if (!is_array($order)) {
		HandleWarning('Function Arg: parameter aOrder is not array!');
		return false;
	}

	foreach ($order as $s) {
		global ${$s};
		if (isset(${$s}[$argName])) {
			if (empty($type))
				return ${$s}[$argName];
			else
				return FormatByType(${$s}[$argName], $type);
		}
	}
	return FormatByType(false, $type);
}

$__optimizeDataParams = array();

function prepOptimize($array = array(), &$params, &$controller) {
  global $__optimizeDataParams;
  if (empty($array))
      return false;
        
  if (is_array($array) && isset($array[0])) {
    foreach ($array as $i => $a) {
      $array[$i] = prepOptimize($a, $params, $controller);
    }
  } elseif (is_array($array)) {
    $obj = new optimizeData($params, $controller);
    foreach ($array as $key => $value) {
      if (is_array($value)) {
        $__optimizeDataParams[$key] = array('hash' => $params['hash']);
        $params[$key] = &$__optimizeDataParams[$key];
        $obj->_fields[$key] = prepOptimize($value, $__optimizeDataParams[$key], $controller);
      } else
        $obj->_fields[$key] = $value;   
    }
    
    return $obj;
  }
  
  return $array;
}

$__optimizeDataControllers = array();

class optimizeData {
      
  var $params   = array();
  var $_fields  = array();
  
  function __construct(&$params, &$controller) {  
    global $__optimizeDataControllers;    
    $__optimizeDataControllers[$params['hash']] = &$controller;        
    $this->params = &$params;
    $this->params['fields'] = array();
  }
  
  function __get($name) {    
    global $__optimizeDataControllers;         
    $controller = &$__optimizeDataControllers[$this->params['hash']];       
    if (! isset($this->_fields[$name]) && ! in_array($name, $this->params['fields']) && ! isset($this->params['contain'][$name]) &&  ! $controller->optimizeCrash) {
      foreach (ob_list_handlers() as $a)
        ob_end_clean();     
      $controller->optimizeCrash = true;
      $controller->__construct();
      exit;
    } elseif (! @in_array($name, $this->params['fields']) && ! isset($this->params[$name])) {
      $this->params['fields'][] = $name;
    } 
    
    return $this->_fields[$name];
  }
  
  function _check($name) {
    if (isset($this->_fields[$name]) && (! empty($this->_fields[$name]) || $this->_fields[$name] == 0))
      return true;
    else
      return false;
  }  
  
  function _debug() {
    debug($this->_fields);
  }
}

function declension($digit,$expr,$onlyword=false) {
	if(!is_array($expr)) $expr = array_filter(explode(' ', $expr));
	if(empty($expr[2])) $expr[2]=$expr[1];
	$i=preg_replace('/[^0-9]+/s','',$digit)%100; //intval íå âñåãäà êîððåêòíî ðàáîòàåò
	if($onlyword) $digit='';
	if($i>=5 && $i<=20) $res=$digit.' '.$expr[2];
	else
	{
		$i%=10;
		if($i==1) $res=$digit.' '.$expr[0];
		elseif($i>=2 && $i<=4) $res=$digit.' '.$expr[1];
		else $res=$digit.' '.$expr[2];
	}
	return trim($res);
}

function md5solt($val) {
  return md5(md5($val) . ')(&v5xc');
}

function securityHash($string, $type = null, $salt = false) {
  $salt   = 'DKH*hy)(#$H3h()*H(F(Weh89B@#(*#H)*F@FLUu';
  $string = $salt . $string;
  if (empty($type)) {
  	$type = $_this->hashType;
  }
  $type = strtolower($type);
  
  if ($type == 'sha1' || $type == null) {
  	if (function_exists('sha1')) {
  		$return = sha1($string);
  		return $return;
  	}
  	$type = 'sha256';
  }
  
  if ($type == 'sha256' && function_exists('mhash')) {
  	return bin2hex(mhash(MHASH_SHA256, $string));
  }
  
  if (function_exists('hash')) {
  	return hash($type, $string);
  }
  return md5($string);
}

function stripslashes_deep($values) {
	if (is_array($values)) {
		foreach ($values as $key => $value) {
			$values[$key] = stripslashes_deep($value);
		}
	} else {
		$values = stripslashes($values);
	}
	return $values;
}

function del_array_value(&$data, $field) {   
  $crumbs = explode('.', $field);
  
  $value = &$data;
  foreach ($crumbs as $i => $crumb) {
    if (count($value[$crumb]) <= 1 || $i >= count($crumbs) - 1) {
      unset($value[$crumb]);
      return true;
    } else
      $value = &$value[$crumb];
  }
}


function put_array_value(&$data, $field, $put) {   
  $crumbs = explode('.', $field);
  
  $value = &$data;
  foreach ($crumbs as $crumb) {
    $value = &$value[$crumb];
  }
    
  $value = $put;
}

function get_array_value($data, $field) {    
  $crumbs = explode('.', $field);
  
  $value = $data;
  foreach ($crumbs as $crumb) {
    if (isset($value[$crumb]))
      $value = $value[$crumb];
    else
      return false;
  }
    
  return $value;
} 

function fileCopy($field = 'Filedata', $path, $options = array()) {
  $default = array(
    'extensions' => array('jpeg', 'jpg', 'png', 'gif', 'mp4'),
    'limit'      => 2097152
  );
  
  $options = array_merge($default, $options);
  
  if (is_string($field) && isset($_FILES[$field]))
  	$data = $_FILES[$field];  
   
   if (is_array($field))
    $data = $field;  
  
  if (! empty($data) && ! $data['error']) {
    if (! file_exists(ROOT . $path)) 
      mkdir(ROOT . $path, 0777);
    
  	preg_match('~\.([^\.]+)$~', $data['name'], $matches);
  	$extension = $matches[1];
    
  	preg_match('~([^\\\/]+)$~', $data['tmp_name'], $matches);
  	$tmpName = preg_replace('~\.([^\.]+)$~', '', $matches[1]);
    
    if (! in_array(strtolower($extension), $options['extensions']))
      return false;
      
    if ($options['limit'] != 0 && filesize($data['tmp_name']) > $options['limit'])
      return false;

    $fileName = '';
    if (isset($options['file_name'])) {
      $fileName = $options['file_name'];
    } else {    
      $fileName = $tmpName . '.' . $extension;  
    }    

    $path = $path . '/' . $fileName;  
    
    if (copy($data['tmp_name'], ROOT . $path)) {
      return $path;
    }
  }
  
  return false;
}

function isAjax() {
  if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
    return true;
  else
    return false;
}

function curl_get($url) {
	$process = curl_init($url);
	curl_setopt($process, CURLOPT_HEADER, 0);
	curl_setopt($process, CURLOPT_TIMEOUT, 2);
	curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
	$return = curl_exec($process);
	curl_close($process);

	return $return;
}

function spacing($text) {
  $text = preg_replace('~([À-ßA-Z][À-ßA-Z]+)~', '<span class="spacing">$1</span>', iconv('UTF-8', 'WINDOWS-1251//ignore', $text));
  return iconv('WINDOWS-1251', 'UTF-8//ignore', $text);
}

function RandomString($l)
{
    $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $randstring = '';
    for ($i = 0; $i < $l; $i++) {
      if ($i % 6 == 0) 
        $randstring .= ' ';
      $randstring .= $characters[rand(0, strlen($characters))];
    }
    return $randstring;
}

 
function yandexGeocode($query) {
  $r = file_get_contents('http://geocode-maps.yandex.ru/1.x/?geocode=' . urlencode($query) . '&format=json');
  if (! empty($r)) {
    $r = json_decode($r);
    if ($r->response->GeoObjectCollection->metaDataProperty->GeocoderResponseMetaData->found > 0) {
      $point = $r->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos;
      list($lon, $lat) = explode(' ', $point);  
      return array('lon' => $lon, 'lat' => $lat);
    }
  }
}

function calendarDay($d) {
  $result = $d;
  $n = date('N');

  if ($n + $d > 5) {
    $result += 2 * round($d / 5);
  }

  return $result;
}

?>