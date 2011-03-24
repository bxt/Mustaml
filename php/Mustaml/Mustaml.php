<?php
namespace Mustaml;

class Mustaml {
	private $config;
	private $data=array();
	private $template='';
	public function __construct($template,$data=array(),$config=null) {
		$this->config=$config?:new HtmlCompilerConfig;
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
			
			$c=new HtmlCompiler($this->config);
			$newData=$this->data;
			if($yieldAst) {
				$newData['-']=new Mustaml($yieldAst,$yieldData+$this->data,$this->config);
			}
			$newData=$newData+$yieldData;
			$html=$c->render($ast,$newData);
			return $html;
	}
}