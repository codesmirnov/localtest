<?php

if (!defined('DS')) 
	define('DS', DIRECTORY_SEPARATOR);

if (!defined('ROOT')) 
	define('ROOT', dirname(__FILE__));
  
  
if (!defined('CORE')) 
	define('CORE', ROOT . DS . '_core' . DS);
  
if (!include(CORE . 'basic.php'))
  trigger_error("Can't find core basic.php");
  
$Dispatcher = new Dispatcher();
$Dispatcher->dispatch(); 
     
?>
