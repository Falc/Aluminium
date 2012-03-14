<?php
/**
 * This file contains the TranslateDriver class.
 *
 * @author		Aitor García <aitor.falc@gmail.com>
 * @copyright	2012 Aitor García <aitor.falc@gmail.com>
 * @license		https://github.com/Falc/Aluminium/blob/master/LICENSE Simplified BSD License
 * @package		Aluminium
 * @subpackage	Components
 */

/**
 * The parent class for TranslateDrivers.
 *
 * A TranslateDriver allows to use a specific translation "process" or library.
 *
 * @package		Aluminium
 * @subpackage	Components
 */
abstract class TranslateDriver {
	/**
	 * The "source" for the translation.
	 *
	 * It is an array, but its content depends on the way the driver works. Some drivers could use it
	 * as a list of source and target strings, others could use it as a list of files.
	 *
	 * The method t() should use it to get a translated string.
	 *
	 * @var array
	 */
	protected $source;

	/**
	 * The locale name.
	 *
	 * @var string
	 */
	protected $locale;

	/**
	 * TranslateDriver constructor.
	 *
	 * Loads the required configuration and checks it.
	 *
	 * @param	string	$locale	Locale name.
	 */
	public function __construct($locale) {
		$this->locale = $locale;
		$this->source = array();
	}

	/**
	 * Loads a single language file.
	 *
	 * @param	string	$file_name	Name of the language file.
	 */
	abstract public function load_file($file_name);

	/**
	 * Loads all the language files found in the locale directory.
	 */
	abstract public function load_all_files();

	/**
	 * Returns the translated string for $text.
	 *
	 * @param	string	$text	Text to translate.
	 */
	abstract public function t($text);

}

?>
