<?php
namespace Mustaml\Util;

class Object2Map {
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