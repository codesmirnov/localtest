<?

class Configure {
  
  var $config = array();
  
	static function &getInstance() {
		static $instance = array();

		if (!$instance) {
			$instance[0] =& new Configure();
		}
		return $instance[0];
	}
  
  static function read($param) {
		$_this =& Configure::getInstance();
    return get_array_value($_this->config, $param);
  }
  
  static function write($param, $value) {
		$_this =& Configure::getInstance();
    put_array_value($_this->config, $param, $value);    
  }
  
}

?>