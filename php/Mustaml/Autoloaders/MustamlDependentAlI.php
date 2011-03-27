<?php
namespace Mustaml\Autoloaders;

/**
 * Interface for objects that are able to dynamicly load data
 */
interface MustamlDependentAlI {
	/**
	 * This method has to be invoked with a mustaml class which can be used for further rendering
	 */
	public function setMustamlBoilerplate($mustamlBoilerplate);
}
