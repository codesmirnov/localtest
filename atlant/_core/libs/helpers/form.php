<?php

class FormHelper extends Helper {
  
  var $model;
  
  var $sessionData = false;
  
  function _field($fieldName) {
    $fieldName = explode('.', $fieldName);
    if (count($fieldName) == 1) {
      return $fieldName[0];
    } else {
      $model   = array_shift($fieldName);
      return $model . '[' . join('][', $fieldName) . ']';
    }
  }
  
  function _value($field) {    
    $data = array();
    if ($this->sessionData) {
      $data = get_array_value(Session::read('formData.' . $this->sessionData), $field);
    } else {
      $data = get_array_value($this->data, $field);
    }
    
    return $data;
  } 
   
  function _initInputField(&$fieldName, $options) {
    $submodel = false;
    $field = explode('.', $fieldName);
    if (count($field) > 1) {
      $submodel = $field[0];
      $field = $field[1];
    } else
      $field = $field[0];
      
    
    $options['name'] = $this->_field($fieldName);
    
    if (! isset($options['id'])) {
      $name = strtolower(str_replace('.', '_', $fieldName));
      $options['id'] = '_' . $name;
    }
    
    if (! isset($options['value'])) {
      $value = $this->_value($fieldName);
        
      if (! $value && isset($options['default']))
        $value = $options['default'];
        
      $options['value'] = $value;
    }
    
    return $options;
  }
  
  function create($model, $attr = array()) {
    $this->model = $model;
    
    if (! isset($attr['method'])) 
      $attr['method'] = 'post';
      
    if (isset($attr['sessionData'])) {
      $this->sessionData = $attr['sessionData']; 
    }
      
    if (! isset($attr['action'])) 
      $attr['action'] = '/' . $this->params['url']['url'];
    
    $attr = $this->_parseAttributes($attr);
		return $this->output(sprintf($this->tags['form'], $attr));
  }
  
  function end() {
		return $this->output($this->tags['formend']);
  }
  
  function input($fieldName, $options = array()) {
    if (! isset($options['type']))
      $options['type'] = 'text'; 
      
    $type = $options['type'];
      
    if ($options['type'] === 'radio') {
			$label = false;
			if (isset($options['options'])) {
				if (is_array($options['options'])) {
					$radioOptions = $options['options'];
				} else {
					$radioOptions = array($options['options']);
				}
				unset($options['options']);
			}
		}
      
    $selected = null;
		if (array_key_exists('selected', $options)) {
			$selected = $options['selected'];
			unset($options['selected']);
		}
		if (isset($options['rows']) || isset($options['cols'])) {
			$options['type'] = 'textarea';
		}

		$dateFormat = 'DMY';
		if (isset($options['dateFormat'])) {
			$dateFormat = $options['dateFormat'];
			unset($options['dateFormat']);
		}
      
    $out = '';  
    switch ($options['type']) {
			case 'hidden':
				$out = $this->hidden($fieldName, $options);
			break;
			case 'checkbox':
				$out = $this->checkbox($fieldName, $options);
			break;
			case 'radio':
				$out = $this->radio($fieldName, $radioOptions, $options);
			break;
			case 'text':
			case 'password':
				$out = $this->{$type}($fieldName, $options);
			break;
			case 'file':
				$out = $this->file($fieldName, $options);
			break;
			case 'select':
				$options = array_merge(array('options' => array()), $options);
				$list = $options['options'];
				unset($options['options']);
				$out = $this->select(
					$fieldName, $list, $selected, $options, $empty
				);
			break;
			case 'date':
				$out = $this->date(
					$fieldName, $dateFormat, $selected, $options, $empty
				);
			break;
			case 'textarea':
			default:
				$out = $this->textarea($fieldName, $options);
			break;
		}
    
    if (isset($this->formError[$fieldName])) {
      $out .= $this->tag('div', $this->formError[$fieldName], array('class' => 'form-error'));
    }
    
    return $out;
  }
  
  function text($fieldName, $options = array()) {
    $options = $this->_initInputField($fieldName, array_merge(
    	array('type' => 'text'), $options
    	));      
    
    return $this->output(sprintf(
    	$this->tags['input'],
    	$options['name'],
    	$this->_parseAttributes($options, array('name'), null, ' ')
    ));
	}
  
	function password($fieldName, $options = array()) {
		$options = $this->_initInputField($fieldName, $options);
		return $this->output(sprintf(
			$this->tags['password'],
			$options['name'],
			$this->_parseAttributes($options, array('name'), null, ' ')
		));
	}
  
