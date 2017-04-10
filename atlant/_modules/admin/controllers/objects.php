<?php

class Objects extends Controller {
  
  var $viewPath = 'objects';
  
  var $search = array();
  
  function beforeFilter() {  
    //$this->loadController('_images.php');
  }
  
  function beforeRender() {
    //$this->loadController('_images.php');
  } 
  
  function _setup() {    
    if (! $this->ajax) {
      if (! empty($this->data)) {
        $this->params['current']['params'] = $params = json_encode($this->data['Params']);
        $this->Map->save(array('id' => $this->params['current']['id'], 'params' => $params));
      }
      
      $this->data['Params'] = json_decode($this->params['current']['params'], true);
    }
    
    $models = $this->Model->find('index', array('fields' => array('id', 'name'), 'order' => 'pos'));
    $this->set('models', $models);    
    
    $this->render('_setup');
  }
  
  function index() {  
    $actions = array_merge(array(
      '_setup' => '_setup',
      'order'  => 'order',
      'show'   => 'show',
      'hide'   => 'hide',
      'dels'   => 'dels',
      'fields' => 'fields',
    ), $this->actionSet['edit']);
    
    if (! $this->dynamicRoutes($this->params['current']['url'], $actions)) {
      
      $current = &$this->params['current'];
      $current['Params'] = json_decode($current['params'], true);
      
      if ($current['Params']['method'] != 'index') {
        return $this->dispatchMethod($current['Params']['method']);
      }
      
      $objects = $this->Map->find('all', array(
        'conditions' => array(
          'id'        => $this->access,
          'parent_id' => $this->params['current']['id']
        ),
        'order' => 'pos'
      ));
      
      if (! empty($objects)) {
        foreach ($objects as &$section) {
          $section['Params'] = json_decode($section['params'], true);
          if (! empty($section['Params']['model'])) {
            if ($section['Model'] = $this->Model->find('first', array(  
              'conditions' => array('id' => $section['Params']['model']),
              'contain'    => array()
            ))) {
              extract($section['Model']);
              $this->loadModel($alias, array(
                'table' => $table
              ));           
              $section['Items'] = $this->optimize($this->{$alias}, array(
                'order' => $section['Params']['order'],
                'limit' => 10
              ));
              $section['count'] = $this->{$alias}->find('count');
            }
          }
        }
      }
      
      $this->set('objects', $objects);
      
      $this->render('index');
    }
  }
  
  function setModel($options) {
    extract($options);
    $assocc = json_decode($assocc, true);          
    if (! $assocc) $assocc = array();   
    if (! empty($assocc))
      foreach ($assocc as $type => $joins)
        foreach ($joins as $alias => $join) {
          $assocc[$type][$alias]['alias'] = ucfirst($alias);
        }
        
    $this->loadModel('Object', array_merge($assocc, array('table' => $table)));
  }
  
  function templates(&$params) {
    $templates = $this->params['current']['Params']['templates'];
    $this->set('templates', $templates);
    
    $template = $templates[0];
    if (isset($this->params['url']['template'])) {
      $template = $templates[$this->params['url']['template']];

      $params['order'] = $template['sort'];
      $params['limit'] = $template['limit'];

    }
    
    return $template; 
  }
  
  function categories(&$params) {
    $category = $this->params['current']['Params']['category'];
    if ($category['show'] && isset($this->params['url'][$category['key']])) {
      $model    = new Model(array('table' => $category['table']));
      $id       = $this->params['url'][$category['key']];
      $children = $model->children($id, array('method' => 'list', 'fields' => array('id')));
      $params['conditions'][$category['key']] = array_merge(array($id), $children);
    }
  }
  
