<?php

class Profiles extends Controller {
  
  var $viewPath = 'tools/profiles';
  
  function beforeFilter() {
    $this->loadModel('Profile', array(
      'table' => '_sys_profiles'
    ));
  }
  
  function _preview() {
    $result = array();
    $profiles = $this->Profile->find('all', array(
      'fields' => array('id', 'name')
    ));
    
    foreach ($profiles as $profile) {
      $result[] = array(
        'title' => $profile['name'],
        'url'   => $this->params['url']['url'] . '/' . $profile['id']
      );
    }
      
    return $result;
  }
  
  function index() {  
    $actions = array_merge(array(
      'dels'   => 'dels',
    ), $this->actionSet['edit']);
    
    if (! $this->dynamicRoutes($this->params['current']['url'], $actions)) {
      $items = $this->Profile->find('all');
      $this->set('items', $items);
      $this->params['jump'] = array('Добавить', 'add');
      $this->render('index');
    }
  }
  
  function edit() {          
    if (isset($this->params['url']['id']))
      $id = $this->params['url']['id']; 
      
    if (! empty($this->data)) {
      if (! empty($this->data['password'])) 
        $this->data['password'] = securityHash($this->data['password']);      
      
      if ($id = $this->data['id'] = $this->Profile->save($this->data)) {   
        $this->redirect('/' . str_replace('add', $id, $this->params['url']['url']));
      }
    }
    
    if ($id) {
      $this->data = $this->Profile->find('first', array(
        'conditions' => array('id' => $id)
      ));
      
      if (empty($this->data))
        $this->renderError('404');
      
      if (! empty($this->data))
        unset($this->data['password']);
      
      $this->set('_data', $this->data);
    }
    
    $this->render('edit');
  }
  
  function del($id = null, $clearCache = true) {
    if (! $id && isset($this->params['url']['id']))
      $id = $this->params['url']['id'];
      
      
    if ($id) {
      $this->Profile->del($id);
    }
  }
  
  function dels() {
    if (isset($this->data['list']) && ! empty($this->data['list'])) {
      if (is_string($this->data['list']))
        $this->data['list'] = explode(';', $this->data['list']);
        
      foreach ($this->data['list'] as $id) {
        $this->del($id, false);
      }
    }
  }
  
}

?>