<?php
namespace Mustaml\Autoloaders;
require_once 'mustaml.php';

class TemplateDirAlTest extends \PHPUnit_Framework_TestCase {
	public function testLoadingTwoNestedTemplate() {
		
		$data=array("love"=>"with love");
		$config=new \Mustaml\Html\CompilerConfig();
		$config->registerAutoloader(new TemplateDirAl('test-php/data'));
		$main=new \Mustaml\Mustaml("-hornshee.mustaml",$data,$config);
		
		$this->assertEquals('<html><div id="hello">from ahorn.mustaml with love<div id="hello">from banshee.mustaml<p class="bansheeblock"><span id="enbloc"></span></p></div></div></html>',$main());
	}
	public function testLoadingTwoNestedTemplateUsingInterfaceCallback() {
		
		$data=array("love"=>"with love");
		$config=new \Mustaml\Html\CompilerConfig();
		$config->registerAutoloader(new TemplateDirAl('test-php/data'));
		$main=new \Mustaml\Mustaml("-hornshee.mustaml",$data,$config);
		
		$this->assertEquals('<html><div id="hello">from ahorn.mustaml with love<div id="hello">from banshee.mustaml<p class="bansheeblock"><span id="enbloc"></span></p></div></div></html>',$main());
	}
	public function testNotFindingTemplateFile() {
		
		$data=array("love"=>"with love");
		$config=new \Mustaml\Html\CompilerConfig();
		$config->registerAutoloader(new TemplateDirAl('test-php/data'));
		$main=new \Mustaml\Mustaml("-nonexistent.mustaml",$data,$config);
		
		$this->assertEquals('',$main());
	}
	public function testLoadingInvalidJSONData() {
		
		$data=array("love"=>"with love");
		$config=new \Mustaml\Html\CompilerConfig();
		$config->registerAutoloader(new TemplateDirAl('test-php/data'));
		$main=new \Mustaml\Mustaml("-invalid-json.mustaml",$data,$config);
		
		$this->assertEquals('<p>invalid json is silenty ignored</p>',$main());
	}
}
?>