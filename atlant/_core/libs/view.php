<?php

class View {
  
  var $root = '';
  
  var $vars = array();
  
  var $data = array();
  
  var $templates = array();
  
  var $params = array();
  
  var $controller = false;
  
  var $viewPath = '';
  
  var $helpers = array();
  
  var $uses = array('Html', 'Form', 'Image', 'Custome');
  
  var $ajax = false;
  
  var $cache = false;
  
  var $nocache = array();
  
  function __construct(&$controller = null, $root = '') {
    if ($controller) {
      $this->root   = $root;
      $this->params = &$controller->params;
      $this->data   = &$controller->data;
      $this->cache  = &$controller->cache;
      
      $this->ajax   = $controller->ajax;
      
      $this->controller = &$controller;
    }
  }
  
  function _render($template, $data = array()) {  
    extract($this->vars);
    extract($data);
    $content = file_get_contents($template);
    if ($this->cache)
      $this->nocache($content);
    ob_start();
    eval('?>' . $content . '<?');
    $out = ob_get_clean();
    return $out;
  }
  
  function loadHelpers() {  
    foreach ($this->uses as $helper) {
      $name = strtolower($helper);          
      if (! isset($this->helpers[$name])) {
        $helper = $helper . 'Helper';
        if (! class_exists($helper))
          include  LIBS . 'helpers' . DS . $name . '.php';
          
        $this->helpers[$name] = new $helper($this);  
      }
      
      if (! isset($this->vars[$name]))
        $this->vars[$name] = &$this->helpers[$name];
    }
  }
  
  function render($template, $layout = '', $data = array(), $viewPath = '') {
    $this->loadHelpers();
    
    if (empty($viewPath) && ! empty($this->viewPath))
      $viewPath = $this->viewPath;
    
    if ($template{0} == '/')  
      $template = $this->root . 'views' . DS . $template . '.ctp';
    else
      $template = $this->root . 'views' . DS . $viewPath . DS . $template . '.ctp';
      
    $out = $this->_render($template, $data);
    
    if (! empty($layout)) {
      $layout = $this->root . 'layouts' . DS . $layout . '.ctp';
      
      if (isset($this->helpers['html'])) {
        $this->helpers['html']->_templateResources = $this->helpers['html']->_resources;
        $this->helpers['html']->_resources = array();
      }
      
      $out = $this->_render($layout, array('content_for_layout' => $out));
    
      if ($this->cache)
        $this->caching($out); 
    }
    
    return $out;
  }
  
  function nocache($content) {
    preg_match_all('~<nocache name="([^"]+)">(.+)</nocache>~Usi', $content, $matches);
    if (! empty($matches[1]))
      foreach ($matches[2] as $i => $match) {
        $this->nocache[$matches[1][$i]] = $match;
      }
  }
  
  function nocacheReplace($matches) {
    return $this->nocache[$matches[1]];
  }
  
  function caching(&$content) { 
    $cache   = preg_replace_callback('~<nocache name="([^"]+)">(.+)</nocache>~Usi', array($this, 'nocacheReplace'), $content);
    $content = preg_replace('~<nocache[^>]+>~Usi', '', $content);
    $content = str_replace('</nocache>', '', $content);
    
    $name   = md5(json_encode(array_merge($_GET, array('ajax' => $this->ajax))));
    
    file_put_contents(CACHE . $name, $cache);
  }
  
  function element($template, $data = array()) {
    return $this->render($template, '', $data);
  }
}

?>