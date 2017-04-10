<?php

class Controller {
  
  var $params   = array();
  
  var $root     = '';
  
  var $view     = null;
  
  var $viewPath = '';
  
  var $formError = array();
  
  var $layout   = '';
  
  var $action  = '';
  
  var $models  = array();
  
  var $data    = array();
  
  var $ajax  = false;
  
  var $route = array();
  
  var $optimizeCrash = false;
  var $optimizeModel = array();
  
  var $cache = false;
  
  function __construct($route = false, $options = array()) {   
    
    if (is_object($route)) {
      $parent = $route;
      
      foreach ($parent as $property => $value) {
        if (! isset($this->{$property}) || ! $this->{$property} || empty($this->{$property}))
          $this->{$property} = $value;
      }
      
      foreach ($options as $property => $value)
        $this->{$property} = $value;  
      
      $this->view   = new View($parent, $this->root);  
      $this->view->vars   = &$parent->view->vars;
      $this->view->params = &$this->params;
      $this->view->data   = &$this->data;
      $this->view->cache  = &$this->cache;
      $this->data         = $this->data;
      
    } elseif (is_array($route)) {
      extract($route['options']);
      
      $this->root   = $root;
      $this->view   = new View($this, $root);    
      $this->action = $action;
      
      $this->params['url'] = $route['params'];
      $this->params['url']['url'] = $url;
    } else {
      #$this->view = new View($this, $root);         
    }
    
    if (! empty($_GET))
      $this->params['url'] = array_merge($_GET, $this->params['url']);
        
    if (isAjax()) {
      $this->ajax = true;
      $this->layout = 'ajax';
    }
    
    if (! empty($_POST) && empty($this->data)) {
      $_POST = stripslashes_deep( $_POST );
      $this->data = $_POST;
    }
    
    $this->beforeFilter();
    
    if (is_array($route) && isset($route['params']['action']) && empty($this->action))
      $this->action = $route['params']['action'];
    
    if (! empty($this->action)) {
      $this->dispatchMethod($this->action, is_array($route) ? $route['match'] : array());
    }
    
    if (! empty($this->optimizeModel)) {
      $this->optimizeSave();
    }
  }
  
  function beforeFilter() {
  }
  
  function beforeRender() {   
  } 
  
  function set($name, $param, $object = true) {
    if ($object)
      $param = prep($param);
    $this->view->vars[$name] = $param;
  }
  
  function render($template, $layout = false, $viewPath = '') {
    $this->beforeRender();
    $this->view->viewPath = $this->viewPath;

    if ($this->ajax)
      $layout = 'ajax';
    
    echo $this->view->render($template, $layout ? $layout : $this->layout, array(), $viewPath);
  }
  
  function renderError($error = '404') {
    $this->cache = false;
    $this->view->root = MODS . 'client' . DS;
    echo $this->view->element('/_errors/' . $error);
    exit;
  }
  
