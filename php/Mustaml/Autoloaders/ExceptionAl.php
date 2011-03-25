<?php
namespace Mustaml\Autoloaders;

class ExceptionAl implements AutoloaderI {
	public function autoload($key) {
		throw new ExceptionAlVarNotFoundException("Tha data entry named $key could not be found. ");
	}
}
