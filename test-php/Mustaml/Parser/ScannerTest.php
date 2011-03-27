<?php
namespace Mustaml\Parser;
require_once 'mustaml.php';

class ScannerTest extends \PHPUnit_Framework_TestCase {
	public function testGetCharlist() {
		$s=new Scanner('abcdheffh');
		$this->assertEquals('abc',$s->get('bac'));
		$this->assertFalse($s->get('bac'));
		$this->assertEquals('dhe',$s->get('dhe'));
		$this->assertFalse($s->get('dhe'));
		$this->assertEquals('ffh',$s->get('fh'));
		$this->assertFalse($s->get('ffh'));
		$this->assertFalse($s->get());
	}
	public function testGetRegexlist() {
		$s=new Scanner('abcdheffh');
		$this->assertEquals('abc',$s->get('a-c'));
		$this->assertFalse($s->get('a-c'));
		$this->assertEquals('dhe',$s->get('a-eh'));
		$this->assertFalse($s->get('A-Z'));
		$this->assertEquals('ffh',$s->get('a-z'));
		$this->assertFalse($s->get('a-z'));
		$this->assertFalse($s->get());
	}
	public function testGetOneByOne() {
		$s=new Scanner('abcd');
		$this->assertEquals('a',$s->get());
		$this->assertEquals('b',$s->get());
		$this->assertEquals('c',$s->get());
		$this->assertEquals('d',$s->get());
		$this->assertFalse($s->get());
	}
	public function testGetOneByOneWithGetOne() {
		$s=new Scanner('abcd');
		$this->assertEquals('a',$s->getOne());
		$this->assertEquals('b',$s->getOne());
		$this->assertEquals('c',$s->getOne());
		$this->assertEquals('d',$s->getOne());
		$this->assertFalse($s->getOne());
	}
	public function testGetOne() {
		$s=new Scanner('aacbbd');
		$this->assertEquals('a',$s->getOne('bac'));
		$this->assertEquals('a',$s->getOne('bac'));
		$this->assertFalse($s->getOne('ba'));
		$this->assertEquals('c',$s->getOne('bac'));
		$this->assertEquals('b',$s->getOne('b'));
		$this->assertEquals('b',$s->getOne('db'));
		$this->assertEquals('d',$s->getOne('db'));
		$this->assertFalse($s->getOne('db'));
		$this->assertFalse($s->getOne());
	}
	public function testGetUnless() {
		$s=new Scanner('abcdheffh');
		$this->assertEquals('abc',$s->getUnless('hd'));
		$this->assertFalse($s->getUnless('hd'));
		$this->assertEquals('dhe',$s->getUnless('fo'));
		$this->assertEquals('ffh',$s->getUnless());
		$this->assertFalse($s->getOne());
		$this->assertEquals('',$s->getUnless());
	}
	public function testIs() {
		$s=new Scanner('abcdheffh');
		$this->assertEquals('a',$s->is('a'));
		$this->assertEquals('abc',$s->is('acb'));
		$this->assertEquals('abcd',$s->getUnless('h'));
		$this->assertFalse($s->is('a'));
		$this->assertFalse($s->is('acb'));
		$this->assertEquals('he',$s->is('eh'));
		$this->assertEquals('h',$s->getOne('ehx'));
		$this->assertEquals('e',$s->getOne('ehx'));
		$this->assertEquals('ffh',$s->is('[:lower:]'));
		$this->assertEquals('ff',$s->get('f'));
		$this->assertEquals('h',$s->is('[:alnum:]'));
		$this->assertEquals('h',$s->get());
		$this->assertFalse($s->is('acb'));
		$this->assertFalse($s->is());
	}
}
?>