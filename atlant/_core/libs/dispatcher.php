<?php

class Dispatcher {  

  function mobile() {
    include  LIBS . 'import' . DS . 'Mobile_Detect.php';

    $detect = new Mobile_Detect;
    if ($detect->isMobile() && ! $detect->isTablet() && ! preg_match('~^m\..~', $_SERVER['HTTP_HOST'], $m)) {
      if (isset($_GET['desktop']))
        Session::write('_desktop', true);

      if (! Session::read('_desktop')) {
        Session::write('_mobile', true);
      } else {        
        Session::write('_mobile', false);
      }
    } else
        Session::write('_mobile', false);

  }
  
  function geo() {
    $city = Session::read('city');
    if ($city == '') {
      $content = curl_get('http://ipgeobase.ru:7020/geo?ip=' . $_SERVER['HTTP_X_REAL_IP']);
      $content = iconv('WINDOWS-1251', 'UTF-8', $content);
      preg_match('/<city>([^<]+)<\/city>/Usi', $content, $match);
      $city = $match[1];
      if (! empty($city)) {
        $cityModel = new Model(array('table' => 'c_cities'));
        $cityId = $cityModel->field('id', array('title' => $city));
        if ($cityId) {
          Session::write('city', array('title' => $city, 'id' => $cityId));
        } else
          Session::write('city', false);
      }
    } else {
      $city = Session::read('city');
      if (! isset($_GET['city'])) {
        $_GET['city'] = $city['id'];
      }
    }
  }
  
  function dispatch($url = '') {
    Session::start();

    $this->mobile();
    
    $this->cache();
    
    if (empty($url) && ! empty($_GET)) {
      $url    = $_GET['url'];
      $params = $_GET;
      unset($params['url']);
    }
    
    $route = Router::matchRoutes($url); 
    if (! empty($route)) {
      include $route['options']['path'];
      
      $controller = isset($route['options']['className']) ? $route['options']['className'] : $route['options']['controller'];
      $controller = new $controller($route);
    }
  }
  
  function cache() {    
    if (Session::check('city')) {      
      $city = Session::read('city');
      if (! isset($_GET['city'])) {
        $_GET['city'] = $city['id'];
      }
    }
    
    $name = md5(json_encode(array_merge($_GET, array('ajax' => isAjax()))));
    if (file_exists(CACHE . $name) && ! isset($_GET['non-cached'])) {  
      $view = new View();
      $view->cache = false;
      $view->loadHelpers();
      echo $view->_render(CACHE . $name);
      exit;
    }
  }
}

?>