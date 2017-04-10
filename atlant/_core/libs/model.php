<?php

class Model {
  
  var $dbo = false;
  
  var $alias = '';
  
  var $table = '';
  
  var $schema = array();
  
  var $relations = array('belongsTo', 'hasOne', 'hasMany');
  
  var $data = array();
  
  var $uses = array();
  
  var $contain = false;

  var $models = array();
  
  function __construct($options = array()) {
    $this->dbo = new Dbo();
    
    $this->alias = isset($options['alias']) ? $options['alias'] : $options['table'];
    $this->table = $options['table'];
    
    foreach ($this->relations as $relation)
      if (isset($options[$relation]) && ! empty($options[$relation])) {
        $this->{$relation} = $options[$relation];
        foreach ($this->{$relation} as $alias => $join) 
          $this->uses[$alias] = $join;
      }
      
    if (! empty($this->uses))
      foreach ($this->uses as $alias => $join) {
        $this->{$alias} = new Model($join);
      }

    $this->models = Configure::read('models');
  }
  
  function hasRelations($options) {   
    foreach ($this->relations as $relation)
      if (isset($options[$relation]) && ! empty($options[$relation])) 
        return true;    
  }
  
  function join($join, $mode) { 
    $join['mode'] = $mode;
    if ($this->hasRelations($join)) {
      if (isset($join['fields']))
        $join['_fields'] = $join['fields'];
        
      #$join['fields'] = array('id');
      $join['_after'] = true;    
    }
    
    return $join;
  }  
  
  function assoc($options, &$results) {    
    $afterJoin = array();
    if (isset($options['joins']) && ! empty($options['joins']))
    foreach ($options['joins'] as $alias => $join)
      if (isset($join['_after']) && $join['_after'])  
        $afterJoin[$alias] = $join;
    
    $sets = array();  
    foreach ($results as $i => $result) {
      if (! empty($afterJoin)) {
        foreach ($afterJoin as $alias => $join) 
          if (isset($result[$alias]) && isset($result[$alias]['id'])) {
            $sets[$alias]['join']                       = $join;
            $sets[$alias]['join']['key']                = 'id';
            $sets[$alias]['ids'][$result[$alias]['id']] = $i;
          }      
      }
      
      if (! empty($this->hasMany)) {
        foreach ($this->hasMany as $alias => $join) {
          if ($this->inContain($alias, $options['contain'], $join)) {
            if (isset($join['fields']))
              $join['fields'][] = $join['key'];            
            
            if (isset($join['limit'])) {
              if (! isset($join['conditions']))
                $join['conditions'] = array();
              $join['alias']                    = $alias;
              $join['conditions'][$join['key']] = $result['id'];
              $assocs = $this->{$alias}->find('all', $join);
              
              $results[$i][$alias] = $assocs;
            } else {
              $sets[$alias]['hasMany'] = true;
              $sets[$alias]['join']    = $join;
              $sets[$alias]['ids'][$result['id']] = $i;
            }
            
            $results[$i][$alias] = array();
          }
        }
      }
    }

    foreach ($sets as $alias => $set) {
      $join = $set['join'];
      if (! isset($join['conditions']))
        $join['conditions'] = array();
      $join['alias'] = $alias;
      $indexes = array_keys($set['ids']);
      $join['conditions'][$set['join']['key']] = $indexes;
      unset($join['limit']);    
      $accoss = $this->{$alias}->find('all', $join);
      foreach ($accoss as $result) {
        $index = $set['ids'][$result[$set['join']['key']]];
        if (! $index)
          $index = 0;
        if (isset($set['hasMany']))
          $results[$index][$alias][] = $result;
        else
          $results[$index][$alias] = $result;
      }
    }
  }
  
  function inContain($alias, $contain = false, &$join = array()) {
    if ($contain === false)
      return true;
    if (isset($contain[$alias]) || in_array($alias, $contain)) {
      if (isset($contain[$alias])) {
        $fields = array('fields', 'conditions', 'limit');
        foreach ($fields as $field)
          if (isset($contain[$alias][$field])) {
            $join[$field] = $contain[$alias][$field];
            unset($contain[$alias][$field]);
          }
          
        $join['contain'] = $contain[$alias];
      } else
        $join['contain'] = array();
        
      return true;
    } else
      return false;
  }

  function afterFind($results) {
    if (isset($this->models[$this->table])) {
      extract($this->models[$this->table]);

      $model = new $class();
      $results = $model->afterFind($results);
    }

    return $results;
  }
     
