<?php 

class Dbo {
  
  var $db = null;
  
  var $basename = '';
  
  var $currentAlias = '';
  
  function __construct() {
    $config = Configure::read('db');
    if (empty($config))
      return false;
      
    $this->db = mysqli_connect($config['host'], $config['login'], $config['password']);
    mysqli_select_db($this->db, $config['database']);
    mysqli_set_charset($this->db, $config['charset']);
  }
  
  function disconnect() {
    
  }
  
  function query($query, $showError = true) {      
    if (isset($_GET['__debug']))
      debug($query);
      
    $result = mysqli_query($this->db, $query);
    if (! $result && $showError)
      debug(mysqli_error($this->db) . '<br/>' . $query, false);
    
    return $result;
  }
  
  function execute($query) {
    $result = $this->query($query);
    if ($result) {
      return $this->fetch($result);
    }    
  }
  
  function insertID() {
    return mysqli_insert_id($this->db);
  }
  
  function fieldDirect($results) {
    $columns = array();
    $count = mysqli_num_fields($results);
    for ($i = 0; $i < $count; $i++) {
      $columns[$i] = mysqli_fetch_field_direct($results, $i);
    }
    
    return $columns;
  }
  
  function fetch($queryResult) {
    $results = array();
    $columns = $this->fieldDirect($queryResult);
    $i = 0;
    while ($result = mysqli_fetch_row($queryResult)) {
      foreach ($columns as $j => $column) {
        if ($this->currentAlias == $column->table)
          $results[$i][$column->name] = ($result[$j]);
        else
          $results[$i][$column->table][$column->name] = ($result[$j]);
      }
      $i++;
    }
        
    return $results;
  }
  
  function selectAll($table, $options = array()) {
    $queryResult = $this->selectQuery($table, $options);
    if ($queryResult) {
      return $this->fetch($queryResult);
    }    
    return false;
  }
  
  function selectList($table, $options = array()) {
    $queryResult = $this->selectQuery($table, $options);
    if ($queryResult) {
      $results = array();
  		while ($result = mysqli_fetch_assoc($queryResult)) {
  			$temp = array_values($result);
  			$results[$temp[0]] = $result;
  		}
      return $results;
    }    
    return false;
  }
  
  function selectOne($table, $field, $options = array()) {
    $options['limit'] = 1;
    $options['fields'] = $field;
    $result = $this->selectQuery($table, $options);
    if (mysqli_num_rows($result) > 0) {
      $result = mysqli_fetch_assoc($result);
      return $result[$field];
    }
    return false;
  }
  
  function selectRow($table, $options = array()) {
    $options['limit'] = 1;
    $result = $this->selectQuery($table, $options);
    if (mysqli_num_rows($result) > 0)
      return mysqli_fetch_assoc($result);
    return false;
  }
  
