<?php

// Load init.php
require(dirname(__FILE__).'/init.php');

// Return a DatabaseDriver instance
$database = new Database(APP_CONF.'database_conf.php');
return $database->load_driver();

?>
