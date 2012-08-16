<?php

// Load init.php
require(dirname(__FILE__).'/init.php');

// Namespace import
use Aluminium\Component\Database\Database;

// Return a DatabaseDriver instance
return new Database();

?>