  function filters(&$params) {
    $filters = &$this->params['current']['Params']['filters'];
    if (! empty($filters)) {
      $currentFilter = array();
      if (isset($this->params['url']['filters']))
        $currentFilter = explode(',', $this->params['url']['filters']);
      
      $notIn = array();
      foreach ($filters as $i => &$filter) {
        $checked = false;
        $field   = '';
        
        if (in_array($filter['field'], $currentFilter)) {
          $checked = $filter['checked'] = true;
          $field   = preg_replace('~^_~Usi', '', $filter['field']);          
        }
        elseif (in_array('$' . $filter['field'], $currentFilter)) {
          $checked = $filter['unchecked'] = true;
          $field   = preg_replace('~^$~Usi', '', $filter['field']); 
          $filter['value'] = $filter['value2'];        
        }
        
        if ($checked) {

          if ($filter['value'] == 'NOT EXISTS') {
            $field = $this->Model->Groups->Fields->find('first', array(
              'conditions' => array(
                'model_id' => $this->params['current']['Params']['model']['id'],
                'alias'    => $field
              )));
              
            $join   = json_decode($field['params'], true);
            $model  = new Model(array('table' => $join['table']));
            $result = $model->find('list', array(
              'fields' => array('DISTINCT ' . $join['join_key'])
            ));
            $notIn = array_merge($notIn, $result);
          } else
            $params['conditions'][$field] = $filter['value'];
        } else
          $filter['checked'] = false;
      }
      
      if (! empty($notIn))
        $params['conditions']['id NOT IN'] = $notIn;
    }
  }
  
  function object() {
    $current = &$this->params['current'];
    $model   = $current['Params']['model'];
    if (! empty($model)) {
      if ($current['Model'] = $this->Model->find('first', array(
        'conditions' => array('id' => $model),
        'contain'    => array()
      ))) {
        
        $this->setModel($current['Model']);
        
        $params = $current['Params'];
        extract($params);
        $params = compact('find_method', 'order', 'conditions');    
        if (isset($params['find_method']))
          $params['method'] = $params['find_method'];
        
        if (isset($this->params['url']['order']))
          $params['order'] = $this->params['url']['order'];   
            
        $this->filters($params);
        $this->categories($params);
        
        $template = $this->templates($params);
        extract($template);
        
        if ($order)
          $params['order'] = $order;
        
        $this->set('templateFile', $file);
        
        if (! $paginate) {
          $items = $this->optimize($this->Object, $params);
        } else {
          $items = $this->paginate($this->Object, array_merge($params, array('optimize' => true)));
        }
        
        $this->set('items', $items, false);
      }
    }
    
    $this->params['jump'] = array('Добавить', 'add');
    
    $this->set('setupLink', true);
    $this->render('object');
  }
  
  function beforeSave() {
    $data = &$this->data;
    
    $includes = array();
    $fields   = array();
    $params   = array();
    foreach ($this->params['current']['Model']['Groups'] as $group) 
      foreach ($group['Fields'] as $field) {
        $fields[$field['alias']]  = str_replace('.ctp', '', $field['file']);
        $params[$field['alias']]  = json_decode($field['params']);
        
        if ($field['file'] == 'custome.ctp') {
          $file = $params[$field['alias']]->template;
          ob_start();
          include $this->root . 'views' . DS . '_form' . DS . 'customes' . DS . $file;
          ob_get_clean(); 
        }                    
        else if (! isset($includes[$field['file']])) { 
          ob_start();
          include $this->root . 'views' . DS . '_form' . DS . 'fields' . DS . $field['file'];
          ob_get_clean();  
          
          $includes[$field['file']] = true;
        }
      }
      
    if (! empty($_FILES))
      $data = array_merge($data, $_FILES);
      
    foreach ($data as $field => &$value) {
      $fieldType = $fields[$field];
      if ($fieldType == 'custome') {
        $fieldType = str_replace('.ctp', '', $params[$field]->template);
      }            
      
      $function = '__'.$fieldType.'BeforeSave';
    
      if (function_exists($function)) {
        $value = $function($value, $params[$field], $data);
        if (! $value)
          unset($data[$field]);
      }
    }     
  }
  
  function beforeToSearch() {
    $this->search['model_id'] = $this->params['current']['Params']['model'];
    $this->search['fields']   = $this->Model->Groups->Fields->find('list', array(
      'fields'     => array('alias', 'id', 'is_site_search', 'is_atlant_search'),
      'conditions' => array(
        'model_id' => $this->search['model_id'],
        'OR' => array(
          'is_site_search'      => 1,
          'is_atlant_search'    => 1
        )
      )
    ));
    
    $this->search['conditions'] = $this->Model->Groups->Fields->find('list', array(
      'fields'     => array('alias'),
      'conditions' => array(
        'model_id'            => $this->search['model_id'],
        'is_search_condition' => 1
      )
    ));
  }
  
