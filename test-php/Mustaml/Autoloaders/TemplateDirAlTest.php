<?php
namespace Mustaml\Autoloaders;
require_once 'mustaml.php';

class TemplateDirAlTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * @dataProvider files
	 */
	public function testLoadingAFile($file) {
		$dir='test-php/data';
		$mustaml=$this->getMock('Mustaml\\Mustaml',array('getWithTemplate'),array(''));
		$mustaml->expects($this->once())
			->method('getWithTemplate')
			->with($this->equalTo(file_get_contents($dir.'/'.$file)))
			->will($this->returnValue('is passed to caller'));
		$al=new TemplateDirAl($dir);
		$al->setMustamlBoilerplate($mustaml);
		$this->assertEquals('is passed to caller',$al->autoload($file));
	}
	public function files() {
		return array(
			array('hornshee.mustaml'),
			array('ahorn.mustaml'),
			array('banshee.mustaml'),
		);
	}
	
	public function testLoadingAFileWithJson() {
		$dir='test-php/data';
		$file='valid-json.mustaml';
		$mustaml=$this->getMock('Mustaml\\Mustaml',array('getWithTemplate'),array(''));
		$mustaml->expects($this->once())
			->method('getWithTemplate')
			->with($this->equalTo(file_get_contents($dir.'/'.$file)),$this->equalTo(array('fromjson'=>'jippeie!')))
			->will($this->returnValue('is passed to caller'));
		$al=new TemplateDirAl($dir);
		$al->setMustamlBoilerplate($mustaml);
		$this->assertEquals('is passed to caller',$al->autoload($file));
	}
	
	// INTEGRATION TESTS
	
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
	public function testNotMatchingTemplateFilename() {
		
		$data=array();
		$config=new \Mustaml\Html\CompilerConfig();
		$config->registerAutoloader(new TemplateDirAl('test-php/data'));
		$main=new \Mustaml\Mustaml("-nonexistent",$data,$config);
		
		$this->assertEquals('',$main());
	}
	public function testLoadingInvalidJSONData() {
		
		$data=array("frommustaml"=>"ahoi");
		$config=new \Mustaml\Html\CompilerConfig();
		$config->registerAutoloader(new TemplateDirAl('test-php/data'));
		$main=new \Mustaml\Mustaml("-invalid-json.mustaml",$data,$config);
		
		$this->assertEquals('<p>invalid json is silenty ignored</p>',$main());
	}
	public function testLoadingValidJSONData() {
		
		$data=array("frommustaml"=>"ahoi");
		$config=new \Mustaml\Html\CompilerConfig();
		$config->registerAutoloader(new TemplateDirAl('test-php/data'));
		$main=new \Mustaml\Mustaml("-valid-json.mustaml",$data,$config);
		
		$this->assertEquals('<p>jippeie!</p><p>ahoi</p>',$main());
	}
	public function testLoadingTwoNestedTemplateWithJsonOverwrites() {
		
		$data=array("myvar"=>"(outer var value)");
		$config=new \Mustaml\Html\CompilerConfig();
		$config->registerAutoloader(new TemplateDirAl('test-php/data'));
		$main=new \Mustaml\Mustaml("%p =myvar\n-subtmpl.mustaml\n %b =myvar",$data,$config);
		
		$this->assertEquals('<p>(outer var value)</p><div id="subtmpl"><i>(var of subtmpl)</i><div class="sub"><b>(outer var value)</b></div></div>',$main());
	}

}
?>