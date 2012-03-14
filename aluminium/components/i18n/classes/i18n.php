<?php
/**
 * This file contains the I18n component class.
 *
 * @author		Aitor García <aitor.falc@gmail.com>
 * @copyright	2012 Aitor García <aitor.falc@gmail.com>
 * @license		https://github.com/Falc/Aluminium/blob/master/LICENSE Simplified BSD License
 * @package		Aluminium
 * @subpackage	Components
 */

/**
 * The I18n component allows to add multilingual support.
 *
 * @package		Aluminium
 * @subpackage	Components
 */
class I18n {
	/**
	 * Translate driver name.
	 *
	 * @var string
	 */
	public $driver_name;

	/**
	 * Locale name.
	 *
	 * The locale name format should be like 'll_CC', where 'll' is an ISO 639 two-letter code and 'CC' is an ISO 3166 country code.
	 *
	 * @var string
	 */
	protected $locale;

	/**
	 * Codeset (character encoding scheme).
	 *
	 * The default one is 'UTF-8'. You should check it is supported by the system and the selected locale.
	 *
	 * @var string
	 */
	protected $codeset;

	/**
	 * I18n constructor.
	 *
	 * Sets the configuration option from $conf_file, if specified.
	 *
	 * @param	string	$conf_file	Name of the configuration file.
	 */
	public function __construct($conf_file = null) {
		// Default values
		$this->driver_name = null;
		$this->locale = 'en_US';
		$this->codeset = 'UTF-8';

		// Load the configuration file, if any
		if(!is_null($conf_file)) {
			$this->set_configuration_from_file($conf_file);
		}

		// [Debug log]
		if(function_exists('write_debug_log')) {
			write_debug_log('I18n instance created successfully.', 'database');
		}
	}

	/**
	 * Sets all the properties from a configuration file.
	 *
	 * @param	string	$conf_file	Name of the configuration file.
	 */
	public function set_configuration_from_file($conf_file) {
		// Load the configuration file
		$conf = require($conf_file);

		// Set the driver, if defined
		if(isset($conf['driver'])) {
			$this->driver_name = $conf['driver'];
		}

		// Set the locale, if defined
		if(isset($conf['locale'])) {
			$this->locale = $conf['locale'];
		}

		// Set the codeset, if defined
		if(isset($conf['codeset'])) {
			$this->codeset = $conf['codeset'];
		}

		$this->apply_locale();
	}

	/**
	 * Sets the locale name.
	 *
	 * The method apply_local() is called automatically.
	 *
	 * @param	string	$locale	Locale name.
	 */
	public function set_locale($locale) {
		$this->locale = $locale;
		$this->apply_locale();
	}

	/**
	 * Sets the codeset.
	 *
	 * The method apply_local() is called automatically.
	 *
	 * @param	string	$codeset	Codeset.
	 */
	public function set_codeset($codeset) {
		$this->codeset = $codeset;
		$this->apply_locale();
	}

	/**
	 * Applies the locale changes.
	 */
	public function apply_locale() {
		list($language, $country) = explode('_', $this->locale);

		// The locale format depends on the platform, so it is better to try different formats
		$locales = array(
			$this->locale.'.'.$this->codeset,						// en_US.UTF-8
			$this->locale.'.'.str_replace('-', '', $this->codeset),	// en_US.UTF8
			$language.'.'.$this->codeset,						  	// en.UTF-8
			$language.'.'.str_replace('-', '', $this->codeset),		// en.UTF8
			$this->locale,											// en_US
			$language,												// en
		);

		foreach($locales as $locale) {
			$success = setlocale(LC_ALL, $locale);

/* Uncomment this for locale testing
			echo 'setlocale(LC_ALL, '.$locale.') => ';
			echo ($success === FALSE) ? 'failed!' : 'worked!';
			echo '<br />';
*/

			if($success != FALSE) {
				return;
			}
		}

		die('Error: It was not possible to set the locale information from locale "'.$this->locale.'" and codeset "'.$this->codeset.'".');
			
	}

	/**
	 * Loads a TranslateDriver instance.
	 *
	 * It can load the TranslateDriver from $driver_name. When a $driver_name is not passed as parameter,
	 * the method will rely on $this->driver_name.
	 *
	 * @param	string	Name of the driver to instance.
	 */
	public function load_driver($driver_name = null) {
		// Overwrite $this->driver_name if a driver name has been specified
		if(!is_null($driver_name)) {
			$this->driver_name = $driver_name;
		}

		// If driver name is null or empty, stop the process
		if(is_null($this->driver_name) || empty($this->driver_name)) {
			die('Error: No TranslateDriver was defined.');
		}

		// If the specified driver file does not exist, stop the process
		$driver_file = dirname(__FILE__).'/drivers/'.$driver_name.'_translate_driver.php';
		if(!file_exists($driver_file)) {
			die('Error: File '.$driver_file.' does not exist or cannot be loaded.');
		}

		// Include the driver class file
		require_once($driver_file);

		// Create the driver instance
		$driver_class = $driver_name.'TranslateDriver';
		return new $driver_class($this->locale);
	}

}

?>