  function toSearch() {    
    if (! empty($this->search['fields'])) {
      foreach ($this->search['fields'] as $alias => &$field) {
        $combineKey = array(            
          'object_model' => $this->params['current']['Model']['alias'],
          'object_id'    => $this->data['id']
        );
        if ($field['is_site_search'] && isset($this->data[$field['alias']]) && ! empty($this->data[$field['alias']]) && isset($this->data['url']) && ! empty($this->data['url'])) {
                   
          $add = true;
          if (! empty($this->search['conditions']))
            foreach ($this->search['conditions'] as $alias)
              if (! isset($this->data[$alias]) || ! $this->data[$alias]) {
                $add = false;  
              }
            
          if ($add) {
            $data = array(
              'key_words' => $this->data[$field['alias']],
              'url'       => $this->data['url']
            );
            
            if ($id = $this->SiteSearch->field('id', $combineKey)) {
              $data['id'] = $id;
            }
            
            $this->SiteSearch->save(array_merge($data, $combineKey));
          } else
            $this->SiteSearch->delete($combineKey);
        }
        
        if ($field['is_atlant_search']) {         
          $data = array('key_words' => $this->data[$field['alias']]);
                    
          if ($id = $this->AtlantSearch->field('id', $combineKey)) {
            $data['id'] = $id;
          }
          
          $this->AtlantSearch->save(array_merge($data, $combineKey));
        }
      }
    }
  }
  
  function afterSave($id) {
    $data = &$this->data;
      
    if (isset($data['parent_id'])) {
      $this->Object->saveUrl($data, array('is_folder' => '0'));
    }   
      
    $includes = array();
    $fields   = array();
    $params   = array();
    foreach ($this->params['current']['Model']['Groups'] as $group) 
      foreach ($group['Fields'] as $field) {
        $fields[$field['alias']]  = str_replace('.ctp', '', $field['file']);
        $params[$field['alias']]  = json_decode($field['params']);
        
        if ($field['file'] == 'custome.ctp') {
          $file = $params[$field['alias']]->template;
          ob_start();
          include $this->root . 'views' . DS . '_form' . DS . 'customes' . DS . $file;
          ob_get_clean(); 
        }                    
        else if (! isset($includes[$field['file']])) { 
          ob_start();
          include $this->root . 'views' . DS . '_form' . DS . 'fields' . DS . $field['file'];
          ob_get_clean();  
          
          $includes[$field['file']] = true;
        }
      }
      
    if (! empty($_FILES))
      $data = array_merge($data, $_FILES);
      
    foreach ($data as $field => &$value) {
      $fieldType = $fields[$field];
      if ($fieldType == 'custome') {
        $fieldType = str_replace('.ctp', '', $params[$field]->template);
      }            
      
      $function = '__'.$fieldType.'AfterSave';
    
      if (function_exists($function)) {
        $function($value, $params[$field], $id);
      }
    } 
    
    $this->beforeToSearch();
    $this->toSearch();
    
    $this->clearCache();
      
    if ($this->ajax) {
      $this->json($data);
    } else {
      $this->redirect('/' . str_replace('add', $id, $this->params['url']['url']));
    }  
  }
  
  function save() {
    
    $this->beforeSave();

    $data = &$this->data;
      
    if ($id = $data['id'] = $this->params['url']['id'] = $this->Object->save($data)) {  
      $this->afterSave($id);      
    }
  }
  
  function loadObject() {
    $current           = &$this->params['current'];
    $current['Params'] = json_decode($current['params'], true);
    
    if (! empty($current['Params']['model'])) {
      if ($current['Model'] = $this->Model->find('first', array(
        'conditions' => array('id' => $current['Params']['model'])
      ))) {         
        $this->setModel($current['Model']);
        return true;
      }
    } else
      return false;
  }
  
  function jump() {
    $urlField = $this->params['current']['Params']['url_field'];
    if (! $urlField)
      $urlField = 'url';
        
    if (isset($this->data[$urlField]) && ! empty($this->data[$urlField]))
      $this->params['jump'] = array('Перейти', $this->data[$urlField]);
  }
  
