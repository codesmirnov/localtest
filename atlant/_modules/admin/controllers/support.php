<?php

class Support extends Controller {
  
  var $viewPath = 'support';
  
  function index() {
    if (! empty($this->params['pass']))
      $this->renderError('404');
      
    $this->render('index');    
  }
  
}

?>