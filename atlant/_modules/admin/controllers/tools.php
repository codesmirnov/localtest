<?

class Tools extends Controller {
  
  var $viewPath = 'tools';
  
  function beforeFilter() {  
    $this->loadModel('Map',   array(
      'table' => '_sys_map'
    ));
  }
  
  function index() {
    if (! empty($this->params['pass']))
      $this->renderError('404');
      
    $tools = $this->Map->find('all', array(
      'conditions' => array(
        'id'        => $this->access,
        'parent_id' => $this->params['current']['id']
      )
    ));
    
    foreach ($tools as &$tool) {
      $controller           = $tool['controller'];
      $params               = $this->params;
      $params['url']['url'] = $tool['url'];
      $tool['children'] = $this->request($controller, '_preview', $params);
    }
        
    $this->set('tools', $tools);
    $this->render('index');
  }
  
} 

?>