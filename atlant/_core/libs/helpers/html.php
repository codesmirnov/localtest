<?

class HtmlHelper extends Helper {
  
  var $_resources = array();
  var $_templateResources = array();
  
  var $_templates = array();
  var $_current   = '';
  
  var $vertion = '';
  
  var $temp = array();
  
  function resource($path = array()) {
    $paths = $path;
    if (is_string($path))
      $paths = array($path);
    
    if (! empty($paths))
      foreach ($paths as $path) {
        preg_match('/\.([^\.]+)$/i', $path, $match);
        if (isset($match[1]) && ! empty($match[1])) {
          $e = $match[1];
          $this->_resources[$e][] = $path;
        }    
      }  
  }
  
  function resourceLink($type, $path) {
    $out = '';        
		switch($type){
			case 'css':
				$out = '<link id="_'.md5($path).'" rel="stylesheet" type="text/css" href="'.$path.'" />' . "\r\n\t";
				break;
			case 'js':
				$out = '<script id="_'.md5($path).'" type="text/javascript" src="'.$path.'"></script>' . "\r\n\t";
				break;
		}
    
    return $out;     
  }    
  
  function resources() {
    $out = '';    
    $this->_resources = array_merge_recursive($this->_resources, $this->_templateResources);
    if (! empty($this->_resources)) {     
      foreach($this->_resources as $type => $paths) {        
    		foreach($paths as $path) {   
          $out .= $this->resourceLink($type, $path);  
        }            
      }
    }
    return $out;
  }

	function href($url) {
    if ($url == ':back')
			$url = '/' . preg_replace('/\/([^\/]+)$/us', '', $this->params['url']['url']);
		elseif (! empty($url) && $url{0} != '/')
			$url = '/' . $this->params['url']['url'] . '/' . $url;
		return $url;
	}
  
  function link($title, $url = '', $attr = array()) {
    if ($url !== null && $url != $this->params['url']['url'] && ! ($url == '/' . $this->params['url']['url'] || $url == '/' . $this->params['url']['url'] . '/')) {
      $url = $this->href($url);
      
      if (isset($attr['pseudo']) && ! $attr['pseudo']) unset($attr['pseudo']);
        
      if (isset($attr['pseudo']) && $attr['pseudo']) {
        $attr['class'] .= ' noindexlink';
        unset($attr['pseudo']);
		    return $this->output(sprintf($this->tags['pseudolink'], $url, $this->_parseAttributes($attr), $title));    
      } else
		    return $this->output(sprintf($this->tags['link'], $url, $this->_parseAttributes($attr), $title));
		} else {
			if (isset($attr['activeClass'])){
				if (! isset($attr['class']))
					$attr['class'] = $attr['activeClass'];
				else
					$attr['class'] .= ' ' . $attr['activeClass'];
				unset($attr['activeClass']);
			}

			return $this->tag('span', $title, $attr);
		}
  }

  function pageLink($item) {
    return $this->link($item['title'], $item['url']);
  }
  
  function a($title, $url = '', $attr = array()) {
    return $this->link($title, $url, $attr);
  }
  
  function paramLink($title, $params = null, $htmlAttributes = array()) {
		$params = explode('&', $params);

		$url = '';

		if (isset($htmlAttributes['url'])) {
			$url = $this->params['url']['url'] . '?' . $htmlAttributes['url'];
			unset($htmlAttributes['url']);
		} else
			$url = $this->params['url']['url'];

		$url = '/' . $url . '?';
    
    foreach ($_GET as $param => $value)
      if ($param != 'url') {
        $url .= $param . '=' . $value . '&';
      }

		foreach($params as $i => $param)
			if ($param{0}) {
				list($param, $value) = explode('=', $param);

				if (isset($this->params['url'][$param]) && $value == $this->params['url'][$param] && $i == 0) {
					$url = null;
					break;
        }
                          
				if ($value != 'none') {
					$url = preg_replace('/'.$param.'=([^&]+)&/', '' , $url);
					$url .= ($i > 0 ? '&' : '') . $param . '=' . $value;
				} else {
					$url = preg_replace('/'.$param.'=([^&]+)&/', '' , $url);
				}
			}

		return $this->link($title, $url, $htmlAttributes);
	}
  
  function massiveParamExplode($param) {
    if (isset($this->temp['_mpexp'][$param]))
      return $this->temp['_mpexp'][$param];
      
    $params = array();
      
    if (isset($this->params['url'][$param])) {
      $this->temp['_mpexp'][$param] = $params = explode(',', $this->params['url'][$param]);
    }
    
    return $params;    
  }
  
  function massiveParamExists($param, $value) {
    $params = $this->massiveParamExplode($param);
    
    if (in_array($value, $params))
      return true;
      
    return false;
  }
  
  function paramLinkMassive($title, $param, $value, $attr = array()) {
    $params = $this->massiveParamExplode($param);
    
    if (! in_array($value, $params))
      $params[] = $value;
    else {
      $k = array_search($value, $params);
      unset($params[$k]);
      
      if (isset($attr['activeClass']))
        $attr['class'] = 'acv';                  
    }
    
    if (empty($params))
      $params = 'none';
    else      
      $params = join(',', $params);
    
    return $this->paramLink($title, $param . '=' . $params, $attr);
  }
  
  function crumbs() {
    $out = $this->link('Главная', '/');
    $url = '/';
    foreach ($this->params['crumbs'] as $i => $item) {
      if (! isset($item['url']))
        $item['url'] = $url . '/' . $item['alias'];
      $out .= ' / ' . $this->link($item['title'], $item['url']);
      if (isset($item['url']))
        $url = $item['url'];
    }
    
    return $out;
  }
  
  function start($name) {
    $this->_current = $name;
    ob_start();
  }
  
  function end() {
    $out = ob_get_clean();
    $this->templates[$this->_current] = $out;
  }
  
  function template($name, $data = array()) {
    $template = $this->templates[$name];
    $template = str_replace(array('<%', '%>'), array('<?', '?>'), $template);
    extract($data);
    $html = $this;
    eval('?>' . $template . '<?');
  }
  
}

?>