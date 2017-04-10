<?php

class Models extends Controller {
  
  var $viewPath = 'tools/models';
  
  var $model  = array();
  
  var $fields = array();
  
  var $currentField = false;
  
  function beforeFilter() {    
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
    
    $this->loadModel('Group', array(
      'table' => '_sys_model_groups',
      'hasMany' => array(
        'Fields' => array(
          'table' => '_sys_model_fields',
          'key'   => 'group_id',
          'order' => 'pos'
        )
      )
    ));
    
    $this->loadModel('Field', array(
      'table' => '_sys_model_fields'
    ));
    
    $this->set('ROOT', $this->params['current']['url']);
  }
  
  function _preview() {
    $result = array();
    $models = $this->Model->find('all', array(
      'fields' => array('id', 'name'),
      'order'  => 'pos'
    ));
    
    foreach ($models as $model) {
      $result[] = array(
        'title' => $model['name'],
        'url'   => $this->params['url']['url'] . '/' . $model['id']
      );
    }
      
    return $result;
  }
  
  function index() {    
    if (! $this->dynamicRoutes($this->params['current']['url'], array(
      '_setup/*'              => '_setup',
      '/copy'                 => 'copy',
      '/paste'                => 'paste',
      '/fields/remove'        => 'fieldRemove',
      '/fields/order'         => 'fieldOrder',
      '/fields/add/:group_id' => 'fieldEdit',
      '/fields/:id/del'       => 'fieldDelete',
      '/fields/:id'           => 'fieldEdit',
      '/groups/order'         => 'groupOrder',
      '/groups/add/:model_id' => 'groupEdit',
      '/groups/:id/del'       => 'groupDelete',
      '/groups/:id'           => 'groupEdit',
      'order'    => 'modelOrder',
      'add'      => 'modelEdit',
      ':id/del'  => 'modelDelete',
      ':id'      => 'modelEdit'
    ))) {            
      
      $models = $this->Model->find('all', array('contain' => array(), 'order' => 'pos'));
      $this->set('models', $models);
      
      $this->params['jump'] = array('Добавить', 'add');
      $this->render('index');           
    }
  }
  
  function edit($model = '', $render = true, $return = true) {
    if (! $model)
      $model = 'Model';
      
    if (isset($this->params['url']['id']))
      $id = $this->params['url']['id']; 
      
    if (! empty($this->data)) {
      if ($id = $this->data['id'] = $this->{$model}->save($this->data)) {   
        if ($return) {
          if ($this->ajax) {
            $this->json($this->data);
          } else {
            $this->redirect('/' . str_replace('add', $id, $this->params['url']['url']));
          }
        }
      }
    }
    
    if ($id) {
      $this->data = $this->{$model}->find('first', array(
        'conditions' => array('id' => $id)
      ));
      
      if (empty($this->data))
        $this->renderError('404');
      
      $this->set('_data', $this->data);
    }
    
    if ($render)
      $this->render('edit');
  }
  
  function modelEdit() {
    if (! empty($this->data))
      $this->createTable();
    $this->edit();
  }
  
  function modelDelete() {
    $this->Model->del($this->params['url']['id']);     
  }
  
  function modelOrder() {   
    foreach ($this->data['pos'] as $pos => $id)
      $this->Model->save(array('id' => $id, 'pos' => $pos));
  }
  
  function groupEdit($render = true, $return = true) {
    $this->edit('Group', false, $return);
        
    $this->set('viewTemplates', array_diff(scandir($this->root . 'views' . DS . '_form' . DS . 'groups'), array('.', '..')), false);
    
    if ($render)
      $this->render('group-edit');
  }
  
  function groupOrder() {   
    foreach ($this->data['pos'] as $pos => $id)
      $this->Group->save(array('id' => $id, 'pos' => $pos));
  }
  
  function groupDelete() {
    $this->Group->del($this->params['url']['id']);
  }
  
