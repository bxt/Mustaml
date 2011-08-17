<?php
namespace Mustaml\Autoloaders;

/**
 * "Autoloader" that throws Eceptions when using undefined vars in templates
 *
 * Useful if you want to force that all vatiables are defined, or specally
 * handle the case of undefined variables. 
 */
class ExceptionAl implements AutoloaderI {
/**
 * Always thorws an ExceptionAlVarNotFoundException
 * @throws ExceptionAlVarNotFoundException
 */
	public function autoload($key) {
		throw new ExceptionAlVarNotFoundException("Tha data entry named $key could not be found. ");
	}
}
