<?php
namespace Mustaml;

class Mustaml {
	private $config;
	private $data=array();
	private $template='';
	public function __construct($template,$data=array(),$config=null) {
		$this->config=$config?:new Html\CompilerConfig;
		$this->data=$data;
		$this->template=$template;
	}
	public function __invoke($yieldAst=false,$yieldData=array()) {
			if($this->template instanceof Ast\Node) {
				$ast=$this->template;
			} else {
				$p=new Parser\Parser();
				$ast=$p->parseString($this->template);
			}
			
			$c=new Html\Compiler($this->config);
			$newData=$this->data;
			if($yieldAst) {
				$newData['-']=$this->getWithTemplate($yieldAst,$yieldData);
			}
			$newData=$newData+$yieldData;
			$html=$c->render($ast,$newData);
			return $html;
	}
	/**
	 * Return a "clone" of this Mustaml instance
	 * 
	 * This is not a clone as the result is wrapping
	 * an other template and optionally has some
	 * override data
	 */
	public function getWithTemplate($template,$data=array()) {
		return new static($template,$data+$this->data,$this->config);
	}
}