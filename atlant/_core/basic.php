<?php

if (isset($_GET['extra_debug'])){
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(-1);
} else {
	error_reporting(0);
}
  
if (!defined('TMP')) 
	define('TMP', ROOT . DS . '_tmp' . DS);
  
if (!defined('CACHE')) 
	define('CACHE', TMP . '_cache' . DS);

if (!defined('LIBS')) 
	define('LIBS', ROOT . DS . '_core' . DS . 'libs' . DS);

if (!defined('MODS')) 
	define('MODS', ROOT . DS . '_modules' . DS);
  
include  LIBS . 'functions.php';
include  LIBS . 'configure.php';
include  MODS . 'config.php'; 
include  LIBS . 'router.php';
include  LIBS . 'dispatcher.php';
include  LIBS . 'session.php';
include  LIBS . 'mail.php';
include  LIBS . 'db' . DS . 'dbo.php';
include  LIBS . 'helper.php';
include  LIBS . 'model.php';
include  LIBS . 'view.php';
include  LIBS . 'controller.php';

include  MODS . 'admin' . DS . 'config.php';
include  MODS . 'client' . DS . 'config.php';

?>