  function find($method, $options = array()) {
    if (is_array($method)) {
      $options['conditions'] = $method;
      $method = 'all';
    }
    
    if (isset($options['page']) && isset($options['limit']) && $options['limit'] > 0) {
      $page  = $options['page'];
      $limit = &$options['limit'];
      $from  = ($page - 1) * $limit;
      $limit =  $from . ',' . $limit;
    }
    
    if (! isset($options['contain']))
      $options['contain'] = false;
    
    if (! isset($options['alias']))
      $options['alias'] = $this->alias;
    
    if (in_array($method, array('all', 'first', 'threaded', 'count'))) {    
      if (! empty($this->belongsTo)) 
        foreach ($this->belongsTo as $alias => $join) 
          if ($this->inContain($alias, $options['contain'], $join))
            $options['joins'][$alias] = $this->join($join, 'to');   
      
      if (! empty($this->hasOne)) 
        foreach ($this->hasOne as $alias => $join) 
          if ($this->inContain($alias, $options['contain'], $join))
            $options['joins'][$alias] = $this->join($join, 'has');
    }
    
    $results = array();
    if ($method == 'all' || $method == 'threaded') {
      $results = $this->dbo->selectAll($this->table, $options);        
    } elseif ($method == 'first') {
      $options['limit'] = 1;
      $results = $this->dbo->selectAll($this->table, $options);      
    } elseif ($method == 'list') {
      $results = $this->dbo->selectList($this->table, $options); 
      if (isset($options['fields']) && count($options['fields']) == 1)
        return array_keys($results);     
    } elseif ($method == 'index') {
      if (! isset($options['fields']))
        $options['fields'] = array('id', 'id');
      $results = $this->dbo->selectList($this->table, $options); 
      foreach ($results as $i => $row)
        $results[$i] = $row[$options['fields'][1]];
      return $this->afterFind($results);
    } elseif ($method == 'count') {
      $results = $this->dbo->selectOne($this->table, 'count('.$this->alias.'.id)', array_merge($options, array('joinFields' => false))); 
      return $this->afterFind($results);
    } elseif ($method == 'field') {
      $results = $this->dbo->selectOne($this->table, $options['field'], $options); 
      return $this->afterFind($results);
    } 
    
    if (! empty($results)) {              
      $this->assoc($options, $results); 
      if ($method == 'first')
        $results = $results[0];     
    }
    
    if ($method == 'threaded') {      
      $results = $this->dbo->selectAll($this->table, $options);
      
      $return = $idMap = $ids = array();
      
      foreach ($results as $result)
        $ids[] = $result['id'];

			foreach ($results as $result) {
				$result['children'] = array();
				$id                 = $result['id'];
				$parentId           = $result['parent_id'];
        
				if (isset($idMap[$id]['children'])) {
					$idMap[$id] = array_merge($result, (array)$idMap[$id]);
				} else {
					$idMap[$id] = array_merge($result, array('children' => array()));
				}
				if (!$parentId || !in_array($parentId, $ids)) {
					$return[] =& $idMap[$id];
				} else {
					$idMap[$parentId]['children'][] =& $idMap[$id];
				}
			}
      
      $results = $return;
    }
    
    $this->data    = $results;
    
    return $this->afterFind($results);
  }
  
  function children($id = 0, $options = array()) {
    if ($id > 0) {
  		$result = $this->find('first', array(
  			'conditions' => array('id' => $id),
  			'fields' => array('lft', 'rght'),
  			'recursive' => -1
  		));
      
      if (empty($result) || !isset($result['lft'])) {
  			return array();
  		}
      
  		$conditions = array(
  			'lft >'  => $result['lft'],
  			'rght <' => $result['rght']
  		);
      
      if (isset($options['conditions']))
        $options['conditions'] = array_merge($options['conditions'], $conditions);
      else
        $options['conditions'] = $conditions;
    }
    
    if (! isset($options['method']))
      $options['method'] = 'threaded';
      
    $results = $this->find($options['method'], $options);
    return $results;
  }
  
  function parents($id, $options = array()) {
    if ($id > 0) {
  		$result = $this->find('first', array(
  			'conditions' => array('id' => $id),
  			'fields' => array('lft', 'rght'),
  			'recursive' => -1
  		));
      
      if (empty($result) || !isset($result['lft'])) {
  			return array();
  		}
      
  		$conditions = array(
  			'lft <='  => $result['lft'],
  			'rght >=' => $result['rght']
  		);
      
      $options['order'] = 'lft asc';
      
      if (isset($options['conditions']))
        $options['conditions'] = array_merge($options['conditions'], $conditions);
      else
        $options['conditions'] = $conditions;
    }
    
    if (! isset($options['method']))
      $options['method'] = 'all';
      
    $results = $this->find($options['method'], $options);
    return $results;
  }
  
