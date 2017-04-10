<?php

class ImageHelper extends Helper {
  
  var $cacheFolder = '/i/_thumbs';
  
  var $quality = 100;
  
  var $cacheRegister = false;
  
  function __construct() {
    $this->cacheRegister = new Model(array(
      'table' => '_sys_image_cache'
    ));
  }
  
  function writeRegister($data) {
    return $this->cacheRegister->save($data);
  }
  
  function checkRegister($image) {
    return $this->cacheRegister->find('all', array(
      'conditions' => array('image' => $image)
    ));
  }
  
  function clear($image) {
    $list = $this->checkRegister($image);
    if (! empty($list))
      foreach ($list as $thumb) {
        unlink($thumb['cache']);
      }
  }
  
  function imageType($path) {
    preg_match('~\.([^\.]+)$~', $path, $matches);
    return strtolower($matches[1]);
  }
  
  function imageSize($source) {
    $width  = imagesx($source);
    $height = imagesy($source);
    return array($width, $height);
  }
  
  function loadImage($path, $type) {
    $source;

    if ($type == 'gif')
      $source = imagecreatefromgif($path);
    if ($type == 'png')
      $source = imagecreatefrompng($path);
    if ($type == 'jpeg' || $type == 'jpg')
      $source = imagecreatefromjpeg($path);

    return $source;
  }

  function putImage($thumb, $type, $path) {
    $source;
    if ($type == 'gif')
      $source = imagegif($thumb, $path);
    if ($type == 'png')
      $source = imagepng($thumb, $path);
    if ($type == 'jpeg' || $type == 'jpg')
      $source = imagejpeg($thumb, $path, $this->quality);

    return $source;
  }
  
  function imageCreate($width, $height) {
    $thumb = imagecreatetruecolor($width, $height);
    imagefill($thumb, 1, 1, imagecolorallocate($thumb, 255, 255, 255));
    imagealphablending($thumb, false);
    imagesavealpha($thumb, true);
    return $thumb;
  }
  
  function _resize($source, $resizeWidth, $resizeHeight) {
    list($width, $height) = $this->imageSize($source);
    
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
    
    $thumb  = $this->imageCreate(floor($newWidth), round($newHeight)); 
    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    imagedestroy($source);
    
    return $thumb;
  }
  
  function _crop($source, $cropWidth, $cropHeight) {
    list($width, $height) = $this->imageSize($source);
    
    $thumb = $this->imageCreate(floor($cropWidth), round($cropHeight)); 

    if ($cropWidth <= $width && $cropHeight <= $height) {
      $resizeWidth = $resizeHeight = false;  
      if ($width/$height < $cropWidth/$cropHeight) 
        $resizeWidth = $cropWidth;
      else 
        $resizeHeight = $cropHeight;  
        
      $source = $this->_resize($source, $resizeWidth, $resizeHeight);
      $width  = imagesx($source);
      $height = imagesy($source);
      
      $cropX = floor($width / 2)  - floor($cropWidth / 2);
      $cropY = floor($height / 2) - floor($cropHeight / 2);
      
      imagecopymerge($thumb , $source , 0 , 0 , $cropX , $cropY , $cropWidth , $cropHeight, 100);
    } else {
      
      if ($width < $cropWidth)  
        $cropX = $width/2;
      else
        $cropX = -$width/2;
      if ($height < $cropHeight)  
        $cropY = $height/2;
      else
        $cropY = 0;
        
      imagecopymerge($thumb , $source , $cropX , $cropY , 0 , 0 , $width , $height, 100);
    }

    imagedestroy($source);
    
    return $thumb;
  }
  
  function _cache($path, $width, $height) {
    $name   = basename($path);
    $folder = $this->cacheFolder . '/' . $width . 'x' . $height;    
    $cache  = $folder . '/' . $name;  
    return array($name, $cache, $folder);
  }
  
  function proccess($method, $options) {    
    if (is_array($path))
      extract($path);
    elseif (is_object($path)) {
      $path = $path->path;
    }
      
    extract($options);

    $ROOT = ROOT;
    
    list($name, $cache, $folder) = $this->_cache($path, $width, $height);
    if (! file_exists($ROOT . $cache)) {
      $type   = $this->imageType($path);
      $source = $this->loadImage($ROOT . $path, $type);
      if (! $source) 
        return '';
        
      $thumb  = $this->{$method}($source, $width, $height);
    
      if (! file_exists($ROOT . $this->cacheFolder))
        mkdir($ROOT . $this->cacheFolder, 0777);
      if (! file_exists($ROOT . $folder))
        mkdir($ROOT . $folder, 0777);
      
      $this->putImage($thumb, $type, $ROOT . $cache);
      
      $this->writeRegister(array(
        'method'   => $method,
        'image'    => $name,
        'original' => $path,
        'cache'    => $cache,        
        'sizes'    => $width . 'x' . $height                
      ));
    }
    
    return $cache;
  }
  
  function resize($path, $width, $height) {
    return $this->proccess('_resize', array('path' => $path, 'width' => $width, 'height' => $height));
  }
  
  function crop($path, $width, $height) {
    return $this->proccess('_crop', array('path' => $path, 'width' => $width, 'height' => $height));
  }
  
  function strongImage($path, $attr = array()) {

    $ROOT = ROOT;

    $sizes = getimagesize($ROOT . $path); 
    $attr['data-width']  = $sizes[0];
    $attr['data-height'] = $sizes[1];
    return $this->output(sprintf($this->tags['image'], $path, $this->_parseAttributes($attr, null, ' ', '')));
  }
  
}

?>