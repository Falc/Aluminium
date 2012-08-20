<?php
/**
 * This file contains the ArrayTranslateDriver class.
 *
 * @author		Aitor García <aitor.falc@gmail.com>
 * @copyright	2012 Aitor García <aitor.falc@gmail.com>
 * @license		https://github.com/Falc/Aluminium/blob/master/LICENSE Simplified BSD License
 */

namespace Aluminium\Component\I18n\Driver;

/**
 * Array Translate Driver.
 *
 * This driver allows to get strings translated by using associative arrays.
 */
class ArrayTranslateDriver extends TranslateDriver {

	/**
	 * Loads a single language file.
	 *
	 * The $file_name must match an existing PHP file that simply returns an associative array containing the source and target strings.
	 *
	 * @param	string	$file_name	Name of the language file.
	 */
	public function load_file($file_name) {
		$file_name = $this->lang_path.'/'.$this->locale.'/'.$file_name.'.php';

		// If the file does not exist, stop the process
		if(!file_exists($file_name)) {
			trigger_error('File "'.$file_name.'" does not exist or cannot be loaded.', E_USER_ERROR);
		}

		$file_content = include($file_name);
		$this->source = array_merge($this->source, $file_content);
	}

	/**
	 * Loads all the language files found in the locale directory.
	 */
	public function load_all_files() {
		if($dir_handle = opendir($this->lang_path.'/'.$this->locale)) {
			// Run load_file() for every .php file
			while(($file = readdir($dir_handle)) !== FALSE) {
				$ext = pathinfo($file, PATHINFO_EXTENSION);
				if($ext === 'php') {
					$file = substr($file, 0, -4);
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
		if(!empty($this->source[$text])) {
			return $this->source[$text];
		}
		else {
			return $text;
		}
	}

}
?>
