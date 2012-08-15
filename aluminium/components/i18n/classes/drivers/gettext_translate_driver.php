<?php
/**
 * This file contains the GettextTranslateDriver class.
 *
 * @author		Aitor García <aitor.falc@gmail.com>
 * @copyright	2012 Aitor García <aitor.falc@gmail.com>
 * @license		https://github.com/Falc/Aluminium/blob/master/LICENSE Simplified BSD License
 * @package		Aluminium
 * @subpackage	Components
 */

/**
 * Gettext Translate Driver.
 *
 * This driver allows to get strings translated by using gettext.
 *
 * @package		Aluminium
 * @subpackage	Components
 */
class GettextTranslateDriver extends TranslateDriver {
	/**
	 * Loads a single language file.
	 *
	 * The $file_name must match an existing .po or .mo file (without the extension).
	 *
	 * @param	string	$file_name	Name of the language file.
	 */
	public function load_file($file_name) {
		$full_file_name = $this->lang_path.'/'.$this->locale.'/LC_MESSAGES/'.$file_name;

		// If there is not any translation file (neither the .po nor the .mo), stop the process
		if(!file_exists($full_file_name.'.mo') && !file_exists($full_file_name.'.po')) {
			trigger_error('Neither "'.$file_name.'.po" nor "'.$file_name.'.mo" exist or can be loaded.', E_USER_ERROR);
		}

		if(!in_array($file_name, $this->source)) {
			bindtextdomain($file_name, $this->lang_path);
			$this->source[] = $file_name;
		}
	}

	/**
	 * Loads all the language files found in the locale directory.
	 */
	public function load_all_files() {
		if($dir_handle = opendir($this->lang_path.'/'.$this->locale.'/LC_MESSAGES/')) {
			// Run load_file() for every .po or .mo file
			while(($file = readdir($dir_handle)) !== FALSE) {
				$ext = pathinfo($file, PATHINFO_EXTENSION);
				if(in_array($ext, array('po', 'mo'))) {
					$file = substr($file, 0, -3);
					$this->load_file($file);
				}
			}
			closedir($dir_handle);
		}
	}

	/**
	 * Returns the translated string for $text.
	 *
	 * @param	string	$text	Text to translate.
	 */
	public function t($text) {
		$translated_string = $text;

		// Look for the $text in every $textdomain specified in $this->source
		foreach($this->source as $textdomain) {
			textdomain($textdomain);
			$translated_string = _($text);

			// If the $translated_string changed, the translated string was found
			if($translated_string != $text) {
				break;
			}
		}

		return $translated_string;
	}

}

?>
