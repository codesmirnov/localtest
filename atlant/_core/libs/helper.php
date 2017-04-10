<?

class Helper {
  
  var $tags = array(
		'meta' => '<meta%s/>',
		'metalink' => '<link href="%s"%s/>',
		'link' => '<a href="%s"%s>%s</a>',
		'pseudolink' => '<span data-href="%s"%s>%s</span>',
		'mailto' => '<a href="mailto:%s" %s>%s</a>',
		'form' => '<form %s>',
		'formend' => '</form>',
		'input' => '<input name="%s" %s/>',
		'textarea' => '<textarea name="%s" %s>%s</textarea>',
		'hidden' => '<input type="hidden" name="%s" %s/>',
		'checkbox' => '<input type="checkbox" name="%s" %s/>',
		'checkboxmultiple' => '<input type="checkbox" name="%s[]"%s />',
		'radio' => '<input type="radio" name="%s" id="%s" %s />%s',
		'radioone' => '<input type="radio" name="%s" %s />',
		'selectstart' => '<select name="%s"%s>',
		'selectmultiplestart' => '<select name="%s[]"%s>',
		'selectempty' => '<option value=""%s>&nbsp;</option>',
		'selectoption' => '<option value="%s"%s>%s</option>',
		'selectend' => '</select>',
		'optiongroup' => '<optgroup label="%s"%s>',
		'optiongroupend' => '</optgroup>',
		'checkboxmultiplestart' => '',
		'checkboxmultipleend' => '',
		'password' => '<input type="password" name="%s" %s/>',
		'file' => '<input type="file" name="%s" %s/>',
		'file_no_model' => '<input type="file" name="%s" %s/>',
		'submit' => '<input type="submit" %s/>',
		'submitimage' => '<input type="image" src="%s" %s/>',
		'button' => '<input type="%s" %s/>',
		'image' => '<img src="%s" %s/>',
		'tableheader' => '<th%s>%s</th>',
		'tableheaderrow' => '<tr%s>%s</tr>',
		'tablecell' => '<td%s>%s</td>',
		'tablerow' => '<tr%s>%s</tr>',
		'block' => '<div%s>%s</div>',
		'blockstart' => '<div%s>',
		'blockend' => '</div>',
		'tag' => '<%s%s>%s</%s>',
		'tagstart' => '<%s%s>',
		'tagend' => '</%s>',
		'para' => '<p%s>%s</p>',
		'parastart' => '<p%s>',
		'label' => '<label for="%s"%s>%s</label>',
		'fieldset' => '<fieldset%s>%s</fieldset>',
		'fieldsetstart' => '<fieldset><legend>%s</legend>',
		'fieldsetend' => '</fieldset>',
		'legend' => '<legend>%s</legend>',
		'css' => '<link rel="%s" type="text/css" href="%s" %s/>',
		'style' => '<style type="text/css"%s>%s</style>',
		'charset' => '<meta http-equiv="Content-Type" content="text/html; charset=%s" />',
		'ul' => '<ul%s>%s</ul>',
		'ol' => '<ol%s>%s</ol>',
		'li' => '<li%s>%s</li>',
		'error' => '<div%s>%s</div>',
		'javascriptblock' => '<script type="text/javascript">%s</script>',
		'javascriptstart' => '<script type="text/javascript">',
		'javascriptlink' => '<script type="text/javascript" src="%s"></script>',
		'javascriptend' => '</script>'
	);
  
  var $view   = false;
  
  var $params = array();
  
  var $data   = array();
  
  var $uses   = array();
  
  var $formError = array();
  
  function __construct(&$view) {
    $this->params    = &$view->params;
    $this->data      = &$view->data;
    $this->formError = &$view->controller->formError;
    
    if (! empty($this->uses))
      foreach ($this->uses as $helper) {
        $class = $helper . 'Helper';
        $this->{$helper} = new $class($this);
      }
  }
  
  function _parseAttributes($options, $exclude = null, $insertBefore = ' ', $insertAfter = null) {
		if (is_array($options)) {
			$options = array_merge(array('escape' => true), $options);

			if (!is_array($exclude)) {
				$exclude = array();
			}
			$keys = array_diff(array_keys($options), array_merge((array)$exclude, array('escape')));
			$values = array_intersect_key(array_values($options), $keys);
			$escape = $options['escape'];
			$attributes = array();

			foreach ($keys as $index => $key) {
				$attributes[] = $this->__formatAttribute($key, $values[$index], $escape);
			}
			$out = implode(' ', $attributes);
		} else {
			$out = $options;
		}
		return $out ? $insertBefore . $out . $insertAfter : '';
	}
  
	function __formatAttribute($key, $value) {
		$attribute = '';
		$attributeFormat = '%s="%s"';
		$minimizedAttributes = array('compact', 'checked', 'declare', 'readonly', 'disabled', 'selected', 'defer', 'ismap', 'nohref', 'noshade', 'nowrap', 'multiple', 'noresize');
		if (is_array($value)) {
			$value = '';
		}

		if (in_array($key, $minimizedAttributes)) {
			if ($value === 1 || $value === true || $value === 'true' || $value == $key) {
				$attribute = $key;
			}
		} else {
			$attribute = sprintf($attributeFormat, $key, $value);
		}
		return $attribute;
	}
  
  function tag($name, $text = null, $attributes = array(), $escape = false) {
		if ($escape) {
			$text = h($text);
		}
		if (!is_array($attributes)) {
			$attributes = array('class' => $attributes);
		}
		if ($text === null) {
			$tag = 'tagstart';
		} else {
			$tag = 'tag';
		}
		return $this->output(sprintf($this->tags[$tag], $name, $this->_parseAttributes($attributes, null, ' ', ''), $text, $name));
	}
  
  function output($str) {
		return $str;
	}
}

?>