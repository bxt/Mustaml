<?php
namespace Mustaml;

class Mustaml {
	private $data=array();
	private $template='';
	public function __construct($template,$data=array()) {
		$this->data=$data;
		$this->template=$template;
	}
	public function __invoke() {
			$p=new Parser();
			$ast=$p->parseString($this->template);
			$c=new HtmlCompiler();
			$html=$c->render($ast,$this->data);
			return $html;
	}
}