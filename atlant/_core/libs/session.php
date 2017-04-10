<?php

class Session {
  static function start($options = array()) {
    extract($options);
    if (isset($name))
      session_name($name);
    if (isset($id))
      session_id($id);
    @session_start();
    
    $_SESSION['ping'] = time();
  }
  
  static function check($param = null) {
    if (isset($_SESSION[$param])) {
      return true;
    }
    
    return false;
  }
  
  static function read($param = null) {
    return get_array_value($_SESSION, $param);
  }
  
  static function write($param = null, $value) {
    put_array_value($_SESSION, $param, $value);
  }
  
  static function del($param = null) {
    del_array_value($_SESSION, $param);
  }
}

class Cookie {
  
  static function check() {
    
  }
  
  static function read() {
    
  }
  
  static function write() {
    
  }
  
  static function del() {
    
  }
}

?>