  function hidden($fieldName, $options = array()) {
		$options = $this->_initInputField($fieldName, $options);
		return $this->output(sprintf(
			$this->tags['hidden'],
			$options['name'],
			$this->_parseAttributes($options, array('name'), '', ' ')
		));
	}
  
  function checkbox($fieldName, $options = array()) {
		$default = @$options['default'];
		$options = $this->_initInputField($fieldName, $options);
		$value   = $options['value'];

		if ($default && $value != '0')
			$value = $options['value'] = $default;

		if (!isset($options['value']) || empty($options['value'])) {
			$options['value'] = 1;
		} elseif (!empty($value) && $value === $options['value']) {
			$options['checked'] = 'checked';
		}

		$hiddenOptions = array(
			'id' => $options['id'] . '_', 'name' => $options['name'],
			'value' => '0', 'secure' => false
		);
		if (isset($options['disabled']) && $options['disabled'] == true) {
			$hiddenOptions['disabled'] = 'disabled';
		}
		$output = $this->hidden($fieldName, $hiddenOptions);

		return $this->output($output . sprintf(
			$this->tags['checkbox'],
			$options['name'],
			$this->_parseAttributes($options, array('name'), null, ' ')
		));
	}
  
	function radio($fieldName, $options = array(), $attributes = array()) {
		$attributes = $this->_initInputField($fieldName, $attributes);

		$value = $this->_value($fieldName);
    
		$out = array();

    if (is_string($options)) {
			$options = array($options => $options);
    } 
    
    if ( !empty($options)) {
      foreach ($options as $optValue => $optTitle) {
        if (isset($attributes['keys']) && $attributes['keys'])
          $optValue = $optTitle;
        $optionsHere = array('value' => $optValue);
        
        if (isset($value) && $optValue == $value) {
        	$optionsHere['checked'] = 'checked';
        }
        
        $parsedOptions = $this->_parseAttributes(
        	array_merge($attributes, $optionsHere),
        	array('name', 'type', 'id'), '', ' '
        );
        
        $radio = sprintf(
        	$this->tags['radio'], $attributes['name'],
        	$tagName, $parsedOptions, $optTitle
        );
        
        $out[] = $this->tag('div', $radio, array('class' => 'radio'));
      } 
     } else {
        
      if (isset($value) && $attributes['value'] == $value) {
      	$attributes['checked'] = 'checked';
      }
        
      $parsedOptions = $this->_parseAttributes(
      	$attributes,
      	array('name', 'type'), '', ' '
      );
      
      return sprintf(
      	$this->tags['radioone'], $attributes['name'],
      	$parsedOptions
      );
    }      

    $hidden = null;
    
    $out = $hidden . join('', $out);
    
    return $this->output($out);
  }
  
  function textarea($fieldName, $options = array()) {
		$options = $this->_initInputField($fieldName, $options);
		$value = null;

		if (array_key_exists('value', $options)) {
			$value = $options['value'];
			if (!array_key_exists('escape', $options) || $options['escape'] !== false) {
				$value = h($value);
			}
			unset($options['value']);
		}
		return $this->output(sprintf(
			$this->tags['textarea'],
			$options['name'],
			$this->_parseAttributes($options, array('type', 'name'), null, ' '),
			$value
		));
	}
  
  function file($fieldName, $options = array()) {
		$options = $this->_initInputField($fieldName, $options);
		$options = array(
			'class' => $options['class'],
			'type' => 'file',
			'name' => $options['name']
		);

		$attributes = $this->_parseAttributes($options, array('name'), '', ' ');
		return $this->output(sprintf($this->tags['file'], $options['name'], $attributes));
	}
  
  function select($fieldName, $options = array(), $selected = false, $attributes = array()) {
		$attributes = $this->_initInputField($fieldName, $attributes);
    
    if (! $selected) {
      $selected = $attributes['value'];
    }
    
    $keys = false;
    if (isset($attributes['keys']))
      $keys = $attributes['keys'];
    
    unset($attributes['keys']);
    unset($attributes['value']);
    
    $name       = $attributes['name'];
		$attributes = $this->_parseAttributes($attributes, array('name'), '', ' ');
    $out = $this->output(sprintf($this->tags['selectstart'], $name, $attributes));
    if (! empty($options))
    foreach ($options as $value => $title) {
      if ($keys)
        $value = $title;
      $out .= $this->output(sprintf($this->tags['selectoption'], $value, $selected == $value ? ' selected' : '', $title));
    }
    
    $out .= $this->output(sprintf($this->tags['selectend']));
    return $out;
  }
  