  function field($field, $condition = array(), $order = false) {
    $result = $this->find('field', array(
      'conditions' => $condition,
      'order'      => $order,
      'field'      => $field
    ));
    return $result;
  }
  
  function deleteBranch($id) {
    if ($id > 0) {
  		$result = $this->find('first', array(
  			'conditions' => array('id' => $id),
  			'fields' => array('lft', 'rght'),
  			'recursive' => -1
  		));
      
      if (empty($result) || !isset($result['lft'])) {
  			return false;
  		}
      
      $diff = $result['rght'] - $result['lft'] - 1;
      
  		$this->delete(array('lft >' => $result['lft'], 'rght <' => $result['rght']));
      $this->delete(array('id' => $id));
      
      $count = $this->field('count(id)');
      if ($count > 1) {
        $this->update("rght = rght - $diff", array('rght >=' => $result['rght']));
        $this->update("lft = lft - $diff", array('lft >=' => $result['lft']));
      } else {
        $this->update("lft = 1, rght = 2", "1=1");
      }
    }    
  }
  
  
	function __sync( $shift, $dir = '+', $conditions = array(),$field = 'both') {
    if ($field == 'both') {
			$this->__sync($shift, $dir, $conditions, 'lft');
			$field = 'rght';
		}
    
		$this->update($field . ' = ' . $field . ' ' . $dir . ' ' . $shift, "{$field} {$conditions}");
	}
  
  function setParent($id, $parentId = null) {
		$node = $this->find('first', array(
			'conditions' => array('id' => $id),
			'fields' => array('id', 'parent_id', 'lft', 'rght')
		));
    
		$edge = $this->field('max(rght)');
    if (! $edge)
      $edge = 0;

		if (empty($parentId)) {
			$this->__sync($edge - $node['lft'] + 1, '+', 'BETWEEN ' . $node['lft'] . ' AND ' . $node['rght']);
			$this->__sync($node['rght'] - $node['lft'] + 1, '-', '> ' . $node['lft']);
		} else {
			$parentNode = $this->find('first', array(
				'conditions' => array('id' => $parentId),
				'fields' => array('id', 'lft', 'rght'),
				'recursive' => $recursive
			));

			if (empty($parentNode)) {
				return false;
			}

			if ($id == $parentId) {
				return false;

			} elseif (($node['lft'] < $parentNode['lft']) && ($parentNode['rght'] < $node['rght'])) {
				return false;
			}
			if (empty ($node['lft']) && empty ($node['rght'])) {
				$this->__sync(2, '+', '>= ' . $parentNode['rght']);
				$result = $this->save(
					array('lft' => $parentNode['rght'], 'rght' => $parentNode['rght'] + 1, 'parent_Id' => $parentId)
				);
			} else {
				$this->__sync($edge - $node['lft'] +1, '+', 'BETWEEN ' . $node['lft'] . ' AND ' . $node['rght']);
				$diff = $node['rght'] - $node['lft'] + 1;

				if ($node['lft'] > $parentNode['lft']) {
					if ($node['rght'] < $parentNode['rght']) {
						$this->__sync($diff, '-', 'BETWEEN ' . $node['rght'] . ' AND ' . ($parentNode['rght'] - 1));
						$this->__sync($edge - $parentNode['rght'] + $diff + 1, '-', '> ' . $edge);
					} else {
						$this->__sync($diff, '+', 'BETWEEN ' . $parentNode['rght'] . ' AND ' . $node['rght']);
						$this->__sync($edge - $parentNode['rght'] + 1, '-', '> ' . $edge);
					}
				} else {
					$this->__sync($diff, '-', 'BETWEEN ' . $node['rght'] . ' AND ' . ($parentNode['rght'] - 1));
					$this->__sync($edge - $parentNode['rght'] + $diff + 1, '-', '> ' . $edge);
				}
			}
		}
    
    $this->save(array('id' => $id, 'parent_id' => $parentId));
    
		return true;
	}
  
  function addBranch($data) {
    if ($data['parent_id'] == 0) {
      $rght = $this->field('max(rght)');
      if (! $rght)
        $rght = 1;
      $data['lft']  = $rght;
      $data['rght'] = $rght + 1;
    } else {     
      $rght = $this->field('rght', array('id' => $data['parent_id']));
      
      $data['lft']  = $rght;
      $data['rght'] = $rght + 1;      
      
      $this->update("`rght` = `rght` + 2", array('rght >=' => $rght));
      $this->update("`lft` = `lft` + 2", array('lft >' => $rght));
    }
    
    return $data;
  }
  
