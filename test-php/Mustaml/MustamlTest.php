<?php
namespace Mustaml;
require_once 'mustaml.php';

class MustamlTest extends \PHPUnit_Framework_TestCase {
	/**
	 * this is the maximum nesing level
	 * we souldn't have too much revursion here
	 * and PHP souldn't run out of adresses etc.
	 * and don't set it too high, because then
	 * tests tun forever and eat all reccources
	 */
	const MAX_NESTING_LVL=400;
	//const MAX_NESTING_LVL=4000;
	
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
	public function testMaximumNestingLevel() {
		$lvl=self::MAX_NESTING_LVL;
		
		$mustaml='';
		$ws='';
		$html_a='';
		$html_b='';
		for($i=0;$i<$lvl;$i++) {
			$mustaml.=$ws.'%p'."\n";
			$ws.=' ';
			$html_a.='<p>';
			$html_b.='</p>';
		}
		
		$main=new Mustaml($mustaml);
		$this->assertEquals($html_a.$html_b,$main());
	}
	public function testMaximumNestingLevelOneliner() {
		$lvl=self::MAX_NESTING_LVL;
		
		$mustaml='';
		$html_a='';
		$html_b='';
		for($i=0;$i<$lvl;$i++) {
			$mustaml.='%p ';
			$html_a.='<p>';
			$html_b.='</p>';
		}
		
		$main=new Mustaml($mustaml);
		$this->assertEquals($html_a.$html_b,$main());
	}
}
?>