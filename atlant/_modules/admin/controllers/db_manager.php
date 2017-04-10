<?php

class DbManager extends Controller {
  
  var $layouts = 'default';
  
  var $viewPath = 'tools/db';
    
	var $db;

	var $error = null;

	var $mysqlTypes = array(
			'TINYINT', 'INT', 'FLOAT', 'DATE', 'DATETIME', 'TIME',
			'VARCHAR', 'TEXT', 'SET');

	var $limit = 50;
  
  function afterDynamicRoutes() {
		if (isset($this->params['url']['table'])) {
			$this->params['url']['table'] = preg_replace('/^\_/s', '', $this->params['url']['table']);
			$this->params['url']['table_name'] = $this->params['url']['table'];
			$this->crumb($this->params['url']['table_name'], '_' . $this->params['url']['table']);
		}

		if (isset($this->params['url']['field'])) {
			$this->params['url']['field'] = preg_replace('/^\_/s', '', $this->params['url']['field']);
			$this->params['url']['field_name'] = $this->params['url']['field'];
			$this->crumb($this->params['url']['field_name'], '_' . $this->params['url']['field']);
		}
  }

	function beforeFilter() {
		$this->db = new Dbo();

		if (! isset($this->params['requested'])) {
			$this->params['current']['title'] = 'Управление базой данных';
		}

		$mysqlTypes = array();
		foreach ($this->mysqlTypes as $type)
			$mysqlTypes[$type] = $type;
    
    $this->set('mysqlTypes', $mysqlTypes);
	}
  
  function _preview() {
    $result = array();
    $tables = $this->db->execute("SHOW TABLE STATUS");
    foreach ($tables as $table) {
      $table = $table['TABLES'];
      $result[] = array(
        'title' => $table['Name'],
        'url'   => $this->params['url']['url'] . '/_' . $table['Name']
      );
    }
    return $result;
  }

	function query($query) {
		error_reporting(0);
		$result = $this->db->query($query);
		$error = mysql_error();
		if (! empty($error)) {
			$this->error = $error;
			$error .= '<br>' . $query;
			$this->set('error', $error);
			return false;
		}
		return $result;
	}

	function index() {
    if ( !$this->dynamicRoutes($this->params['current']['url'], array(
    	'/db_structure'              => 'structure',
    	'/sql'                       => 'sql',
    	'/add'                       => 'tableAdd',
    	'/:table/data'               => 'tableData',
    	'/:table/data/:field/:value' => 'tableDataSearch',
    	'/:table'                    => 'table'    
    ))) {
      $this->params['action'] = 'structure';
      $this->structure();      
    }
  
  	$this->set('db_sections', array('' => 'Струтура БД', 'sql' => 'SQL Запрос'), false);
    $this->render('db');
	}

	function structure() {
    $this->params['jump'] = array('Добавить таблицу', 'add');        

		$this->data = $this->db->execute("SHOW TABLE STATUS");
    foreach ($this->data as &$r)
      $r = $r['TABLES'];

		$need_fields = array(
			'Name' => 'Имя таблицы',
			'Rows' => 'Количество записей',
			'Auto_increment' => 'Auto_increment'
		);

		$this->set('need_fields', $need_fields, false);
		$this->set('target_field', array('Name', 'Rows'), false);
	}

	function sql() {
		$this->Navigation->add('Запрос к БД', 'sql');
		$this->Navigation->title('Запрос к базе данных');

		if (isset($this->params['form']['query'])) {
			$query = preg_replace('/ +/',' ' , $query);
			$query = $this->params['form']['query'] . ' ';

			if (preg_match('/^select.+from\s(.+)\s/Usi', $query, $match)) {
				if (! preg_match('/\slimit\s/Usi', $query, $match))
					$query .= ' LIMIT 0,' . $this->limit;
			}
			$result = $this->query($query);

			if (! empty($result)) {
				if (isset($result[0]) && count($result[0]) > 1) {
					foreach ($result as $i => $row)
						foreach ($row as $key => $data)
							foreach ($data as $field => $value) {
								$this->data[$i]['Table'][$key . '.' . $field] = $value;
							}
				} else
					foreach ($result as $i => $row)
						$this->data[$i]['Table'] = array_shift($row);
			}
		}

		$this->params['form']['query'] = $query;

		$this->db();
	}

	function tableAdd() {
		$this->crumb('Новая таблица', 'add');
		$this->params['current']['title'] = 'Новая таблица';

		if (! empty($this->data)) {
			if (empty($this->data['name']))
				$this->set('error', 'Укажите имя таблицы');
			else {
				if (empty($this->data['Fields']))
					$this->set('error', 'Нельзя создать таблицу без полей');
				else {
					$query = "CREATE TABLE IF NOT EXISTS `". $this->data['name'] . "` (";

					$primary_key = '';
					foreach ($this->data['Fields'] as $i => $field) {
						if (! isset($field['type']))
							$field['type'] = 'TINYINT';

						$query .= ($i > 0 ? ', ' : '') . '`'.$field['name'].'` ';
						$query .= $field['type'] . (! empty($field['length']) ? '('.$field['length'].')' : '');
						$query .= $field['null'] ? ' NULL' : ' NOT NULL';
						$query .= $field['auto'] ? ' AUTO_INCREMENT' : '';
						if ($field['primary'])
							$primary_key = $field['name'];
					}

					if (! empty($primary_key))
						$query .= ', PRIMARY KEY(`'.$primary_key.'`)';

					$query .= ')';

					$this->query($query);

					if (! $this->error)
						$this->redirect('/' . str_replace('add', $this->data['name'], $this->params['url']['url']));
				}
			}

		}
	}

