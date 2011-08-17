<?php
namespace Mustaml\Util;

/**
 * Static utility class, see mthods
 * @see convert()
 */
class Object2Map {
	/**
	 * Converts an object to a dictionary
	 * 
	 * Uses refelction to figure getter methods
	 * and invokes them, constructing the 
	 * map keys from their names. 
	 * Getter methods must start with get and
	 * have no required params. 
	 */
	public static function convert($obj) {
		$result=get_object_vars($obj);
		$rc=new \ReflectionClass($obj);
		$meths=$rc->getMethods(\ReflectionMethod::IS_PUBLIC);
		foreach($meths as $m) {
			$n=$m->getShortName();
			$n2=lcfirst(substr($n,3));
			if(strpos($n,'get')===0 && $m->getNumberOfRequiredParameters()==0) {
				$result[$n2]=$m->invoke($obj);
			}
		}
		return $result;
	}
}