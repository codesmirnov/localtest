<?php

class Settings extends Controller {
  
  var $viewPath = 'settings';
  
  function beforeFilter() {
    $this->loadModel('Setting', array(
      'table' => '_sys_settings'
    ));
  }
  
  function index() {
    if (! empty($this->params['pass']))
      $this->renderError('404');
      
    if (! empty($this->data)) {
      $data = array();
      $this->Setting->delete(array('id >=' => '0'));
      foreach ($this->data as $field => $value) {
        $data = array(
          'field' => $field,
          'value' => $value
        );
        
        if ($id = $this->Setting->field('id', array('field' => $field)))
          $data['id'] = $id;
          
        $this->Setting->save($data);
        $this->clearCache();
      }    
    }
    
    $this->data = $this->Setting->find('index', array('fields' => array('field', 'value')));
    $this->render('index');
  }
  
}

?>