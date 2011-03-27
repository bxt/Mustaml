<?php
namespace Mustaml\Autoloaders;

/**
 * "Autoloader" that displays the used varnames in the template
 */
class DisplayAl implements AutoloaderI {
	private $openingDelim;
	private $closingDelim;
	/**
	 * Initialize optionally specifying custom marks to surrond the varname with
	 */
	public function __construct($openingDelim=null,$closingDelim=null) {
		$this->openingDelim=$openingDelim?:'{{';
		$this->closingDelim=$closingDelim?:'}}';
	}
	/**
	 * Returns a string conatining the surrounded varname
	 */
	public function autoload($key) {
		$string=$this->openingDelim.$key.$this->closingDelim;
		return $string;
	}
}
