<?php
/**
 * This file contains the TranslateDriver class.
 *
 * @author		Aitor García <aitor.falc@gmail.com>
 * @copyright	2012 Aitor García <aitor.falc@gmail.com>
 * @license		https://github.com/Falc/Aluminium/blob/master/LICENSE Simplified BSD License
 */

namespace Aluminium\Component\I18n\Driver;

/**
 * The parent class for TranslateDrivers.
 *
 * A TranslateDriver allows to use a specific translation "process" or library.
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
	 * Lang path.
	 *
	 * @var string
	 */
	protected $lang_path;

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
	public function __construct($lang_path, $locale) {
		$this->source = array();
		$this->lang_path = $lang_path;
		$this->locale = $locale;
	}

	/**
	 * Gets the source.
	 *
	 * @return array
	 */
	public function get_source() {
		return $this->source;
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
	 * Gets the locale name.
	 *
	 * @return string
	 */
	public function get_locale() {
		return $this->locale;
	}

	/**
	 * Clears the source.
	 */
	public function clear_source() {
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
