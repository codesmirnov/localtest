<?php

class Admin extends Controller {
  
  var $layout     = 'default';
  
  var $access     = array();
  
  var $logProfile = array();
  
  var $actionSet = array(
    'edit' => array(
      'order'   => 'order',
      'add'     => 'edit',
      ':id/del' => 'del', 
      ':id'     => 'edit'
    ),
    'tree' => array(
      'remove'  => 'remove',
      'order'   => 'order',
      'add'     => 'add',
      ':id/del' => 'del', 
      ':id'     => 'edit'
    )
  );
  
  function beforeFilter() {   
    Session::start();
    
    if (Session::check('logProfile')) {
      $this->logProfile = Session::read('logProfile');
      $this->set('logProfile', $this->logProfile);
    } elseif ($this->action != 'login')
      $this->renderError('404');
    
    $this->sessionParams();
    
    $this->loadModel('Profile', array(
      'table' => '_sys_profiles'
    ));
    
    if ($this->logProfile) {
      $profile       = $this->Profile->find('first', array('conditions' => array('id' => $this->logProfile['id'])));
      $this->access  = explode(';', $profile['access']);
      
      
      $this->loadModel('Map',   array(
        'table' => '_sys_map'
      ));
      
      $this->loadModel('Model', array(
        'table' => '_sys_models',
        'hasMany' => array(
          'Groups' => array(
            'table' => '_sys_model_groups',
            'key'   => 'model_id',
            'order' => 'pos',
            'hasMany' => array(
              'Fields' => array(
                'table' => '_sys_model_fields',
                'key'   => 'group_id',
                'order' => 'pos'
              )
            )
          )
        )
      ));
      
      $this->loadModel('SiteSearch', array(
        'table' => '_sys_search_client'
      )); 
      
      $this->loadModel('AtlantSearch', array(
        'table' => '_sys_search_atlant'
      ));
      
      
      $sections = $this->Map->find('threaded', array(
        'conditions' => array('id' => $this->access),
        'fields' => array('id', 'parent_id', 'title', 'alias', 'url', 'unchecked')
      ));    
      $this->set('sections', $sections);
    } 
  }
  
  function beforeRender() {    
  }
  
  function sessionParams() {    
    $hash = md5($this->params['url']['url'] . $this->ajax);
    
    foreach ($this->params['url'] as $param => $value)
      if (! in_array($param, array('url', 'debug'))) {
        $_SESSION['url'][$hash][$param] = $value;
      }
      
    if (isset($_SESSION['url'][$hash]))
      foreach ($_SESSION['url'][$hash] as $param => $value)
        if (! isset($this->params['url'][$param]))
          $this->params['url'][$param] = $value;
  }
  
  function dispatch() {    
    $this->parseUrl($this->Map, array('fields' => array('controller', 'params')));    
    $this->params['admin']['url'] = $this->params['crumbs'][0]['url'];
      
    $this->unchecked();
    
    if (! in_array($this->params['current']['id'], $this->access))
      $this->renderError('404', 'clear');
    
    if ($this->params['current']['controller'] == 'admin.php') {
      $this->index();
    } else
      $this->loadController($this->params['current']['controller'], array('action' => 'index', 'params' => $this->params));
  } 
  
  function index() {
    if (! empty($this->params['pass']))
      $this->renderError('404');    
    
    $this->render('index');
  }
  
  function login() {    
    if (! empty($this->data)) {
      $password = securityHash($this->data['password'], null, true);
      $profile  = $this->Profile->find('first', array('conditions' => array('login' => $this->data['login'])));
      if (! empty($password) && $profile['password'] == $password) {
        unset($profile['password']);
        $profile['_suggest'] = true;
        Session::write('logProfile', $profile);
        $this->redirect('/admin');
      } else {
        $this->set('error', 'Пара логин-пароль не найдена');
      }
    } 
    
    $this->params['current']['title'] = 'Авторизация';
    $this->render('login');
  }
  
  function logout() {
    Session::del('logProfile');
    $this->redirect('/');
  }
  
  function search($model, $id) {
    $modelId  = $this->Model->field('id', array('alias' => $model));
    $sections = $this->Map->find('all', array(
      'fields'     => array('id', 'url', 'params'),
      'conditions' => array('controller' => array('objects.php', 'sitemap.php'))
    ));
    
    if (! empty($sections) && $modelId && $id) {
      foreach ($sections as $section) {
        $params = json_decode($section['params'], true);
        if ((! isset($params['method']) || $params['method'] == 'object') && $params['model'] == $modelId) {
          $this->redirect($section['url'] . '/' . $id);
        }
      }
    }
  }
  
  function unchecked() {
    $models   = $this->Model->Groups->Fields->find('list', array('fields' => array('DISTINCT model_id'), 'conditions' => array('alias' => 'is_checked')));
    if (empty($models))
      return false;
    $aliases  = $this->Model->find('list', array('fields' => array('id', 'alias', 'table'), 'conditions' => array('id' => $models)));
    $sections = $this->Map->find('list', array('fields' => array('id', 'params'), 'conditions' => array('controller' => 'objects.php')));
    foreach ($sections as $section) {
      $params = json_decode($section['params'],true);
      if ($params['method'] != 'index' && in_array($params['model'], $models)) {
        $model = new Model($aliases[$params['model']]);
        $params['conditions'] = (! empty($params['conditions']) ? $params['conditions'] . ' and ' : '') . 'is_checked=0';
        $count = $model->find('count', array('conditions' => $params['conditions']));
        $this->Map->save(array('id' => $section['id'], 'unchecked' => $count));
      }
    }
  }
  
}

?>