<?php

/*
 * Required files
 */
require_once(dirname(__FILE__).'/classes/router.php');
require_once(dirname(__FILE__).'/classes/route.php');

/*
 * Constant definitions
 */


/*
 * Return a Router instance
 */
return new Router(APP_CONF.'routes_conf.php');

?>