  function save($data) {
    if (! empty($data)) {
      if (isset($data['id']) && ! $data['id'])
        unset($data['id']);
      
      $schema           = $this->dbo->schema($this->table);
      $directlySaveData = array(); 
      foreach ($data as $key => $value)
        if (isset($schema[$key]))
          $directlySaveData[$key] = $value;  
      
      if (isset($schema['modified']) && ! isset($data['modified'])) 
        $data['verified:modified'] = 'NOW()';  
        
      if (isset($data['id'])) {
        $this->beforeSave($data);
        $result = $this->dbo->update($this->table, $directlySaveData, array('id' => $data['id']));
        $this->afterSave($data, $data['id']);
        return $data['id'];
      } else {                  
        if (isset($schema['created']) && ! isset($data['created'])) 
          $directlySaveData['verified:created'] = 'NOW()';
          
        foreach ($schema as $field) {
          if (! $field['null'] && ! isset($directlySaveData[$field['name']]) && ! isset($directlySaveData['verified:' . $field['name']])) {
            $directlySaveData[$field['name']] = $this->dbo->defaultValue($field['type']);
          }
        }
        
        if (isset($data['parent_id']) && isset($schema['lft']) && isset($schema['rght']))
          $directlySaveData = $this->addBranch($directlySaveData);
        
        $this->beforeSave($data);
        $id = $this->dbo->insert($this->table, $directlySaveData);
        if ($id) {
          $this->afterSave($data,$id);
          return $id;
        }
      }
    }
  }
  
  function beforeSave(&$data) {
    $this->belongsToSave($data);
  }
  
  function afterSave($data, $id) {
    $this->hasManySave($data, $id);
    $this->hasOneSave($data, $id);
  }
  
  function hasManySave($data, $id) {
		if (! empty($this->hasMany)) {
			foreach ($this->hasMany as $assoc => $params) {
				if (isset($data[$assoc]) && ! empty($data[$assoc])) {
					$foreignKey = $params['key'];
					$old = array_flip($this->{$assoc}->find('list', array(
            'conditions' => array($foreignKey => $id),
            'fields'     => array('id')
          )));
          
					foreach ($data[$assoc] as $i => $item)
						if (is_numeric($i)) {
							if (isset($item['id']))
								unset($old[$item['id']]);
							$item[$foreignKey] = $id;
							$this->{$assoc}->save($item);
						}
            
          $old = array_flip($old);
          
					if (! empty($old))
						$this->{$assoc}->delete(array('id' => $old));
				}
			}
		}
	}

	function hasOneSave($data, $id) {
		if (! empty($this->hasOne))
			foreach ($this->hasOne as $assoc => $params)
				if (isset($data[$assoc]) && ! empty($data[$assoc])) {
					$foreignKey = $params['key'];
					$data[$assoc][$foreignKey] = $id;
					$this->{$assoc}->delete(array($assoc.'.'.$foreignKey => $this->id));
					$this->{$assoc}->save($data[$assoc]);
				}
	}

	function belongsToSave(&$data) {
		if (! empty($this->belongsTo))
			foreach ($this->belongsTo as $assoc => $params)
				if (isset($data[$assoc]) && is_array($data[$assoc]) && ! empty($data[$assoc])) {
					$foreignKey = $params['key'];
      
					if ($data[$foreignKey])
						$this->{$assoc}->delete(array('id' => $data[$foreignKey]));
					$data[$foreignKey] = $this->{$assoc}->save($data[$assoc]);
				}
	}
  
  function update($data, $conditions) {
    return $this->dbo->update($this->table, $data, $conditions);
  }
  
  function delete($conditions) {
    return $this->dbo->delete($this->table, $conditions);
  }
  
  function del($id) {
		if (! empty($this->hasMany))
			foreach ($this->hasMany as $assoc => $params) {
        $foreignKey = $params['key'];
        $this->{$assoc}->delete(array($foreignKey => $id));
			}
      
    return $this->dbo->delete($this->table, array('id' => $id));
  }
  
  function saveUrl(&$data, $condition = array()) {
    if (! empty($data['alias'])) {      
      $parents   = $this->parents($data['id'], array(
        'method'     => 'list',
        'fields'     => array('alias'),
        'conditions' => $condition
      ));
      
      if (isset($parents[0]) && $parents[0]['alias'] == '/')
        unset($parents[0]);    
      
      $data['url'] = join('/', $parents);
      
      $this->save(array('id' => $data['id'], 'url' => '/' . $data['url']));
      
      $children = $this->children($data['id'], array(
        'method'     => 'all',
        'conditions' => array_merge(array('id <>' => $data['id']), $condition),
        'fields'     => array('id', 'alias')
      ));    
      
      if (! empty($children)) {
        foreach ($children as $item)
          $this->saveUrl($item, $condition);
      }
    }
  }
  
}

?>