<?php
namespace Mustaml\Autoloaders;
require_once 'mustaml.php';

class ExceptionAlTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @expectedException Mustaml\Autoloaders\ExceptionAlVarNotFoundException
	 */
	public function testThrowingException() {
		$al=new ExceptionAl();
		$config=new \Mustaml\Html\CompilerConfig(array(array($al,'autoload')));
		$main=new \Mustaml\Mustaml("%p =foo\n%p =bar",array("bar"=>"set"),$config);
		$main();
	}
	public function testNotThrowingException() {
		
		$al=new ExceptionAl();
		$config=new \Mustaml\Html\CompilerConfig(array(array($al,'autoload')));
		$main=new \Mustaml\Mustaml("%p =foo",array("foo"=>"set"),$config);
		
		$this->assertEquals('<p>set</p>',$main());
	}
}
?>