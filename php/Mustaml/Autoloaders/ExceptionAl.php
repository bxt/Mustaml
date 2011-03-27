<?php
namespace Mustaml\Autoloaders;

/**
 * "Autoloader" that throws Eceptions when using undefined vars in templates
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
