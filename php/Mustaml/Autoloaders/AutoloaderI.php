<?php
namespace Mustaml\Autoloaders;

interface AutoloaderI {
	/**
	 * This method will be invoked for a undefined var. 
	 *
	 * Its single parameter is the varname, and it should return
	 * the vars value on success, and null otherwise. 
	 */
	public function autoload($key);
}
