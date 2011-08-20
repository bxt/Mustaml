<?php
namespace Mustaml\Compiler;

/**
 * Does the non-HTML-specific parts of compiling AST
 */
abstract class CompilerBase extends CompilerEngine {
	private $config;
	/**
	 * Intialize with a CompilerConfig
	 */
	public function __construct($config=null) {
		$this->config=$config?:new CompilerConfig;
	}
	/**
	 * Return current CompilerConfig
	 */
	protected final function getConfig() {
		return $this->config;
	}
	/**
	 * Check if a variable is set or at least autoloadable
	 * @param array Current template data to use
	 * @param String Varname
	 */
	protected function issetData($data,$key) {
		if(isset($data[$key])) return true;
		return $this->config->isAutoloadable($key);
	}
	/**
	 * Get a variables contents
	 * @param array Current template data to use
	 * @param String Varname
	 */
	protected function getData($data,$key) {
		if(isset($data[$key])) return $data[$key];
		return $this->config->getAutoloadable($key);
	}
	
	/**
	 * Render function for the root node
	 * @param Node Child nodes
	 * @param array Current template data to use
	 */
	protected function render_root($ast,$data) {
		$this->renderChildren($ast,$data);
	}
	/**
	 * Render function for '='-Operator, html-escaped textoutput
	 * @param Node Child nodes
	 * @param array Current template data to use
	 */
	protected function render_hecho($ast,$data) {
		if($this->issetData($data,$ast->varname)) {
			$this->sheduleEcho(htmlspecialchars($this->getData($data,$ast->varname)));
		}
	}
	/**
	 * Render function for '^'-operator
	 * @param Node Child nodes
	 * @param array Current template data to use
	 */
	protected function render_notval($ast,$data) {
		if( !$this->issetData($data,$ast->varname) || !$this->getData($data,$ast->varname) ) {
			$this->renderChildren($ast,$data);
		}
	}
	/**
	 * Render function for '^^'-operator
	 * @param Node Child nodes
	 * @param array Current template data to use
	 */
	protected function render_notnotval($ast,$data) {
		if( $this->issetData($data,$ast->varname) && $this->getData($data,$ast->varname) ) {
			$this->renderChildren($ast,$data);
		}
	}
	/**
	 * Render function for Minus-operator
	 * @param Node Child nodes
	 * @param array Current template data to use
	 */
	protected function render_val($ast,$data) {
		if($this->issetData($data,$ast->varname)) {
			$v=$this->getData($data,$ast->varname);
			if(is_callable($v)) {
				$r=new \Mustaml\Ast\Node('root');
				$r->children=$ast->children;
				$this->sheduleEcho($v($r,$data));
			} elseif(is_object($v)) {
					$newdata=$data;
					$addeddata=\Mustaml\Util\Object2Map::convert($v);
					$newdata=array_merge($newdata,$addeddata);
					$this->renderChildren($ast,$newdata);
			} elseif(is_array($v)) {
				$isAssoc=false;
				for($i=count($v)-1;$i>=0;$i--) {
					if (isset($v[$i])) continue;
					$isAssoc=true;
					$newdata=$data;
					$newdata=array_merge($newdata,$v);
					$this->renderChildren($ast,$newdata);
					break;
				}
				for($i=count($v)-1;$i>=0&&!$isAssoc;$i--) {
					$newdata=$data;
					$newdata['.']=$v[$i];
					if(is_array($v[$i])) {
						$newdata=array_merge($newdata,$v[$i]);
					}
					if(is_object($v[$i])) {
						$newdata=array_merge($newdata,\Mustaml\Util\Object2Map::convert($v[$i]));
					}
					$this->renderChildren($ast,$newdata);
				}
			} elseif ($v===true) {
				$this->renderChildren($ast,$data);
			} elseif ($v===false) {
			} else {
				if(count($ast->children)<1) {
					$this->renderChildren($ast,$data);
					$this->sheduleEcho($v);
				} else { // whatever it is, we have a block and we give it to the block:
					$newdata=$data;
					$newdata['.']=$v;
					$this->renderChildren($ast,$newdata);
				}
			}
		}
	}
	/**
	 * Render function for direct output
	 * @param Node Child nodes
	 * @param array Current template data to use
	 */
	protected function render_text($ast,$data) {
		$this->renderChildren($ast,$data);
		$this->sheduleEcho($ast->contents);
	}
	/**
	 * Render function for comment nodes
	 *
	 * Does nothing. 
	 * @param Node Child nodes
	 * @param array Current template data to use
	 */
	protected function render_comment($ast,$data) {
		// we don't touch children here
		// and basicly do nothng ;)
	}
	
	// Abstract render functions to be implemented by subclasses
	
	/**
	 * Render function for html comment nodes
	 * @param Node Child nodes
	 * @param array Current template data to use
	 */
	abstract protected function render_hcomment($ast,$data);
	/**
	 * Processing function for html comment nodes' output
	 * @param Node Child nodes
	 * @param array Current template data to use
	 */
	abstract protected function process_hcomment($contents);
	/**
	 * Processing function for the doctype declatation
	 * @param Node Child nodes
	 * @param array Current template data to use
	 */
	abstract protected function render_doctype($ast,$data);
	/**
	 * Processing function for html tags
	 * @param Node Child nodes
	 * @param array Current template data to use
	 */
	abstract protected function render_htag($ast,$data);
}
