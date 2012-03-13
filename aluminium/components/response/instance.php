<?php

// Load init.php
require(dirname(__FILE__).'/init.php');

// Return a Response instance
return new Response(new Headers(), new Cookies());

?>
