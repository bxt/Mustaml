<?php
namespace Mustaml;
require_once 'mustaml.php';

class MustamlTest extends \PHPUnit_Framework_TestCase {
	public function testNstingWithoutSubblock() {
		
		$child=new Mustaml("#child\n  %p =foo\n  %p =bam",array("foo"=>"override"));
		$main=new Mustaml("%p -child\n%p =foo\n%p =bam",array("foo"=>"bar","bam"=>"inherit","child"=>$child));
		
		$this->assertEquals('<p><div id="child"><p>override</p><p>inherit</p></div></p><p>bar</p><p>inherit</p>',$main());
	}
	public function testNstingWithAnotherSubblock() {
		
		$child=new Mustaml("#child\n  %p =foo\n  %p =bam\n#childblock\n  --",array("foo"=>"override","col"=>"additional"));
		$main=new Mustaml("%p -child\n  %span =foo\n  %span =col\n%p =foo\n%p =bam",array("foo"=>"bar","bam"=>"inherit","child"=>$child));
		
		$this->assertEquals('<p><div id="child"><p>override</p><p>inherit</p></div><div id="childblock"><span>bar</span><span>additional</span></div></p><p>bar</p><p>inherit</p>',$main());
	}
}
?>