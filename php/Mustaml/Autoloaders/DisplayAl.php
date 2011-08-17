<?php
namespace Mustaml\Autoloaders;

/**
 * "Autoloader" that displays the used varnames in the template
 *
 * Handy, if yout want to know which parameters would be 
 * autoloaded and where they are. 
 */
class DisplayAl implements AutoloaderI {
	/**
	 * Holds the string to display before the varname
	 * @var String
	 */
	private $openingDelim;
	/**
	 * Holds the string to display after the varname
	 * @var String
	 */
	private $closingDelim;
	/**
	 * Initialize optionally specifying custom marks to surrond the varname with
	 * @param String string to display before the varname
	 * @param String string to display after the varname
	 */
	public function __construct($openingDelim=null,$closingDelim=null) {
		$this->openingDelim=$openingDelim?:'{{';
		$this->closingDelim=$closingDelim?:'}}';
	}
	/**
	 * Returns a string conatining the surrounded varname
	 * @param String name of var to be autoloaded
	 * @retrun String the varname surrounded by {{ }} or specifyed strings
	 */
	public function autoload($key) {
		$string=$this->openingDelim.$key.$this->closingDelim;
		return $string;
	}
}
