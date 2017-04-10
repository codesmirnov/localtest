<?

class CustomeHelper extends Helper {
  
  var $uses = array('Html');
  
  function pagitorLink($title, $page, $mode, $attr = array()) {
    if ($mode == 'link') {
      if (! empty($_GET)) {
        $params = '';
        foreach ($_GET as $param => $value)
          if ($param != 'url' && ! empty($value)) {
            $params .= (! empty($params) ? '&' : '') . $param . '=' . $value;
          }
        }
        if (! empty($params))
          $params = '?' . $params;
        
      return $this->Html->link($title, $page . $params, $attr);
    } else {
      return $this->Html->paramLink($title, "page=$page", $attr);
    } 
  }
  
  function paginator($options = array()) {
    if (! isset($this->params['paginate']) || $this->params['paginate']['pageCount'] <= 1)
      return '';
      
    extract($this->params['paginate']);
    
    $default = array(
      'tagName'     => 'div',
      'class'       => 'paginator',
      'label'       => '',
      'activeTag'   => 'span',
      'activeClass' => 'acv',
      'firstLabel'  => 'Первая',
      'lastLabel'   => 'Последняя',
      'first'       => false,
      'last'        => false,
      'pageLimit'   => false,
      'mode'        => 'link'  
    ); 
    
    $options = array_merge($default, $options);
    extract($options);
    
    if (! $pageLimit)
      $pageLimit = $pageCount;
    else {      
      $offset = round($pageLimit/2);
      if ($page > $offset) {
        $offset = $page - $offset;
        $first  = $last = true;
      } else {
        $offset = 0;
        $last   = true;
      }
        
      $pageLimit = $pageLimit+$offset;
      if ($pageLimit > $pageCount) {
        $pageLimit = $pageCount;
        $last      = false;
      }
    }
    
    $out = '';
    for ($i = 1+$offset; $i <= $pageLimit; $i++) {
      if ($i == $page) {        
        $out .= $this->tag($activeTag, $i, array('class' => $activeClass));        
      } else {
        $out .= $this->pagitorLink($i, $i, $mode);
      }
    }
      
    if ($first)
      $out = $this->pagitorLink($firstLabel, 1, $mode, array('class' => 'first')) . $out;
      
    if ($last)
      $out .= $this->pagitorLink($lastLabel, $pageCount, $mode, array('class' => 'last'));
    
    $out = $this->tag($tagName, (! empty($label) ? $label . ' ' : '') . $out, array('class' => $class));
    
    return $out;
  }

	function date($date = null) {
		list($date, $time) = explode(' ', $date);
    
    if ($date == date('Y-m-d'))
      return 'сегодня';
    
    if ($date == date('Y-m-d', time() - 60 * 60 * 24))
      return 'вчера';    

	  list($y, $m, $d) = explode('-', $date);

	  if ($m == 1) $m = 'января';
	  elseif ($m == 2) $m = 'февраля';
	  elseif ($m == 3) $m = 'марта';
	  elseif ($m == 4) $m = 'апреля';
	  elseif ($m == 5) $m = 'мая';
	  elseif ($m == 6) $m = 'июня';
	  elseif ($m == 7) $m = 'июля';
	  elseif ($m == 8) $m = 'августа';
	  elseif ($m == 9) $m = 'сентября';
	  elseif ($m == 10) $m = 'октября';
	  elseif ($m == 11) $m = 'ноября';
	  elseif ($m == 12) $m = 'декабря';
    
		if ($y < date('Y') || $y > date('Y'))
			$format = array('d', 'm', 'y');
		else
			$format = array('d', 'm');
      
	  $y = $y . ' года';

	  $date = array('d' => $d, 'm' => $m, 'y' => $y);

	  $out = '';

	  foreach($date as $t => $v)
		  if (in_array($t,$format)) {
		    $v = preg_replace('~^0~', '', $v);
			  $out .= $v . ' ';
      }

	  return $out;
	}
  
  function dateInterval($d1, $d2) {
    $out = '';
    
    if ($d1 == $d2) {
      $out = $this->date($d1);
    } else {
    
      list($y1,$m1,$dd1) = explode('-', $d1);
      list($y2,$m2,$dd2) = explode('-', $d2);
      
      if ($y1==$y2 && $m1==$m2) {        
        $out = preg_replace('~^0~', '', $dd1) . '-' . $this->date($d2);         
      } else {
        $out = $this->date($d1) . ' &mdash; ' . $this->date($d2);           
      }
    }
    
    return $out;
  }
  
  function price($number) {
    return array_shift(explode('.' ,$number));
  }

  function rur($number) {
    return array_shift(explode(',' ,number_format($number, 2, ',', ' ')));
  }
  
  
}

?>