  function edit() {    
    if ($this->loadObject()) {
      $id = &$this->params['url']['id'];
      if (! empty($this->data)) {                            
        $this->save($model);
      }
    
      if ($id) {
        $this->data = $this->Object->find('first', array(
          'conditions' => array('id' => $id)
        ));
        
        $this->jump();
        
        if (empty($this->data))
          $this->renderError('404');
          
        $this->Object->save(array('is_checked' => 1, 'id' => $id));
          
        $titleField = $this->params['current']['Params']['title_field'];
        if (! $titleField)
          $titleField = 'title';
          
        $this->params['current']['title'] = $this->data[$titleField];
        $this->set('_data', $this->data);
      }
      
      $this->set('_model', $this->params['current']['Model']); 

      $this->render('form', '', '_form');
    }
  } 
  
  function show() {
    if ($this->loadObject()) {
      $fields = array();
      foreach ($this->params['current']['Model']['Groups'] as $groups)
        foreach ($groups['Fields'] as $field)
          $fields[$field['alias']] = $field;
      if (isset($this->data['list']) && ! empty($this->data['list'])) {
        $data = array('is_public' => 1);
        if (isset($fields['is_checked']))
          $data['is_checked'] = 1;
        $this->Object->update($data, array('id' => $this->data['list']));          
    
        $this->beforeToSearch();
        
        $fields = (array_merge($this->search['conditions'], array_keys($this->search['fields'])));
        $items = $this->Object->find('all', array('conditions' => array('id' => $this->data['list']), 'fields' => array_merge(array('id',' url'), $fields), 'contain' => array()));
        
        foreach ($items as $item) {
          $this->data = $item;
          $this->toSearch();
        }
      }
    }    
    
    $this->clearCache();
  }
  
  function hide() {
    if ($this->loadObject()) {    
      if (isset($this->data['list']) && ! empty($this->data['list'])) {
        $this->Object->update(array('is_public' => '0'), array('id' => $this->data['list']));       
    
        $this->beforeToSearch();
        
        $fields = (array_merge($this->search['conditions'], array_keys($this->search['fields'])));
        $items = $this->Object->find('all', array('conditions' => array('id' => $this->data['list']), 'fields' => array_merge(array('id',' url'), $fields), 'contain' => array()));
        foreach ($items as $item) {
          $this->data = $item;
          $this->toSearch();
        }
      }
    }
    
    $this->clearCache();
  }
  
  function dels() {
    if (isset($this->data['list']) && ! empty($this->data['list'])) {
      if (is_string($this->data['list']))
        $this->data['list'] = explode(';', $this->data['list']);
        
      foreach ($this->data['list'] as $id) {
        $this->del($id, false);
      }
    }
    
    $this->clearCache();
  }
  
  function deleteImages($id) {
    $model  = $this->params['current']['Params']['model'];
    $fields = $this->Model->Groups->Fields->find('list', array('fields' => array('id'), 'conditions' => array('model_id' => $model, 'file' => 'images.ctp'))); 
    if (! empty($fields)) {
      $imageController = $this->loadController('_images.php');
      
      foreach ($fields as $field) {
        $imageController->init($field);
        $imageController->deleteAll($id);
      }
    }
  }
  
  function del($id = null, $clearCache = true) {
    if (! $id && isset($this->params['url']['id']))
      $id = $this->params['url']['id'];
      
      
    if ($id && $this->loadObject()) {
      $this->deleteImages($id);
      $this->Object->del($id);
    }
    
    if ($clearCache)
      $this->clearCache();  
  }
  
  function fields() {    
    extract($this->data);
    
    if (isset($items) && !empty($items)) {
      if ($this->loadObject()) {   
        foreach ($items as $item) {
          extract($item);
          if ($id > 0) {
            $this->Object->save(array(
              'id' => $id,
              $field => $value
            ));
          }
        }
      }
    }
  }
  
  function order() {     
    if (isset($this->data['pos']) && ! empty($this->data['pos'])) {
      if ($this->loadObject()) { 
        foreach ($this->data['pos'] as $pos => $id)
          if (is_numeric($pos) && is_numeric($id) && $id > 0) {
            $this->Object->update("pos = $pos", "id = $id");
          }
      }
    }
  }
  
}

?>