  function getModelFields($modelId) {    
    $groups = $this->Group->find('list', array( 
      'conditions' => array('model_id' => $modelId),
      'fields' => array('id')
    ));
    
    $fields = $this->Field->find('all', array(
      'conditions' => array('group_id' => $groups),
      'fields'     => array('alias', 'params')
    ));
    
    $this->fields = $fields;
  }
  
  function createTable() {
    $dbo = new Dbo();
    $table = $this->data['table'];
    
    $dbo->query("
      CREATE TABLE IF NOT EXISTS `$table` (
        `id`  int(11) NOT NULL AUTO_INCREMENT,
        `pos` int(11) NOT NULL,
        PRIMARY KEY(`id`)    
      )");
  }
  
  function extractFields($fields) {
    
  }
  
  function bildTalbe($table) {            
		if (! empty($this->fields)) {	  
      foreach ($this->fields as $i => $field) {
        $params = $this->fields[$i]['params'] = json_decode($field['params'], true);
        if (isset($params['support_fields'])) {
          $supportFields = json_decode($params['support_fields'], true);
          foreach ($supportFields as $name => $f) {  
            $this->fields[] = array(
              'alias' => $name,
              'params' => array(
                'field_type'   => $f[0],
                'field_length' => $f[1]
              )
            );
          }
        }
      }
      
		  $first   = array(array('name' => 'id', 'type' => 'int', 'length' => 11), array('name' => 'pos', 'type' => 'int', 'length' => 11));
      $byTypes = array('keys' => $first);
      foreach ($this->fields as $field) {
        $params = $field['params'];
        if (isset($params['join_key'])) {
          $byTypes['keys'][] = array(
            'name'   => $params['join_key'],
            'type'   => 'int',
            'length' => 11
          );
        } 
        
        if (isset($params['field_type'])) {
          $byTypes[$params['field_type']][] = array(
            'name'   => $field['alias'],
            'type'   => $params['field_type'],
            'length' => $params['field_length']
          );
        }
      }
            
      $fields    = array();
      $typeOrder = array('keys', 'date', 'int', 'varchar', 'text', 'tinyint');
      
      foreach ($typeOrder as $type)
        if (isset($byTypes[$type])) {
          ksort($byTypes);
          foreach ($byTypes[$type] as $field)
            $fields[] = $field;
        }        
      
      $dbo = new Dbo();
      $schema = $dbo->schema($table);  
      
			foreach ($fields as $i => &$field) {      
        $query = 'ALTER TABLE `' . $table . '`';
        
        if (isset($schema[$field['name']])) {
          continue;
          //unset($schema[$field['name']]);
          //$query .= ' CHANGE ';
          //$query .= '`' . $field['name'] . '` `' . $field['name'] . '` ';
        } elseif (isset($schema[$this->currentField['oldAlias']]) && $field['name'] == $this->currentField['alias']) {
          $query .= ' CHANGE ';
          $query .= '`' . $this->currentField['oldAlias'] . '` `' . $field['name'] . '` ';
        } else {
          $field['id'] = $field['name'];
          $query .= ' ADD ';
          $query .= '`' . $field['name'] . '` ';
        }
        
        $query .=	$field['type'] . (! empty($field['length']) ? '(' .$field['length'] . ')' : '');
        $query .= $field['null'] ? ' NULL' : ' NOT NULL';
        $query .= ($i == 0 ?  ' FIRST ' : ' AFTER `' . $fields[$i-1]['name'] . '`');
        
        $dbo->query($query);
      }
      
      /*
      unset($schema[$this->currentField['oldAlias']]);
      unset($schema['primary']);
      if (! empty($schema))
  			foreach ($schema as $field)
  				$dbo->query('ALTER TABLE `'.$table.'` DROP `'.$field['name'].'`;');*/
		}
	}
  
  function assocc($modelId) {    
    $assocc = array();
    foreach($this->fields as $field) {
      $params  = $field['params'];
      $fields  = '';
      if (! empty($params['fields']))
        $fields = explode(',', str_replace(' ', '', $params['fields']));
      if (isset($params['join'])) {
        if (isset($params['model']))
          $params['table'] = $this->Model->field('table', array('id' => $params['model']));
        
        $assocc[$params['join']][$field['alias']] = array(
          'table'      => $params['table'],
          'key'        => $params['join_key'],
          'fields'     => $fields
        );
        
        if (isset($params['join_order']))
          $assocc[$params['join']][$field['alias']]['order'] = $params['join_order'];
      }
    }
    
    $this->Model->save(array('id' => $modelId, 'assocc' => json_encode($assocc)));
  }
  
  function searchCondition($modelId) {  
    $conditionFields = $this->Field->find('list', array(
      'conditions' => array(
        'model_id'            => $modelId,
        'is_search_condition' => 1
      ),
      'fields'     => array('alias')
    ));
    
    $conditions = array();
    if (! empty($conditionFields))
      foreach ($conditionFields as $field) {
        $conditions[$field] = 1;
      }
      
    return $conditions;
  }
  
  function searchLoadModel($modelId) {
    $model = $this->Model->find('first', array(
      'conditions' => array('id' => $modelId),
      'fields'     => array('id', 'table', 'alias'),
      'contain'    => array()
    ));   
    return $model;
  }
  
  function search() {
    $current = $this->currentField;
      
    if ($current['is_site_search']) {      
      if ($current['is_past_data_site']) {
        extract($this->searchLoadModel($current['model_id']));
        
        $this->loadModel($alias, array('table' => $table)); 
        
        $results = $this->{$alias}->find('all', array(
          'conditions' => $this->searchCondition($current['model_id']),
          'fields'     => array('id', $current['alias'], 'url')
        ));
        
        $this->SiteSearch->delete(array('object_model' => $alias));
        if (! empty($results))      
          foreach ($results as $result) {
            $data = array(
              'object_model' => $alias,
              'object_id'    => $result['id'],
              'key_words'    => $result[$current['alias']],
              'url'          => $result['url']
            );
            
            $this->SiteSearch->save($data);
          }      
      }
    } else {
      extract($this->searchLoadModel($current['model_id']));
      $this->SiteSearch->delete(array('object_model' => $alias));
    }
    
    if ($current['is_atlant_search']) {    
      if ($current['is_past_data_atlant']) {
        extract($this->searchLoadModel($current['model_id']));
        
        $this->loadModel($alias, array('table' => $table)); 
        
        $results = $this->{$alias}->find('all', array(
          'fields' => array('id', $current['alias'])
        ));
        
        $this->AtlantSearch->delete(array('object_model' => $alias));
        if (! empty($results))
          foreach ($results as $result) {
            $data = array(
              'object_model' => $alias,
              'object_id'    => $result['id'],
              'key_words'    => $result[$current['alias']]
            );
            
            $this->AtlantSearch->save($data);
          }   
      }
    } else {
      extract($this->searchLoadModel($current['model_id']));
      $this->AtlantSearch->delete(array('object_model' => $alias));
    }
  }
  
  function fieldEdit($render = true, $return = true) {    
    $modelId = false;
    if (! empty($this->data)) {
      $this->data['params'] = json_encode($this->data['Params']);
      $this->currentField   = $this->data;
      
      if (! isset($this->data['id']) || ! $this->data['id']) {
        if ($id = $this->Field->field('id', array('alias' => $this->data['alias'], 'model_id' => $this->data['model_id']))) {
          trigger_error('Dublicate field alias');
          return false;
        }
      }
    }
    
    $this->edit('Field', false, false);
        
    $this->set('viewTemplates', array_diff(scandir($this->root . 'views' . DS . '_form' . DS . 'fields'), array('.', '..')), false);
    
    if (! empty($this->currentField)) {
      $modelId = $this->data['model_id'];
      $table   = $this->Model->field('table', array('id' => $modelId));
      $this->getModelFields($modelId);
      $this->bildTalbe($table, $modelId);
      $this->assocc($modelId);
      $this->search();
    }
      
    if (! empty($_POST) && $return) {  
      if ($this->ajax) {
        $this->json($this->data);
      }
    }
    
    if (isset($this->params['url']['group_id'])) {
      $modelId = $this->Group->field('model_id', array('id' => $this->params['url']['group_id']));
      $this->set('modelId', $modelId);
    }
    
    if ($render)
      $this->render('field-edit');
  }
  
  function fieldOrder() {
    foreach ($this->data['pos'] as $pos => $id)
      $this->Field->save(array('id' => $id, 'pos' => $pos));
  }
  
  function fieldRemove() {
    if (isset($this->data['id']) && $this->data['id'] > 0)
    $this->Field->save(array('id' => $this->data['id'], 'group_id' => $this->data['parent_id']));
  }
  
  function fieldDelete() {
    $dbo   = new Dbo();
    $id    = $this->params['url']['id'];
    $field = $this->Field->find('first', array(
      'conditions' => array('id' => $id),
      'fields'     => array('id', 'alias', 'model_id')
    ));
    if ($field) {
      $table = $this->Model->field('table', array('id' => $field['model_id']));
      if ($table)
        $dbo->query("ALTER TABLE `$table` DROP `".$field['alias']."`");
    }
    $this->Field->del($id);
  }
  
  function copy() {
    $_SESSION['models']['clipboard'] = $_POST;
  }
  
  function paste() {
    if (isset($_SESSION['models']['clipboard']) && ! empty($_SESSION['models']['clipboard']) && isset($this->params['url']['model_id'])) {
      $modelId   = $this->params['url']['model_id'];
      $clipboard = $_SESSION['models']['clipboard'];
      
      $copiedGroup = array();
      if (! empty($clipboard['groups']))
        foreach ($clipboard['groups'] as $groupId) {
          $group = $this->Group->find('first', array(
            'conditions' => array('id' => $groupId), 
            'contain' => array()
          ));
          if (! empty($group) && $group['model_id'] != $modelId) {
            $group['Params']   = json_decode($group['params'], true);
            $group['model_id'] = $modelId;
            unset($group['id']);
            $this->data = $group;
            $this->groupEdit(false, false);
            $copiedGroup[$groupId] = $this->data['id'];
          }
        }
      
      $bufferGroup = false;  
      if (! empty($clipboard['fields']))
        foreach ($clipboard['fields'] as $fieldId) {
          $field = $this->Field->find('first', array(
            'conditions' => array('id' => $fieldId), 
            'contain' => array()
          ));
        
          if (! empty($field) && $field['model_id'] != $modelId) {
            if (isset($copiedGroup[$field['group_id']]))
              $field['group_id'] = $copiedGroup[$field['group_id']];
            elseif ($bufferGroup)
              $field['group_id'] = $bufferGroup;
            elseif ($bufferGroup = $this->Group->field('id', array('model_id' => $modelId, 'name' => '_temp'))) {
              $field['group_id'] = $bufferGroup;
            } else {
              $field['group_id'] = $bufferGroup = $this->Group->save(array(
                'model_id' => $modelId,
                'name'     => '_temp'
              ));
            }
              
            $field['Params']   = json_decode($field['params'], true);  
            $field['model_id'] = $modelId;
            unset($field['id']);
            $this->data = $field;
            $this->fieldEdit(false, false);
          }
        }
    }
  }
  
  function _setup($act = '', $file = '') {
    $this->edit('Field', false);    
    if (! empty($this->data)) {
      $this->data['Params'] = json_decode($this->data['params'], true);
    }
    
    //debug(json_decode(stripslashes(mysqli_real_escape_string($this->Model->dbo->db, json_encode(array('dsf' => 'выаыав'))))));            
    
    $file = str_replace('.ctp', '', $file);
    $this->set('_setup', true, false);
    $this->set('Model', $this->Model);
    $this->render($act . DS . $file, '', '_form');
  }
}

?>