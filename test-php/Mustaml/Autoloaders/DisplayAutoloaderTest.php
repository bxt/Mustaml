<?php
namespace Mustaml\Autoloaders;
require_once 'mustaml.php';

class DisplayAutoloaderTest extends \PHPUnit_Framework_TestCase {
	public function testDisplayingValues() {
		
		$al=new DisplayAutoloader();
		$config=new \Mustaml\HtmlCompilerConfig(array(array($al,'autoload')));
		$main=new \Mustaml\Mustaml("%p =foo\n%p =bar",array("bar"=>"set"),$config);
		
		$this->assertEquals('<p>{{foo}}</p><p>set</p>',$main());
	}
	public function testCustomDelims() {
		
		$al=new DisplayAutoloader('-=|','|=-');
		$config=new \Mustaml\HtmlCompilerConfig(array(array($al,'autoload')));
		$main=new \Mustaml\Mustaml("%p =foo",array(),$config);
		
		$this->assertEquals('<p>-=|foo|=-</p>',$main());
	}
	public function testDisplayingBlockcode() {
		
		$al=new DisplayAutoloader();
		$config=new \Mustaml\HtmlCompilerConfig(array(array($al,'autoload')));
		$main=new \Mustaml\Mustaml("%p -foo\n  %p -bar",array("bar"=>"set"),$config);
		
		$this->assertEquals('<p>{{foo}}<p>set</p></p>',$main());
	}
}
?>