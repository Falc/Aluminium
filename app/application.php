<?php
/**
 * This file contains the Application class.
 */

namespace App;

use Aluminium;

/**
 * The main Application class.
 *
 * It must extend from Aluminium\Application.
 */
class Application extends Aluminium\Application {

	/**
	 * The configuration method is called before init() and should be used to set up the application.
	 *
	 * It is possible to load configuration options by using:
	 * - $this->set_conf_option('option' => 'value');
	 * - $this->set_conf_options(array('option1' => 'value1', 'option2' => 'value2', ...));
	 * - $this->set_conf_options_from_file(APP_CONF.'your_conf.php');
	 */
	protected function configuration() {
	}

	/**
	 * Inits the application.
	 *
	 * This is the main method, where you should load/instance components and do whatever you want.
	 */
	protected function init() {
	}

}
?>
