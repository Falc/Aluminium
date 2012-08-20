<?php
/**
 * This file contains the I18n component class.
 *
 * @author		Aitor García <aitor.falc@gmail.com>
 * @copyright	2012 Aitor García <aitor.falc@gmail.com>
 * @license		https://github.com/Falc/Aluminium/blob/master/LICENSE Simplified BSD License
 */

namespace Aluminium\Component\I18n;

/**
 * The I18n component allows to add multilingual support.
 */
class I18n {

	/**
	 * Translate driver name.
	 *
	 * @var string
	 */
	protected $driver_name;

	/**
	 * Lang path.
	 *
	 * @var string
	 */
	protected $lang_path;

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
	 * Sets the configuration option from $conf, if specified.
	 *
	 * @param	string	$conf	An array containing configuration options.
	 */
	public function __construct($conf = null) {
		// Default values
		$this->driver_name = null;
		$this->lang_path = null;
		$this->locale = 'en_US';
		$this->codeset = 'UTF-8';

		// Load the configuration file, if any
		if(!empty($conf)) {
			$this->load_configuration($conf);
		}
	}

	/**
	 * Gets the driver name.
	 *
	 * @return string
	 */
	public function get_driver_name() {
		return $this->driver_name;
	}

	/**
	 * Sets the driver name.
	 *
	 * @param	string	$driver_name	Driver name.
	 */
	public function set_driver_name($driver_name) {
		$this->driver_name = $driver_name;
	}

	/**
	 * Gets the lang path.
	 *
	 * @return string
	 */
	public function get_lang_path() {
		return $this->lang_path;
	}

	/**
	 * Sets the lang path.
	 *
	 * @param	string	$lang_path	Lang path.
	 */
	public function set_lang_path($lang_path) {
		// If the directory does not exist or cannot be accessed, stop the process
		if(!is_dir($lang_path)) {
			trigger_error('Directory "'.$lang_path.'" does not exist or cannot be accessed.', E_USER_ERROR);
		}

		$this->lang_path = $lang_path;
	}

	/**
	 * Gets the locale name.
	 *
	 * @return string
	 */
	public function get_locale() {
		return $this->locale;
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
	 * Gets the codeset name.
	 *
	 * @return string
	 */
	public function get_codeset() {
		return $this->codeset;
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
	 * Sets properties from an array.
	 *
	 * @param	array	$conf	An array containing some configuration options.
	 */
	public function load_configuration($conf) {
		// Set the driver, if defined
		if(!empty($conf['driver'])) {
			$this->set_driver_name($conf['driver']);
		}

		// Set the lang path, if defined
		if(!empty($conf['lang_path'])) {
			$this->set_lang_path($conf['lang_path']);
		}

		// Set the locale, if defined
		if(!empty($conf['locale'])) {
			$this->set_locale($conf['locale']);
		}

		// Set the codeset, if defined
		if(!empty($conf['codeset'])) {
			$this->set_codeset($conf['codeset']);
		}

		$this->apply_locale();
	}

	/**
	 * Sets properties from a configuration file.
	 *
	 * @param	string	$conf_file	Name of the configuration file.
	 */
	public function load_configuration_from_file($conf_file) {
		// Load the configuration file
		$conf = require($conf_file);

		$this->load_configuration($conf);
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

			if($success !== FALSE) {
				return;
			}
		}

		// If no locale format worked, stop the process
		trigger_error('Locale "'.$this->locale.'.'.$this->codeset.'" was not found.', E_USER_ERROR);
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

		// If driver name is empty, stop the process
		if(empty($this->driver_name)) {
			trigger_error('No TranslateDriver was defined.', E_USER_ERROR);
		}

		// If the lang path is empty, stop the process
		if(empty($this->lang_path)) {
			trigger_error('No lang path was defined.', E_USER_ERROR);
		}

		// If the specified driver file does not exist, stop the process
		$driver_file = dirname(__FILE__).'/drivers/'.$this->driver_name.'_translate_driver.php';
		if(!file_exists($driver_file)) {
			trigger_error('File '.$driver_file.' does not exist or cannot be loaded.', E_USER_ERROR);
		}

		// Include the driver class file
		require_once($driver_file);

		// Create the driver instance
		$driver_class = $this->driver_name.'TranslateDriver';
		return new $driver_class($this->lang_path, $this->locale);
	}

}
?>
