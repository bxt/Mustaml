<?php
namespace Mustaml\Autoloaders;

/**
 * Interface for objects that are able to dynamicly load data and
 * render in a given Mustaml context
 */
interface MustamlDependentAlI {
	/**
	 * This method has to be invoked with a mustaml class which can be used for further rendering
	 *
	 * @param \Mustaml\Mustaml Object able to replicate itself for further usage
	 */
	public function setMustamlBoilerplate($mustamlBoilerplate);
}
