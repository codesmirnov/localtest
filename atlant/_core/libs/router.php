<?php

class Router {
  
  var $routes = array(); 
  
  var $regexps = array();
  
  var $from = '';
  var $module = '';
  
	static function &getInstance() {
		static $instance = array();

		if (!$instance) {
			$instance[0] =& new Router();
		}
		return $instance[0];
	}
  
  static function from($url) {
		$_this =& Router::getInstance();
    $_this->from = $url;
  }
  
  static function module($name) {
		$_this =& Router::getInstance();
    $_this->module = $name;
  }
  
  static function clear() {
		$_this =& Router::getInstance();
    $_this->from   = '';
    $_this->module = '';
  }
  
  static function prepRoute(&$route) {
    preg_match_all('/:([^\/]+)/', $route, $matches);
    $names = $matches[1];
    if (! empty($names))
      foreach ($names as &$name)
        $name = preg_replace('/(\[.+\])$/i', '', $name);
    $route = preg_replace('/^\//', '', $route);
    $route = preg_replace('/\/$/', '', $route);
    $regexp = preg_replace('/:([^\[]+)\[([^\]]+)\]/', '([$2]+)', $route);
    $regexp = preg_replace('/:([^\/]+)/', '([^/]+)', $regexp);
    $regexp = str_replace('*', '(.*)', $regexp);
    $regexp = str_replace('/', '\/', $regexp);
    
    $regexp = '^' . $regexp . '';
    
    return array(
      'regexp' => $regexp,
      'names'  => $names
    );
  }
  
  static function connect($route, $params = array()) {
		$_this =& Router::getInstance();
    
    $route = $_this->from . $route;
    
    $params = array_merge($params, $_this->prepRoute($route));
    
    if (isset($params['path']) && ! empty($params['path'])) {
      $params['path'] = ROOT . $params['path'];
    } elseif (! empty($_this->module) && isset($params['controller']) && ! empty($params['controller'])) {
      $params['path'] = MODS . $_this->module . DS . 'controllers' . DS . $params['controller'] . '.php';
      $params['root'] = MODS . $_this->module . DS;
    }
        
    $_this->routes[$route] = $params;
  }
  
  static function url($url) {
    $url = preg_replace('/^\//', '', $url);
    $url = preg_replace('/\/$/', '', $url);
    return $url;
  }
  
  static function matchRoute($url, $options) {
    preg_match('/' . $options['regexp'] . '/i', $url . '/', $match);
    
    if (! empty($match)) {      
      $params = array();
      $base = array_shift($match);
      $last = array_pop($match);
      $last = preg_replace('/\/$/', '', $last);
      $match[] = $last;
      if (! empty($options['names']))
        foreach ($options['names'] as $i => $name) 
          $params[$name] = array_shift($match);      
        
      $options['url'] = $url;  
      
      if (! empty($match[0]))
        $match = explode('/', $match[0]);
    
      return array(
        'options' => $options,
        'params'  => $params,
        'match'   => $match
      );
    }
    
    return false;
  }
  
  static function matchRoutes($url) {
		$_this =& Router::getInstance();
    
    $url = $_this->url($url);
     
    if (! empty($_this->routes))
    foreach ($_this->routes as $options) {
      if ($result = $_this->matchRoute($url, $options)) {
        return $result;
      }
    }
  }
}


?>