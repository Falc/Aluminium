<?php

// Load init.php
require(dirname(__FILE__).'/init.php');

// Return a Router instance
return new Router(APP_CONF.'routes_conf.php');

?>
