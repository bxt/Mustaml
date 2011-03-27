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
	protected final function getConfig() {
		return $this->config;
	}
	protected function issetData($data,$key) {
		if(isset($data[$key])) return true;
		return $this->config->isAutoloadable($key);
	}
	protected function getData($data,$key) {
		if(isset($data[$key])) return $data[$key];
		return $this->config->getAutoloadable($key);
	}

	protected function render_root($ast,$data) {
		$this->renderChildren($ast,$data);
	}
	protected function render_hecho($ast,$data) {
		if($this->issetData($data,$ast->varname)) {
			$this->sheduleEcho(htmlspecialchars($this->getData($data,$ast->varname)));
		}
	}
	protected function render_notval($ast,$data) {
		if( !$this->issetData($data,$ast->varname) || !$this->getData($data,$ast->varname) ) {
			$this->renderChildren($ast,$data);
		}
	}
	protected function render_notnotval($ast,$data) {
		if( $this->issetData($data,$ast->varname) && $this->getData($data,$ast->varname) ) {
			$this->renderChildren($ast,$data);
		}
	}
	protected function render_val($ast,$data) {
		if($this->issetData($data,$ast->varname)) {
			$v=$this->getData($data,$ast->varname);
			if(is_callable($v)) {
				$r=new \Mustaml\Ast\Node('root');
				$r->children=$ast->children;
				$this->sheduleEcho($v($r,$data));
			} elseif(is_array($v)) {			
				for($i=count($v)-1;$i>=0;$i--) {
					$newdata=$data;
					$newdata['.']=$v[$i];
					if(is_array($v[$i])) {
						$newdata=array_merge($newdata,$v[$i]);
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
	protected function render_text($ast,$data) {
		$this->renderChildren($ast,$data);
		$this->sheduleEcho($ast->contents);
	}
	protected function render_comment($ast,$data) {
		// we don't touch children here
		// and basicly do nothng ;)
	}
	
	abstract protected function render_hcomment($ast,$data);
	abstract protected function process_hcomment($contents);
	abstract protected function render_doctype($ast,$data);
	abstract protected function render_htag($ast,$data);
}
