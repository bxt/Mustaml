<?php
namespace Mustaml\Compiler;

class CompilerConfig {
	private $valueAutoloaders;
	private $autoloadedValues=array();
	public function __construct($valueAutoloaders=array()) {
		$this->valueAutoloaders=$valueAutoloaders;
	}
	public function isAutoloadable($key) {
		if(isset($this->autoloadedValues[$key])) return true;
		for($i=count($this->valueAutoloaders)-1;$i>=0;$i--) {
			$al=$this->valueAutoloaders[$i];
			$loaded=null;
			if(interface_exists('Mustaml\\Autoloaders\\AutoloaderI',false) && ($al instanceof \Mustaml\Autoloaders\AutoloaderI) ) {
				$loaded=call_user_func_array(array($al,'autoload'),array($key));
			} elseif(is_callable($al)) {
				$loaded=call_user_func_array($al,array($key));
			}
			if($loaded!==null) {
				$this->autoloadedValues[$key]=$loaded;
				return true;
			}
		}
		return false;
	}
	public function getAutoloadable($key) {
		if(!$this->isAutoloadable($key)) throw new \OutOfRangeException("Tried to autoload an not-autoloadable key. ");
		return $this->autoloadedValues[$key];
	}
}