  function selectQuery($table, $options = array()) {
    $query = 'SELECT ';
    
    $this->currentAlias = $alias = isset($options['alias']) ? $options['alias'] : $table;
    
    $joinPart = '';
    $joinFields = '';
    if (isset($options['joins']) && ! empty($options['joins'])) {
      foreach ($options['joins'] as $joinAlias => $join) {  
        if (isset($join['alias']))
          $joinAlias = $join['alias'];
        $conditions = '';
        if (isset($join['conditions']) && ! empty($join['conditions'])) 
          $conditions = ' AND (' . $this->conditions($joinAlias, $join['conditions']) . ')';        
        
        $joinPart .= ' LEFT JOIN `' . $join['table'] . '` AS `'.$joinAlias.'` ON ';
        if ($join['mode'] == 'has') {
          $joinPart .= '`' . $joinAlias . '`.`' .$join['key'] . '` = `' . $alias . '`.`id`' . $conditions; 
        } else {
          $joinPart .= '`' . $alias . '`.`' .$join['key'] . '` = `' . $joinAlias . '`.`id`' . $conditions;           
        }
        
        if (isset($join['fields']) && ! empty($join['fields'])) {
          $joinFields .= (! empty($joinFields) ? ', ' : '') . $this->fields($joinAlias, $join['fields']);
        } else
          $joinFields .= (! empty($joinFields) ? ', ' : '') . '`' . $joinAlias . '`.*';
      }
    }
        
    $fields = '';    
    if (isset($options['fields']) && ! empty($options['fields'])) {
      $fields .= $this->fields($alias, $options['fields']);    
    } else
      $fields .= '`' . $alias . '`.*';
    
    if (! empty($joinFields) && (! isset($options['joinFields']) || $options['joinFields']))
      $query .= $fields . ', ' . $joinFields;
    else
      $query .= $fields;
     
    $query .= ' FROM `'.$table.'` AS `' . $alias . '`'; 
    
    $query .= $joinPart;
    
    if (isset($options['conditions']) && ! empty($options['conditions'])) {
      $query .= ' WHERE ' . $this->conditions($alias, $options['conditions']);
    }
    
    if (isset($options['order']) && ! empty($options['order'])) {
      $query .= ' ORDER BY ' . $this->fields($alias, $options['order']);
    }
    
    if (isset($options['groupe']) && ! empty($options['groupe'])) {
      $query .= ' GROUPE BY ' . $this->fields($alias, $options['groupe']);
    }
    
    if (isset($options['limit']) && ! empty($options['limit'])) {
      $query .= ' LIMIT ' . $options['limit'];
    }
    
    return $this->query($query);
  }
  
  function insert($table, $data) {   
    $names = $values = '';
    foreach ($data as $name => $value) {
      $escape  = $this->verified($name);
      $names  .= (! empty($names) ? ', ' : '')  . "`$name`";
      $values .= ($values != '' ? ', ' : '') . ($escape ? $this->escape($value) : $value);
    }
    
    $query = "INSERT INTO `$table` ($names) VALUES ($values)";
    
    
    $this->query($query);
		return $this->insertID();
  }
  
  function update($table, $data, $conditions = array()) {
    $data       = $this->conditions($table, $data, ',', false);
    $conditions = $this->conditions($table, $conditions);
    if (! $conditions) $conditions = '1=1';
    $query = "UPDATE `$table` SET $data WHERE $conditions";  
    $this->query($query);
    return mysqli_affected_rows($this->db);
  }
  
  function delete($table, $conditions) {
    $conditions = $this->conditions($table, $conditions);
    $query = "DELETE FROM `$table` WHERE $conditions";
    $this->query($query);
    return mysqli_affected_rows($this->db);
  }
  
  function explodeFieldType($type) {
		preg_match('/\(([^\(\)]+)\)/s', $type, $matches);
		$type = preg_replace('/\(([^\(\)]+)\)/s', '', $type);
		$length = $matches[1];
		return array($type, $length);
	}
  
  function defaultValue($type) {
    if ($type == 'string' || $type == 'text')
      return '';
      
    return 0;
  }
  
  function columnType($type) {
    list($type, $length) = $this->explodeFieldType($type);
		if (in_array($type, array('date', 'time', 'datetime', 'timestamp'))) {
			return $type;
		}
		if (($type == 'tinyint' && $length == 1) || $type == 'boolean') {
			return 'boolean';
		}
		if (strpos($type, 'int') !== false) {
			return 'integer';
		}
		if (strpos($type, 'char') !== false || $type == 'tinytext') {
			return 'string';
		}
		if (strpos($type, 'text') !== false) {
			return 'text';
		}
		if (strpos($type, 'blob') !== false || $type == 'binary') {
			return 'binary';
		}
		if (strpos($type, 'float') !== false || strpos($type, 'double') !== false || strpos($type, 'decimal') !== false) {
			return 'float';
		}
		if (strpos($type, 'enum') !== false) {
			return "enum($vals)";
		}
		return 'text';
	}
  
