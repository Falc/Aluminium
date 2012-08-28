<?php

// Load init.php
require(dirname(__FILE__).'/init.php');

// Namespace import
use Aluminium\Component\Response\Cookies;
use Aluminium\Component\Response\Headers;
use Aluminium\Component\Response\Response;

// Return a Response instance
return new Response(new Headers(), new Cookies());

?>
