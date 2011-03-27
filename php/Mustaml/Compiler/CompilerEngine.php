<?php
namespace Mustaml\Compiler;

class CompilerEngine {
	// shedule types
	const S_NODE=0;
	const S_BUFFERPUSH=1;
	const S_ECHO=2;
	const S_BUFFERPOP=3;
	public final function render($ast,$data=array()) {
		$this->initialize();
		$this->sheduleRender($ast,$data);
		while ($cur=$this->todoPopAndProzess());
		return $this->bufferPop();
	}
	protected final function initialize() {
		$this->outBuf='';
		$this->buffers=array();
		$this->todo=array();
		$this->bufferPush();
	}
	protected final function bufferPush() {
		array_push($this->buffers,'');
	}
	protected final function bufferPop() {
		return array_pop($this->buffers);
	}
	protected final function bufferAppend($str) {
		$this->buffers[count($this->buffers)-1].=$str;
	}
	protected final function sheduleRender($ast,$data) {
		array_push($this->todo,array(self::S_NODE,$ast,$data));
	}
	protected final function sheduleEcho($str='') {
		array_push($this->todo,array(self::S_ECHO,$str));
	}
	protected final function sheduleBufferPush() {
		array_push($this->todo,array(self::S_BUFFERPUSH));
	}
	protected final function sheduleBufferPop($cb=false) {
		if($cb) {
			array_push($this->todo,array(self::S_BUFFERPOP,$cb));
		} else {
			array_push($this->todo,array(self::S_BUFFERPOP));
		}
	}
	protected final function todoPopAndProzess() {
			$todo=array_pop($this->todo);
			if(!$todo) return $todo;
			if($todo[0]==self::S_NODE) {
				list(,$ast,$data)=$todo;
				$renderMethod='render_'.$ast->type;
				$this->$renderMethod($ast,$data);
				
			} elseif($todo[0]==self::S_ECHO) {
				$this->bufferAppend($todo[1]);
				
			} elseif($todo[0]==self::S_BUFFERPUSH) {
				$this->bufferPush();
				
			} elseif($todo[0]==self::S_BUFFERPOP) {
				if($b=$this->bufferPop()) {
					if($todo[1]) {
						$f=$todo[1];
						$b=$this->$f($b);
					}
					$this->bufferAppend($b);
				}
			}
			return $todo;
	}
	protected final function renderChildren($ast,$data) {
		for($i=count($ast->children)-1;$i>=0;$i--) {
			$this->sheduleRender($ast->children[$i],$data);
		}
	}
}
