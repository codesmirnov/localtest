<?php

class Map extends Controller {
  
  var $viewPath = 'map';
  
  var $layout   = 'default';
  
  function beforeFilter() {  
    $this->loadModel('Map', array(
      'table' => '_sys_map'
    ));
  }
  
  function beforeRender() {
  } 

  function index() {  
    
    if (! $this->dynamicRoutes($this->params['current']['url'], array(
      '_setup/*' => 'setup',
      'add'      => 'edit',
      'edit'     => 'edit',
      'order'    => 'order',
      'remove'   => 'remove',
      ':id/del'  => 'del',
      ':id'      => 'edit'
    ))) {    
      $map = $this->Map->find('threaded', array('order' => 'pos'));
      $this->set('map', $map);
      $this->render('index');
      
    }
  }
  
  function edit() {    
    if (isset($this->params['url']['id']) && is_numeric($this->params['url']['id']))
      $id = $this->params['url']['id'];
      
    if (! empty($this->data)) {
      $this->data['params'] = json_encode($this->data['Params']);
      if ($id = $this->data['id'] = $this->Map->save($this->data)) {      
        $this->Map->saveURL($this->data);
        if ($this->ajax) {
          $this->json($this->data);
        }
      }
    }
    
    if ($id) {
      $this->data = $this->Map->find('first', array(
        'conditions' => array('id' => $id)
      ));
      
      $this->set('_data', $this->data);
    }
    
    $this->set('controllers', array_diff(scandir($this->root . DS . 'controllers'), array('.', '..')), false);
    
    $this->render('edit');
  }
  
  function del() { 
    if (isset($this->params['url']['id'])) {
      $this->Map->deleteBranch($this->params['url']['id']);
    }
  }
  
  function remove() {
    extract($this->data);
    
    if (isset($id) && isset($parentId)) {
      $this->Map->setParent($id, $parentId);
    }    
  }
  
  function order() {     
    if (isset($this->data['pos']) && ! empty($this->data['pos'])) {
      foreach ($this->data['pos'] as $pos => $id)
        $this->Map->save(array('id' => $id, 'pos' => $pos));
    }
  }
  
  function setup($file) {
    if (isset($this->params['url']['id']) && is_numeric($this->params['url']['id'])) {
      $this->data = $this->Map->find('first', array(
        'conditions' => array('id' => $this->params['url']['id'])
      ));
      if (! empty($this->data['params']))
        $this->data['Params'] = json_decode($this->data['params'], true);
    }
      
    $this->loadController($file, array(
      'action' => '_setup'
    ));
  }
}

?>