  function dispatchMethod($method, $params = array()) {
    if (! method_exists($this, $method))
      return false;
		$params = array_values($params);
		switch (count($params)) {
			case 0:
				return $this->{$method}();
			case 1:
				return $this->{$method}($params[0]);
			case 2:
				return $this->{$method}($params[0], $params[1]);
			case 3:
				return $this->{$method}($params[0], $params[1], $params[2]);
			case 4:
				return $this->{$method}($params[0], $params[1], $params[2], $params[3]);
			case 5:
				return $this->{$method}($params[0], $params[1], $params[2], $params[3], $params[4]);
			case 6:
				return $this->{$method}($params[0], $params[1], $params[2], $params[3], $params[4], $params[5]);
			case 7:
				return $this->{$method}($params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6]);
			default:
				return call_user_func_array(array(&$this, $method), $params);
			break;
		}
	}
  
  function afterDynamicRoutes() {
    
  }
  
  function dynamicRoutes($base, $rules) {
    foreach ($rules as $rule => $action) {
      $base  = Router::url($base);
      $rule  = Router::url($rule);
      $rule  = $base . '/' . $rule;
      $route = Router::prepRoute($rule);
    
      if ($result = Router::matchRoute($this->params['url']['url'], $route)) {
        $this->params['url']    = array_merge($this->params['url'], $result['params']);
        $this->params['action'] = $action;
        $this->afterDynamicRoutes();
        if ($action != $this->action) {
          $this->dispatchMethod($action, $result['match']);
          return true;
        } else
          return false;
      }
    }    
    return false;
  }
  
  function loadController($file, $options = array()) {
    if (! isset($options['root']))
      $options['root'] = $this->root;
     
    $name = '';
    $temp = explode('_', str_replace('.php', '', $file));
    foreach ($temp as $t)
      $name .= ucfirst($t);
      
    if (! class_exists($name))
    include $options['root'] . 'controllers' . DS . $file;

    $controller = new $name($this, $options);
    return $controller;
  }
  
  function request($file, $action, $params = array()) { 
    $controller = $this->loadController($file, array('action' => false, 'params' => $params));  
    return $controller->dispatchMethod($action, $params);
  }
  
  function loadModel($model, $options = array()) {
    $this->{$model} = new Model($options);
    $this->models[$model] = $this->{$model};
  }
  
  function json($data) {
    echo json_encode($data);
    exit;
  }
  
  function parseUrl(&$model, $options = array()) {
    $pass = explode('/', $this->params['url']['url']);
    
    if (empty($pass[0])) {
      $pass[0] = '/';
    }
    
    $default = array(
      'conditions' => array('alias' => $pass),
      'fields'     => array('id', 'parent_id', 'title', 'alias', 'url'),
      'order'  => 'lft asc'            
    );
    
    $options = array_merge_recursive($default, $options);
    
    $crumbs = $model->find('list', $options);
    
    $aliases = array_flip($pass);
    
    $current = false;
    foreach ($crumbs as $id => $item) {
      if (! isset($crumbs[$item['parent_id']]) && $pass[0] != $item['alias']) {
        unset($crumbs[$id]);
      } else
        $current = $item;
        
      if (isset($aliases[$item['alias']])) {
        unset($aliases[$item['alias']]);
      }
    }
    
    $this->params['pass']         = array_flip($aliases);   
    $this->params['current']      = $current;
    $this->params['crumbs']       = array_values($crumbs);
  }
  
  function referer() {
		$refere = preg_replace('/http\:\/\/([^\/]+)/u','',$_SERVER['HTTP_REFERER']);
		return $refere;
	}
  
  function crumb($title = '', $alias = '') {
    $this->params['crumbs'][] = array(
      'title' => $title,
      'alias' => $alias
    );
  }
  
  function validate(&$model, $data, $fields) {
    foreach ($fields as $field => $rules) {
      if (get_array_value($data, $field)) {
        foreach ($rules as $name => $rule) {
          extract($rule);
          switch ($name) {
            case 'required' : 
            break;
            case 'email' : 
            break;
            case 'minLength' : 
            break;
            case 'maxLength' : 
            break;
            case 'unique' : 
            break;
          }
        }
      }
    }
  }
  
  function paginate(&$model, $options = array()) {
    $params = array('page', 'limit', 'order');
    foreach ($params as $param)
      if (! isset($options[$param]) && isset($this->params['url'][$param]))
        $options[$param] = $this->params['url'][$param];
        
    extract($options); 
    if (! $page) $page = 1;
    else {
      #$this->params['url']['url'] = str_replace('/' . $page, '', $this->params['url']['url']);
    }
         
    if (! $limit)
      $options['limit'] = $limit = 20;
    
    $count     = $model->find('count', $options);  
    $pageCount = intval(ceil($count / $limit));
    $prevPage  = $page > 1;
    $nextPage  = $count > ($page * $limit);
    
    $this->params['paginate'] = compact('limit', 'page', 'count', 'pageCount', 'prevPage', 'nextPage');
    
    if (isset($options['optimize']) && $options['optimize'])
      $result = $this->optimize($model, $options);
    else
      $result = $model->find('all', $options);
    return $result;
  }
  
  function optimize(&$model, $options = array()) {
    if (! isset($options['method']))
      $options['method'] = 'all';
    if (! isset($options['fields']))
      $options['fields'] = array();
    if (! isset($options['conditions']))
      $options['conditions'] = array();
    
    $hash = md5(json_encode(array(
      'model'      => $model->alias, 
      'action'     => $this->action, 
      'class'      => get_class($this), 
      'conditions' => $options['conditions'], 
      'fields'     => $options['fields']
    )));
    
    if (! isset($options['contain']))
      $options['contain'] = false;
    
    $params = array('hash' => $hash, 'model' => $model->alias);    

    if (! $this->optimizeCrash) {
      if ($data = @file_get_contents(ROOT . DS . '_tmp' . DS . '_optimize' . DS . $hash)) {
        $data = json_decode($data, true);
        $options['fields'] = array_merge($options['fields'], $data['fields']);
        unset($data['fields']);
        
        foreach ($data as $join => $opt)
          if (empty($opt['fields'])) {
            unset($data[$join]);
          }
            
        $params['fields']  = $options['fields'];
        $params['contain'] = $options['contain'] = $data;
      } else
        unset($options['fields']);
    }     
    
    $result = $model->find($options['method'], $options);
    if (! empty($result)) {    
      $result = prepOptimize($result, $params, $this);    
      if (isset($options['method']) && $options['method'] == 'threaded')
        $params['fields'][] = 'parent_id';
        
      $this->optimizeModel[] = &$params;
    } else {      
      $file = $this->optimizeFile($hash);
      @unlink($file);
    }      
    
    return $result;
  }
  
  function optimizeFile($hash) {
    $file = ROOT . DS . '_tmp' . DS . '_optimize' . DS . $hash;
    return $file;
  }
  
  function optimizeSave() {
    foreach ($this->optimizeModel as $model) {
      $hash = $model['hash'];
      unset($model['hash']);
      $file = $this->optimizeFile($hash);
      if ($this->optimizeCrash || ! file_exists($file))
        file_put_contents($file, @json_encode($model));
    }
  }
  
  function clearCache() {
    $files = array_diff(scandir(CACHE), array('.', '..'));
    foreach ($files as $file)
      unlink(CACHE . $file);
  }
  
  function redirect($url) {
    header("Location: $url");
    exit;
  }
}

?>