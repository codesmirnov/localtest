<?php

class Sitemap extends Controller {
  
  var $viewPath = 'sitemap';
  
  function beforeFilter() {    
    $current           = &$this->params['current'];
    $current['Params'] = json_decode($current['params'], true);
  }
  
  function _setup() {
    $models = $this->Model->find('index', array('fields' => array('id', 'name')));
    $this->set('models', $models);    
    
    $this->render('_setup');
  }
  
  function loadPageModel($loadAssocc = true) {
    $model = $this->params['current']['Params']['model'];
    if (! empty($model)) {
      if ($current['Model'] = $this->Model->find('first', array(
        'conditions' => array('id' => $model)
      ))) {     
        extract($current['Model']);
        $assocc = json_decode($assocc, true);          
        if (! $assocc || ! $loadAssocc) $assocc = array();   
        $this->loadModel('Page', array_merge($assocc, array('table' => $table)));
      }
    }
  }
  
  function setup($file) {
    if (isset($this->params['url']['id']) && is_numeric($this->params['url']['id'])) {    
      $this->loadPageModel();    
      
      $this->data = $this->Page->find('first', array(
        'conditions' => array('id' => $this->params['url']['id'])
      ));
      if (! empty($this->data['params']))
        $this->data['Params'] = json_decode($this->data['params'], true);
    }
      
    $this->loadController($file, array(
      'root'   => MODS . 'client' . DS,
      'action' => '_setup'
    ));
  }
  
  function index() {    
    $routes = array_merge(array(
      'restruct'   => 'restruct',
      '_setup/*'   => 'setup',
      'checkbox/*' => 'checkbox'), $this->actionSet['tree']);
    if (! $this->dynamicRoutes('/admin/sitemap/', $routes)) {    
      $this->loadPageModel();      
    
      $map = $this->Page->find('threaded', array('order' => 'pos'));
      
      if (empty($map)) {
        $this->Page->save(array(
          'title' => 'Главная',
          'alias' => '/'
        ));
        $map = $this->Page->find('threaded', array('order' => 'pos'));
      }
      
      $this->set('map', $map);
      
      $fields = $this->Model->Groups->Fields->find('all', array(
        'conditions' => array(
          'file'     => 'checkbox.ctp',
          'model_id' => $this->params['current']['Params']['model'])));
      $this->set('fields', $fields);
      
      if (! isset($this->params['url']['mode']))
        $this->params['url']['mode'] = 'sideform';
      elseif ($this->params['url']['mode'] == 'checkbox' && ! isset($this->params['url']['field']))
        $this->params['url']['field'] = 'is_public';
      
      $this->render('index');
    }
  }
  
  function remove() {
    extract($this->data);
    
    if (isset($id) && isset($parentId)) {
      $this->loadPageModel();
      $this->Page->setParent($id, $parentId);
    }    
  }
  
  function order() {     
    if (isset($this->data['pos']) && ! empty($this->data['pos'])) {
      $this->loadPageModel();  
      foreach ($this->data['pos'] as $pos => $id)
        $this->Page->save(array('id' => $id, 'pos' => $pos . ''));
    }
  }
  
  function edit() {     
    if (! empty($_POST)) {
      if (isset($this->data['Params'])) 
        $this->data['params'] = json_encode($this->data['Params']);
    }
        
    $this->loadController('objects.php', array(
      'action' => 'edit'
    )); 
  }
  
  function del() { 
    if (isset($this->params['url']['id'])) {
      $this->loadPageModel();   
      $this->Page->deleteBranch($this->params['url']['id']);
    }
  }
  
  function checkbox() {
    $this->loadPageModel(false);
    
    $field = $this->params['url']['field'];
    $this->Page->update(array($field => $this->data['value']), array('id' => $this->data['id']));
    
    $this->clearCache();
  }
  
  function restruct() {
    #$this->loadPageModel();

    $this->Page = new Model(array('table' => 'catalog'));

    $tree = $this->Page->find('threaded', array('fields' => array('id', 'parent_id')));
    
    function r(&$a, $l, $Page) {
      if (! empty($a))
      foreach($a as &$b) {
        if (! empty($b['children'])) {
          $r = r($b['children'], $l+1, $Page);
        } else
          $r = $l;
          
        $r++;
        
        #$b['lft'] = $l;
        #$b['rght'] = $r;
        
        array_unshift($b , $r);
        array_unshift($b , $l);
        
        $Page->save(array(
          'id'   => $b['id'],
          'lft'  => $l,
          'rght' => $r
        ));    
        
        $l += $r-$l;    
      }  
      
      return $l;
    }
    
    r($tree, 1, $this->Page);
  }
  
}

?>