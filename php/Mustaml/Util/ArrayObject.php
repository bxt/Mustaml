<?php
namespace Mustaml\Util;

class ArrayObject implements \ArrayAccess, \Iterator, \Countable {
	private $object;
	private $iterator=0;
	private $offsetMethodMap=array();
	private $offsetMethodMapSetters=array();
	public function __construct($obj) {
		$this->object=$obj;
		$rc=new \ReflectionClass($obj);
		$meths=$rc->getMethods(\ReflectionMethod::IS_PUBLIC);
		foreach($meths as $m) {
			$n=$m->getShortName();
			$n2=lcfirst(substr($n,3));
			if(strpos($n,'get')===0 && $m->getNumberOfRequiredParameters()==0) {
				$this->offsetMethodMap[$n2]=$m;
			}
			if(strpos($n,'set')===0 && $m->getNumberOfRequiredParameters()==1) {
				$this->offsetMethodMapSetters[$n2]=$m;
			}
		}
	}
	public function offsetSet($offset,$value) {
		if(isset($this->offsetMethodMapSetters[$offset]))
			return $this->offsetMethodMapSetters[$offset]->invoke($this->object,$value);
		throw new \OutOfRangeException("Tried to write an attribute without appropriate setter. ");
	}
	public function offsetExists($offset) {
		return isset($this->offsetMethodMap[$offset]);
	}
	public function offsetUnset($offset) {
		$this->offsetSet($offset,null);
	}
	public function offsetGet($offset) {
		if(!$this->offsetExists($offset))
			throw new \OutOfRangeException("Tried to read an attribute without appropriate getter. ");
		return $this->offsetMethodMap[$offset]->invoke($this->object);
	}
	public function rewind() {
			$this->iterator=0;
	}
	public function current() {
		$i=0;
		foreach($this->offsetMethodMap as $key=>$val) {
			if($i==$this->iterator) return $this->offsetGet($key);
			$i++;
		}
		return false;
	}
	public function key() {
		$i=0;
		foreach($this->offsetMethodMap as $key=>$val) {
			if($i==$this->iterator) return $key;
			$i++;
		}
	}
	public function next() {
		$this->iterator++;
		$i=0;
		foreach($this->offsetMethodMap as $key=>$val) {
			if($i==$this->iterator) return $this->offsetGet($key);
			$i++;
		}
	}
	public function valid() {
		return $this->current() !== false;
	}
	public function count() {
	return count($this->offsetMethodMap);
	}

}