  function schema($table) {
    if ($queryResult = $this->query("SHOW COLUMNS FROM `$table`")) {
      $results = array();
      while ($result = mysqli_fetch_assoc($queryResult))
        $results[] = $result;

      $schema  = array();
  		$primary = '';
  		foreach ($results as $i => $field) {
  			list($type, $length) = $this->explodeFieldType($field['Type']);
  			$schema[$field['Field']] = array(
  				'name'       => $field['Field'],
  				'myqsl_type' => strtoupper($type),
          'type'       => $this->columnType($type),
  				'length'     => $length,
  				'null'       => $field['Null'] == 'NO' ? false : true,
  				'auto'       => $field['Extra'] == 'auto_increment' ? true : false
  			);
  
  			if ($field['Key'] == 'PRI')
  				$primary = $field['Field'];
  		}
  
  		if (! empty($primary))
  			$schema['primary'] = $primary;
        
      return $schema;  
    }
  }
  
  function field($field, $table) {
    if (stristr($field, '('))
      return $field;
    
    $order   = '';
    $command = '';
    
    if (stristr($field, '.')) {
      list($table, $field) = explode('.', $field);
    } 
    
    if (stristr($field, 'ASC') || stristr($field, 'DESC')) {
      list($field, $order) = explode(' ', $field);
      $order = ' ' . $order;
    } 
    
    if (stristr($field, ' as ')) {
      list($field, $as) = explode(' as ', $field);
      $field = $field . '` AS `' . $as;
    } 
    
    elseif (stristr($field, ' ')) {
      list($command, $field) = explode(' ', $field);
      $command .= ' ';
    } 
        
    $field = "$command`$table`.`$field`$order";
      
    return $field;
  }
  
  function fields($table, $fields = array()) {
    if (is_string($fields)) 
      $fields = explode(',', $fields);
    $out = '';
    foreach ($fields as $i => $field) {
      $out .= (! empty($out) ? ', ' : '') . $this->field($field, $table);
    }
    return $out;
  }
  
  function escape($value = '') {
    $value = is_string($value) ?  '\''.mysqli_real_escape_string($this->db, $value).'\'' : $value;
    return $value;
  }
  
  function verified(&$param) {
    $verified = true;
    if (stristr($param, 'verified:')) {
      $param = str_replace('verified:', '', $param);
      $verified = false;
    }
    return $verified;
  }
  
  function operator(&$param) {
    $operators = array('<>', '>=', '<=', '>', '<', 'like', 'NOT IN', 'REGEXP', 'BETWEEN');
    foreach ($operators as $operator) {
      if (stristr($param, $operator)) {
        $param = str_replace(' ' . $operator, '', $param);
        return $operator;
      }
    }
    
    return '=';
  }
  
  function arrayValue($arr) {
    $out = '';
    foreach ($arr as $value) {
      $out .= (! empty($out) ? ', ' : '') . $this->escape($value);
    }
    return $out;
  }
  
  function conditions($table, $condition = array(), $separator = 'AND', $empty = true) {
    if (is_string($condition))
      return $condition;
      
    $out = '';
    
    if (! empty($condition))
    foreach ($condition as $param => $value) {
      if ($value == '_*')
        continue;
        
      if (in_array($param, array('EXISTS', 'NOT EXISTS'))) {
        $out .= $param . ' ' . $value;
        continue;
      }
      
      $operator = $this->operator($param);
      $escape   = $this->verified($param);
    
      if (stristr($param, '.')) {
        list($table, $param) = explode('.', $param);
      } 
      
      if ($param == 'OR' && is_array($value)) {
        $out .= (! empty($out) ? " $separator " : '') . '(' . $this->conditions($table, $value, 'OR') . ')'; 
      }
      elseif (is_array($value) && ! empty($value)) {
        if ($operator == '=')
          $operator = 'IN';
        $out .= (! empty($out) ? " $separator " : '') . "`$table`.`$param` $operator (" . $this->arrayValue($value) . ')';
      } else
        $out .= (! empty($out) ? " $separator " : '') . "`$table`.`$param` " . $operator . ' ' . ($escape ? $this->escape($value) : $value);
    } elseif (empty($empty))
      $out = '1=1';
    
    return $out;
  }
  
  
}

?>