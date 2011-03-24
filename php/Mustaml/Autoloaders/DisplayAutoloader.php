<?php
namespace Mustaml\Autoloaders;

class DisplayAutoloader {
	private $openingDelim;
	private $closingDelim;
	public function __construct($openingDelim=null,$closingDelim=null) {
		$this->openingDelim=$openingDelim?:'{{';
		$this->closingDelim=$closingDelim?:'}}';
	}
	public function autoload($key) {
		$string=$this->openingDelim.$key.$this->closingDelim;
		return $string;
	}
}