  function date($fieldName, $dateFormat = 'DMY', $selected = null, $attributes = array()) {
    $attributes = $this->_initInputField($fieldName, $attributes);
    if (! $attributes['value']) {
      $attributes['value'] = date('Y') . '-' . date('m') . '-' . date('d');
    }
    
    if ($selected)
      $attributes['value'] = $selected;
    
    list($d, $t) = explode(' ', $attributes['value']);
    list($value['y'], $value['m'], $value['d']) = explode('-', $d);
    list($value['h'], $value['i']) = explode(':', $t);
    
    if (! isset($attributes['minYear']))
      $attributes['minYear'] = date('Y') - 2;
    if (! isset($attributes['maxYear']))
      $attributes['maxYear'] = date('Y');
      
		$selects = array();
		foreach (preg_split('//', $dateFormat, -1, PREG_SPLIT_NO_EMPTY) as $char) {
			switch ($char) {
				case 'Y':
          $years = array();
          for ($y = $attributes['minYear']; $y <= $attributes['maxYear']; $y++)
            $years[$y] = $y;
            
					$selects[] = $this->select($fieldName . '.y', $years, $value['y'], array('class' => 'year'));
				break;
				case 'M':      
      		$months['01'] = 'Январь';
      		$months['02'] = 'Февраль';
      		$months['03'] = 'Март';
      		$months['04'] = 'Апрель';
      		$months['05'] = 'Май';
      		$months['06'] = 'Июнь';
      		$months['07'] = 'Июль';
      		$months['08'] = 'Август';
      		$months['09'] = 'Сентябрь';
      		$months['10'] = 'Октябрь';
      		$months['11'] = 'Ноябрь';
      		$months['12'] = 'Декабрь';
          
					$selects[] = $this->select($fieldName . '.m', $months, $value['m'], array('class' => 'month'));
				break;
				case 'D':
          $days = array();
          for ($d = 1; $d <= 31; $d++)
            $days[$d] = $d;
					$selects[] = $this->select($fieldName . '.d', $days, $value['d'], array('class' => 'day'));
				break;
				case 'H':
          $hours = array();
          for ($h = 0; $h <= 23; $h++)
            $hours[$h] = (($h < 10) ? '0' : '') . $h;
					$selects[] = $this->select($fieldName . '.h', $hours, $value['h'], array('class' => 'hour'));
				break;
				case 'I':
          $minutes = array();
          for ($m = 0; $m <= 59; $m++) 
            $minutes[$m] = (($m < 10) ? '0' : '') . $m;
					$selects[] = ': ' . $this->select($fieldName . '.i', $minutes, $value['i'], array('class' => 'minutes'));
				break;
			}
		}
    
    $selects = join('', $selects);
    return $selects;
  }
  
  function submit($title, $attr = array()) {
		return $this->output(sprintf(
			$this->tags['submit'],
			$this->_parseAttributes(array('value' => $title))
		));
  }
  
  function tableInputs($fieldName, $options = array(), $attributes = array()) {
    if (empty($options))
      return false;

    $data = $this->_value($fieldName);
    if (empty($data))
      $data[] = array();
            
    $out = $header = '';
    foreach ($options as $name => &$attr) {
      if (is_array($attr) && isset($attr['th'])) {
        $title = $attr['th'];  
        unset($attr['th']);
      } else
        $title = $attr;         
        
      $header .= $this->tag('th', $title);
    }      
    
    $out = $this->tag('tr', $header);
    
    foreach ($data as $i => $row) {
      $field = $fieldName . '.' . $i;
      $tr = '';
      
      foreach ($options as $name => $a) {
        if (! is_array($a)) $a = array();
        if (isset($row[$name]))
          $a['value'] = $row[$name];
        $input = $this->input($field . '.' . $name, $a);
        $tr   .= $this->tag('td', $input);
      }
      
      $tr .= $this->hidden($field . '.pos', array('class' => 'pos'));
      $tr .= $this->hidden($field . '.id');
      
      $out .= $this->tag('tr', $tr);
    }
    
    if (isset($attributes['wrap'])) {
      $tag = $attributes['wrap'];
      unset($attributes['wrap']);
      
      $out = $this->tag('table', $out);
      $out = $this->tag($tag, $out, $attributes);  
    } else   
      $out = $this->tag('table', $out, $attributes);  
      
    return $out;
 	}
  
  
}

?>