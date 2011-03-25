<?php
namespace Mustaml\Autoloaders;
require_once 'mustaml.php';

class ExceptionAutoloaderTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @expectedException Mustaml\Autoloaders\ExceptionAutoloaderVarNotFoundException
	 */
	public function testThrowingException() {
		$al=new ExceptionAutoloader();
		$config=new \Mustaml\HtmlCompilerConfig(array(array($al,'autoload')));
		$main=new \Mustaml\Mustaml("%p =foo\n%p =bar",array("bar"=>"set"),$config);
		$main();
	}
	public function testNotThrowingException() {
		
		$al=new ExceptionAutoloader();
		$config=new \Mustaml\HtmlCompilerConfig(array(array($al,'autoload')));
		$main=new \Mustaml\Mustaml("%p =foo",array("foo"=>"set"),$config);
		
		$this->assertEquals('<p>set</p>',$main());
	}
}
?>