<?

class Images extends Controller {
  
  var $viewPath = '_images';
  
  function beforeFilter() {
    $this->loadModel('Cache', array(
      'table' => '_sys_image_cache'
    ));
    
    $this->loadModel('Field', array(
      'table' => '_sys_model_fields'
    ));
    
    if (isset($this->params['url']['field'])) {
       $this->init($this->params['url']['field']);
    } 
  }
  
  function init($field = null) {
    $field = $this->Field->find('first', array(
      'conditions' => array('id' => $field)
    ));
    if (! empty($field)) {
      $field['Params']       = json_decode($field['params'], true);
      $this->params['field'] = $field;
      
      $this->Images = new Model(array(
        'table' => $field['Params']['table']
      ));      
    } 
  }
  
  function add() {
    #$this->clear();
    
    $token = -$this->params['url']['token'];    
    extract($this->params['field']['Params']);    
    
    if ($path = fileCopy('Filedata', $path)) {
      $pos = $this->Images->field('count(id)', array($join_key => $token)); 

      if ($id = $this->Images->save(array($join_key => $token, 'path' => $path, 'pos' => $pos))) {
        $this->set('id', $id);
        $this->set('path', $path);
        $this->render('success');
      }
    }
	}
  

	function order() {
    if (! isset($this->data['list']) || empty($this->data['list']))
      return false;
    $list = explode(',', $this->data['list']);
    foreach ($list as $pos => $image) {
      $this->Images->save(array('id' => $image, 'pos' => $pos + 1));
    }
	}
  
  function clearCache($path) {
    $name   = basename($path);
    $thumbs = $this->Cache->find('all', array(
      'conditions' => array('image' => $name)
    ));
    
    if (! empty($thumbs))
    foreach ($thumbs as $thumb) {
      unlink(ROOT . $thumb['cache']);
      $this->Cache->del($thumb['id']);
    }
  }

	function clear() {
    $key    = $this->params['field']['Params']['join_key'];
    $token  = $this->params['url']['token'];
    $images = $this->Images->find('all', array(
      'conditions' => array($key . ' <>' => -abs($token), $key . ' <' => 0)
    ));
    
    if (! empty($images))
    foreach ($images as $image) {
      $this->clearCache($image['path']);
      unlink(ROOT . $image['path']);
      #$this->Images->del($image['id']);
    }
  }
  
  function del($id = null) {
    if (! $id)
      $id = $this->data['id'];
    $image = $this->Images->find('first', array(
      'conditions' => array('id' => $id)
    ));
    if (! empty($image)) {
      $this->clearCache($image['path']);
      @unlink(ROOT . $image['path']);
      $this->Images->del($image['id']);      
    }
  }
  
  function deleteAll($id = null) {
    if (! $id) 
      return false;
    $key    = $this->params['field']['Params']['join_key'];
    $images = $this->Images->find('list', array(
      'fields'     => array('id'),
      'conditions' => array($key => $id)
    ));
    
    foreach ($images as $image)
      $this->del($image);
  }
  
  function edit($id = null) {
    $image = $this->Images->find('first', array(
      'conditions' => array('id' => $id)
    ));
    if (! empty($image)) {
      $this->params['current']['title'] = $image['path'];
      $this->set('img', $image);
      $this->render('edit', 'default');
    }
  }
}

?>