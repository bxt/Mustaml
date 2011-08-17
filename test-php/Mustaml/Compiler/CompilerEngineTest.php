<?php
namespace Mustaml\Compiler;
require_once 'mustaml.php';

class CompilerEngineTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * @dataProvider strings
	 */
	public function testInsertingData($s) {
		$dut=new CompilationSubclass();
		$ast=new \stdClass();
		$ast->type='test3';
		$result=$dut->render($ast,array('aah'=>$s));
		$this->assertEquals(' {test3,d='.$s.'/} ',$result);
	}
	public function strings() {
		return array(
			array('lalala'),
			array('oooh$?öäü.;'),
			array("\nx\tl"),
		);
	}
	
	public function testNestingOfVariousAstObjects() {
		$dut=new CompilationSubclass();
		$data=array("aah"=>"val");
		$ast=new \stdClass();
		$ast->type='test1';
			$astC1=new \stdClass();
			$astC1->type='test3';
			
			$astC2=new \stdClass();
			$astC2->type='test2';
				$astC2C1=new \stdClass();
				$astC2C1->type='test3';
			$astC2->children=array($astC2C1);
			
			$astC3=new \stdClass();
			$astC3->type='test1';
				$astC3C1=new \stdClass();
				$astC3C1->type='test4';
			$astC3->children=array($astC3C1);
			
		$ast->children=array($astC1,$astC2,$astC3);
		$result=$dut->render($ast,$data);
		
		$this->assertEquals(' {test1,d=val}   {{tteesstt33,,dd==vvaall//}}    {{tteesstt22,,dd==vvaall}}    {{tteesstt44,,dd==nneeuu//}}    {{//tteesstt22}}    {{tteesstt11,,dd==vvaall}}      {{{{tttteeeesssstttt4444,,,,dddd====vvvvaaaallll////}}}}      {{//tteesstt11}}   {/test1} ',$result);
	}
	
	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testExceptionWithBadProcessingCallback() {
		$dut=new CompilationSubclass();
		$ast=new \stdClass();
		$ast->type='testE';
		$result=$dut->render($ast,array('aah'=>'val'));
		var_dump($result);
	}
	
}

class CompilationSubclass extends CompilerEngine {
	protected function render_test1($ast,$data) {
		$this->sheduleEcho(' {/test1} ');
		$this->sheduleBufferPop('processor');
		$this->renderChildren($ast,$data);
		$this->sheduleBufferPush();
		$this->sheduleEcho(' {test1,d='.$data['aah'].'} ');
	}
	protected function render_test2($ast,$data) {
		$ast2=new \stdClass();
		$ast2->type='test1';
		$ast2C=new \stdClass();
		$ast2C->type='test4';
		$ast2->children=array($ast2C);
		
		$this->sheduleBufferPop();
		$this->sheduleEcho(' {/test2} ');
		$this->renderChildren($ast2,array('aah'=>'neu'));
		$this->sheduleEcho(' {test2,d='.$data['aah'].'} ');
		$this->sheduleBufferPush();
	}
	protected function render_test3($ast,$data) {
		$this->sheduleEcho(' {test3,d='.$data['aah'].'/} ');
	}
	protected function render_test4($ast,$data) {
		$this->sheduleEcho(' {test4,d='.$data['aah'].'/} ');
	}
	protected function render_testE($ast,$data) {
		$this->sheduleBufferPop(array(1,2,3));
		$this->sheduleEcho('tE');
		$this->sheduleBufferPush();
	}
	protected function processor($in) {
		$out='';
		for($i=0,$len=strlen($in);$i<$len;$i++) {
			$out.=$in[$i];
			$out.=$in[$i];
		}
		return $out;
	}
}

?>