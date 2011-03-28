<?php
namespace Mustaml;

/**
 * Class providing a simple interface to mustaml
 */
class Mustaml {
	/**
	 * Holds the configuration used for rendering
	 */
	private $config;
	/**
	 * Holds the data always used for rendering
	 */
	private $data=array();
	/**
	 * Holds an AST or template string to render
	 */
	private $template='';
	/**
	 * Get a new Instance for a template optionally with prefilling data and a config
	 */
	public function __construct($template,$data=array(),$config=null) {
		$this->config=$config?:new Html\CompilerConfig;
		$this->data=$data;
		$this->template=$template;
	}
	/**
	 * Alias for action(), all parameters optional
	 *
	 * With this you can do `$m= new Mustaml(); $m();`
	 */
	public function __invoke($yieldAst=false,$yieldData=array()) {
		return $this->action($yieldAst,$yieldData);
	}
	/**
	 * Alias for action()
	 *
	 * With this you can do `echo new Mustaml();`
	 */
	public function __toString() {
		return $this->action(false,array());
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
	/**
	 * Render the template, optionally making another AST with data availible as '-' for yield
	 */
	private function action($yieldAst,$yieldData) {
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
}