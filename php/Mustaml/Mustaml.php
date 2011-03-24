<?php
namespace Mustaml;

class Mustaml {
	private $data=array();
	private $template='';
	public function __construct($template,$data=array()) {
		$this->data=$data;
		$this->template=$template;
	}
	public function __invoke($yieldAst=false,$yieldData=array()) {
			if($this->template instanceof Ast\Node) {
				$ast=$this->template;
			} else {
				$p=new Parser();
				$ast=$p->parseString($this->template);
			}
			
			$c=new HtmlCompiler();
			if($yieldAst) {
				$this->data['-']=new Mustaml($yieldAst,$yieldData+$this->data);
			}
			$html=$c->render($ast,$this->data+$yieldData);
			return $html;
	}
}