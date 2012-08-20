<?php

// Load init.php
require(dirname(__FILE__).'/init.php');

// Namespace import
use Aluminium\Component\I18n\I18n;

// Return a I18n instance
return new I18n();

?>
