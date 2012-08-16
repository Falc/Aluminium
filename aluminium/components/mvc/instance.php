<?php

// Load init.php
require(dirname(__FILE__).'/init.php');

// Namespace import
use Aluminium\Component\MVC\MVC;

// Return an MVC instance
return new MVC();

?>
