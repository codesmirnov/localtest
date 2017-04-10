<?

class Crop extends Controller {
  
  var $viewPath = '_crop';
  
  function beforeFilter() {
    $this->loadModel('ImageCache', array(
      'table' => '_sys_image_cache'
    ));
  }
  
  function _resize($width, $height, $resizeWidth, $resizeHeight) {
    if ($width <= $resizeWidth && $height <= $resizeHeight) {
      return $source;
    } elseif (! $resizeWidth) {      
      $percent = $height / $resizeHeight;      
		} elseif (! $resizeHeight) {      
      $percent = $width / $resizeWidth;      
		} else {
			$percent = 1;

			if ($width > $height)     $percent = $width / $resizeWidth;
			elseif ($width < $height) $percent = $height / $resizeHeight;
			elseif ($resizeWidth == $resizeHeight) {
				if (($width - $resizeWidth)  >= ($height - $resizeHeight)) $percent = $width / $resizeWidth;
				if (($width - $resizeHeight) <= ($height - $resizeHeight)) $percent = $height / $resizeHeight;
			}
		}

		$newWidth  = $width / $percent;
		$newHeight = $height / $percent;
    
    return array($newWidth, $newHeight);
  }
  
  function _crop($path, $cropWidth, $cropHeight) {
    list($width, $height) = getimagesize(ROOT . $path);

		if ($cropWidth <= $width && $cropHeight <= $height) {
      $resizeWidth = $resizeHeight = false;  
  		if ($width/$height < $cropWidth/$cropHeight) 
        $resizeWidth = $cropWidth;
  		else 
        $resizeHeight = $cropHeight;  
        
  		list($width, $height) = $this->_resize($width, $height, $resizeWidth, $resizeHeight);
      
  		$cropX = floor($width / 2)  - floor($cropWidth / 2);
  		$cropY = floor($height / 2) - floor($cropHeight / 2);
      
			return array($width, $height, $cropX , $cropY);
		} else {
		  
			if ($width < $cropWidth)	
				$cropX = $width/2;
			else
				$cropX = -$width/2;
			if ($height < $cropHeight)	
				$cropY = $height/2;
			else
				$cropY = 0;
				
			return array($width, $height, $cropX , $cropY);
		}
  }
  
  function correct($data) {
    include LIBS . 'helpers' . DS . 'image.php';
    
    extract($data);
    
    $imageHelper = new ImageHelper();
    
    $type = $imageHelper->imageType($original);
    
    $source = $imageHelper->loadImage(ROOT . $original, $type);
    
    list($width, $height) = $imageHelper->imageSize($source);
    
    $thumb = $imageHelper->imageCreate($rWidth, $rHeight);     
    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $rWidth, $rHeight, $width, $height);
    $source = $thumb;    
    
    list($cropWidth, $cropHeight) = explode('x', $_GET['size']);
    
    $thumb = $imageHelper->imageCreate($cropWidth, $cropHeight); 
    imagecopymerge($thumb , $source , 0 , 0 , $cropX , -$cropY , $cropWidth , $cropHeight, 100);
    $source = $thumb;
    
    $imageHelper->putImage($source, $type, ROOT . $cache);
  }
  
  function index() {
    if (isset($this->data['act']) && $this->data['act'] == 'correct') {
      $this->correct($this->data);
      exit;
    }
    elseif (isset($this->data['original'])) {
      list($width, $height) = explode('x', $_GET['size']);
      echo json_encode($this->_crop($this->data['original'], $width, $height));
      exit;
    }
    
    $sizes = array_keys($this->ImageCache->find('index', array(
      'conditions' => array('method' => '_crop'),
      'fields' => array('DISTINCT sizes')
    )));
    $this->set('sizes', $sizes);
    
    if (isset($_GET['size'])) {
      $images = $this->ImageCache->find('all', array(
        'conditions' => array('sizes' => $_GET['size'])
      ));
      $this->set('images', $images);
    }
    
    $this->render('index');
  }
  
} 

?>