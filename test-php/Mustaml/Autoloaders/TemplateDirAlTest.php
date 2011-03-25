<?php
namespace Mustaml\Autoloaders;
require_once 'mustaml.php';

class TemplateDirAlTest extends \PHPUnit_Framework_TestCase {
	public function testLoadingTwoNestedTemplate() {
		
		$data=array("love"=>"with love");
		$al=new TemplateDirAl('test-php/data');
		$config=new \Mustaml\Html\CompilerConfig(array(array($al,'autoload')));
		$main=new \Mustaml\Mustaml("-hornshee.mustaml",$data,$config);
		$al->setMustamlBoilerplate($main);
		
		$this->assertEquals('<html><div id="hello">from ahorn.mustaml with love<div id="hello">from banshee.mustaml<p class="bansheeblock"><span id="enbloc"></span></p></div></div></html>',$main());
	}
	public function testLoadingTwoNestedTemplateUsingInterfaceCallback() {
		
		$data=array("love"=>"with love");
		$al=new TemplateDirAl('test-php/data');
		$config=new \Mustaml\Html\CompilerConfig(array($al));
		$main=new \Mustaml\Mustaml("-hornshee.mustaml",$data,$config);
		$al->setMustamlBoilerplate($main);
		
		$this->assertEquals('<html><div id="hello">from ahorn.mustaml with love<div id="hello">from banshee.mustaml<p class="bansheeblock"><span id="enbloc"></span></p></div></div></html>',$main());
	}
}
?>