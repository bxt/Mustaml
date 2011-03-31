<?php
namespace Mustaml\Util;
require_once 'mustaml.php';

class Testclass {
	public $baz="objproperty";
	public $foo;
	public function __construct($f='') {
		$this->foo=$f;
	}
	public function getFoo() {
		return array($this->foo,"gettermethod");
	}
	public function getBar() {
		return "barcnt";
	}
	public function getXXX($a) {}
}

class Object2MapTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @dataProvider feedValues
	 */
	public function testGetterAccess($x) {
		$object=new Testclass($x);
		$map=Object2Map::convert($object);
		
		$this->assertTrue(isset($map['foo']));
		$this->assertTrue(isset($map['bar']));
		$this->assertEquals(array($x,"gettermethod"),$map['foo']);
		$this->assertEquals('barcnt',$map['bar']);
	}
	public function testPropertyAccess() {
		$object=new Testclass();
		$map=Object2Map::convert($object);
		
		$this->assertTrue(isset($map['baz']));
		$this->assertEquals("objproperty",$map['baz']);
	}
	function feedValues() {
		return array(
			array('string value'),
			array(new \stdClass()),
			array(12),
			array(false),
			array(true),
			array(array('a','b')),
		);
	}
}
?>