<?php
namespace Mustaml\Autoloaders;
require_once 'mustaml.php';

class DisplayAlTest extends \PHPUnit_Framework_TestCase {
	public function testDisplayingValues() {
		
		$al=new DisplayAl();
		$config=new \Mustaml\Html\CompilerConfig(array(array($al,'autoload')));
		$main=new \Mustaml\Mustaml("%p =foo\n%p =bar",array("bar"=>"set"),$config);
		
		$this->assertEquals('<p>{{foo}}</p><p>set</p>',$main());
	}
	public function testCustomDelims() {
		
		$al=new DisplayAl('-=|','|=-');
		$config=new \Mustaml\Html\CompilerConfig(array(array($al,'autoload')));
		$main=new \Mustaml\Mustaml("%p =foo",array(),$config);
		
		$this->assertEquals('<p>-=|foo|=-</p>',$main());
	}
	public function testDisplayingBlockcode() {
		
		$al=new DisplayAl();
		$config=new \Mustaml\Html\CompilerConfig(array(array($al,'autoload')));
		$main=new \Mustaml\Mustaml("%p -foo\n  -.\n  %p -bar",array("bar"=>"set"),$config);
		
		$this->assertEquals('<p>{{foo}}<p>set</p></p>',$main());
	}
}
?>