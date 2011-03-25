<?php
namespace Mustaml\Autoloaders;

class ExceptionAutoloader implements AutoloaderI {
	public function autoload($key) {
		throw new ExceptionAutoloaderVarNotFoundException("Tha data entry named $key could not be found. ");
	}
}
