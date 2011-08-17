<?php
namespace Mustaml\Compiler;

/**
 * Holds and manages options for parsing AST
 */
class CompilerConfig {
	/**
	 * Holds a ordered list of autoloaders
	 * @var array
	 */
	private $valueAutoloaders;
	/**
	 * Caches already autoloaded values
	 * @var array
	 */
	private $autoloadedValues=array();
	/**
	 * Holds the boilerplate
	 * @var \Mustaml\Mustaml
	 */
	private $mustamlBoilerplate=false;
	/**
	 * Return if or not a varname is autoloadable
	 * 
	 * Checks all autoloaders and performs their
	 * autoload() functios
	 */
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
	/**
	 * Returns the autoloadable value of a varnames
	 */
	public function getAutoloadable($key) {
		if(!$this->isAutoloadable($key)) throw new \OutOfRangeException("Tried to autoload a not-autoloadable key. ");
		return $this->autoloadedValues[$key];
	}
	/**
	 * Add to the list of autoloaders
	 *
	 * Autoloaders are objects implementing 
	 * Mustaml\\Autoloaders\\AutoloaderI or
	 * callbacks that return a value given
	 * a key or null. 
	 * If multiple autoloaders can deliver
	 * a value the last one in list is used. 
	 */
	public function registerAutoloader($al) {
		$this->valueAutoloaders[]=$al;
		if(interface_exists('Mustaml\\Autoloaders\\MustamlDependentAlI',false) && ($al instanceof \Mustaml\Autoloaders\MustamlDependentAlI) ) {
			if(!$this->mustamlBoilerplate) {
				$this->mustamlBoilerplate=new \Mustaml\Mustaml('',array(),$this);
			}
			$al->setMustamlBoilerplate($this->mustamlBoilerplate);
		}
	}
}