	function _fieldType($type) {
		preg_match('/\(([^\(\)]+)\)/s', $type, $matches);
		$type = preg_replace('/\(([^\(\)]+)\)/s', '', $type);
		$length = $matches[1];
		return array($type, $length);
	}

	function tableStructured($table) {
    $queryResult = array();
    
		$r = $this->db->execute("SHOW COLUMNS FROM `$table`");
    foreach ($r as $row) 
      $queryResult[] = $row['COLUMNS'];
      
		$result  = array();
		$primary = '';
		foreach ($queryResult as $i => $field) {
			list($type, $length) = $this->_fieldType($field['Type']);
			$result[$i] = array(
				'id' => $field['Field'],
				'pos' => $i,
				'name' => $field['Field'],
				'type' => strtoupper($type),
				'length' => $length,
				'null' => $field['Null'] == 'NO' ? false : true,
				'auto' => $field['Extra'] == 'auto_increment' ? true : false
			);

			if ($field['Key'] == 'PRI')
				$primary = $result[$i]['primary'] = $field['Field'];
		}

		if (! empty($primary))
			$result['primary'] = $primary;

		return $result;
	}

	function table() {
    $this->params['current']['title'] = 'Структура таблицы ' . $this->params['url']['table_name'];
		$this->params['jump']             = array('Обзор данных', 'data');
    
    $table = $this->params['url']['table'];
		if (! empty($this->data)) {

		  $oldTable = array();
			$r = $this->tableStructured($table);
      foreach ($r as $key => $row) 
        $oldTable[$row['name']] = $row;      
      unset($oldTable['i']);

			$fields = $this->data['Fields'];
			foreach ($fields as $i => &$field) {
				$query = 'ALTER TABLE `' . $table . '`';

				if (isset($oldTable[$field['id']])) {
					unset($oldTable[$field['id']]);
					$query .= ' CHANGE ';
					$query .= '`' . $field['id'] . '` `' . $field['name'] . '` ';
				} else {
					$field['id'] = $field['name'];
					$query .= ' ADD ';
					$query .= '`' . $field['name'] . '` ';
				}

				$query .=	$field['type'] . (! empty($field['length']) ? '(' .$field['length'] . ')' : '');
				$query .= $field['null'] ? ' NULL' : ' NOT NULL';
				$query .= ($i == 0 ?  ' FIRST ' : ' AFTER `' . $fields[$i-1]['id'] . '`');

				$this->query($query);
			}

			foreach ($oldTable as $field)
				$this->query('ALTER TABLE `'.$table.'` DROP `'.$field['name'].'`;');

			$this->query('ALTER TABLE `'.$table.'` DROP PRIMARY KEY');
			$this->query('ALTER TABLE `'.$table.'` ADD PRIMARY KEY(`id`)');
			$this->query('ALTER TABLE `'.$table . '` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT');
		}

		$this->data['Fields'] = $this->tableStructured($this->params['url']['table']);  
    unset($this->data['Fields']['primary']);
	}

	function tableData() {
		if (! isset($this->params['url']['page']))
			$this->params['url']['page'] = 1;


		if (! isset($this->params['url']['order']))
			$this->params['url']['order'] = 'id';

		if (! isset($this->params['url']['limit']))
			$this->params['url']['limit'] = $this->limit;

		$this->params['url']['order'] = preg_replace('/\-$/us', ' DESC', $this->params['url']['order']);

		$this->crumb('Обзор', 'data');
    
    $this->params['current']['title'] = 'Обзор данных из таблицы ' . $this->params['url']['table_name'];
    
		$this->loadModel('Table', array(
      'table' => $this->params['url']['table']
    ));

		$params = array(
			'page' => $this->params['url']['page'],
			'order' => 'Table.' . $this->params['url']['order']
		);
    
    $this->data = $this->Table->find('all'); 

		#$this->paginate = $params;
		#$this->data = $this->paginate('Table');
	}
	
	function tableDataSearch() {
		$key   = $this->params['field'];
		$value = $this->params['value'];
		
		$this->Navigation->add('Обзор', 'data');
		$this->Navigation->title('Обзор данных из таблицы ' . $this->params['table_name']);
		$this->loadModel($this->params['table_name'], 'Table', $this->params['table']);
		
		$this->data = $this->Table->find('first', array(
			'conditions' => array(
				'Table.' . $key => $value
			)
		));
	}
}

?>