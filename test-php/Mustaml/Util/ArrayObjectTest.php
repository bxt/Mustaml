<?php
namespace Mustaml\Util;
require_once 'mustaml.php';

class Testclass {
	function __construct($f='') {
		$this->foo=$f;
	}
	function getFoo() {
		return $this->foo;
	}
	function getBar() {
		return "barcnt";
	}
	function getXXX($a) {}
	function setFoo($f) {
		$this->foo=$f;
	}
	function setXXX() {}
}

class ArrayObjectTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @dataProvider feedValues
	 */
	public function testSimpleGet($x) {
		$t=new Testclass($x);
		$o=new ArrayObject($t);
		
		$this->assertEquals($x,$o['foo']);
		$this->assertEquals('barcnt',$o['bar']);
	}
	/**
	 * @dataProvider feedValues
	 */
	public function testSimpleSet($x) {
		$t=new Testclass("foocnt");
		$o=new ArrayObject($t);
		$this->assertEquals('foocnt',$o['foo']);
		$o['foo']=$x;
		$this->assertEquals($x,$o['foo']);
	}
	public function testIsset() {
		$t=new Testclass("foocnt");
		$o=new ArrayObject($t);
		$this->assertTrue(isset($o['foo']));
		$this->assertTrue(isset($o['bar']));
		$this->assertFalse(isset($o['XXX']));
	}
	/**
	 * @expectedException \OutOfRangeException
	 */
	public function testSetFail() {
		$t=new Testclass();
		$o=new ArrayObject($t);
		$this->assertEquals('barcnt',$o['bar']);
		$o['bar']='newbarcnt';
	}
	/**
	 * @expectedException \OutOfRangeException
	 */
	public function testGetFail() {
		$t=new Testclass();
		$o=new ArrayObject($t);
		$this->assertEquals('barcnt',$o['XXX']);
	}
	public function testCount() {
		$t=new Testclass();
		$o=new ArrayObject($t);
		$this->assertEquals(2,count($o));
	}
	/**
	 * @dataProvider feedValues
	 */
	public function testForeach($x) {
		$t=new Testclass($x);
		$o=new ArrayObject($t);
		$keys=array('foo','bar');
		$vals=array($x,'barcnt');
		$i=0;
		foreach($o as $key=>$val) {
			$this->assertEquals($keys[$i],$key);
			$this->assertEquals($vals[$i],$val);
			$i++;
		}
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