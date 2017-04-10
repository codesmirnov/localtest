<?php

Router::from('/admin');
Router::module('admin');

Router::connect('/_map/*', array(
  'controller' => '_map',
  'className'  => 'Map',
  'action'     => 'index'
));

Router::connect('/_images/:field/:action/*', array(
  'controller' => '_images',
  'className'  => 'Images'
));

Router::connect('/_search/*', array(
  'controller' => 'admin',
  'action'     => 'search'
));

Router::connect('/test/*', array(
  'controller' => 'admin',
  'action'     => 'test'
));

Router::connect('/login', array(
  'controller' => 'admin',
  'action'     => 'login'
));

Router::connect('/logout', array(
  'controller' => 'admin',
  'action'     => 'logout'
));


Router::connect('/*', array(
  'controller' => 'admin',
  'action'     => 'dispatch'
));

Router::clear();

?>