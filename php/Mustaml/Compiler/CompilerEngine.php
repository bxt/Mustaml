<?php
namespace Mustaml\Compiler;

/**
 * Engine for compiling AS-Trees iterativly
 *
 * Meant to be subclassed by compilers. These subclasses
 * are then able to control it's multi-layer buffer, by sheduling
 * certain actions (see shedule* methods). When processing the
 * sheduled action it may call render_* methods of the subclass, 
 * which in turn may shedule additional stuff. Whenevery a buffer
 * layer is ended, you might call a callback to process the buffer
 * contents before they are written to the next higher buffer. 
 */
class CompilerEngine {
	// shedule types
	/**
	 * Shedule type for render actions
	 */
	const S_NODE=0;
	/**
	 * Shedule type for increasing buffer level
	 */
	const S_BUFFERPUSH=1;
	/**
	 * Shedule type for appending to buffer
	 */
	const S_ECHO=2;
	/**
	 * Shedule type for decreasing buffer level
	 */
	const S_BUFFERPOP=3;
	/**
	 * Given an AST start to shedule render jobs
	 */
	public final function render($ast,$data=array()) {
		$this->initialize();
		$this->sheduleRender($ast,$data);
		while ($cur=$this->todoPopAndProzess());
		return $this->bufferPop();
	}
	/**
	 * Initializes all the internal state vars
	 */
	protected final function initialize() {
		$this->outBuf='';
		$this->buffers=array();
		$this->todo=array();
		$this->bufferPush();
	}
	/**
	 * Internal function to increase buffer level
	 */
	protected final function bufferPush() {
		array_push($this->buffers,'');
	}
	/**
	 * Internal function to decrease buffer level
	 */
	protected final function bufferPop() {
		return array_pop($this->buffers);
	}
	/**
	 * Internal function to append to the current buffer
	 */
	protected final function bufferAppend($str) {
		$this->buffers[count($this->buffers)-1].=$str;
	}
	/**
	 * For subclasses to shedule render of an element
	 * @param mixed Param 1 for render_X method, used to determine X
	 * @param mixed Param 2 for render_X method
	 */
	protected final function sheduleRender($ast,$data) {
		array_push($this->todo,array(self::S_NODE,$ast,$data));
	}
	/**
	 * For subclasses to shedule buffer append
	 * @param String What to append to buffer
	 */
	protected final function sheduleEcho($str='') {
		array_push($this->todo,array(self::S_ECHO,$str));
	}
	/**
	 * For subclasses to shedule increase of buffer level
	 */
	protected final function sheduleBufferPush() {
		array_push($this->todo,array(self::S_BUFFERPUSH));
	}
	/**
	 * For subclasses to shedule decrease of buffer level
	 */
	protected final function sheduleBufferPop($cb=false) {
		array_push($this->todo,array(self::S_BUFFERPOP,$cb));
	}
	/**
	 * Internal function to process the uppermost element
	 * of sheduled actions
	 * @return array The processed element
	 */
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
				if($b=$this->bufferPop()) { // note that cb is not called for empty strings
					if($todo[1]) {
						$f=(string)$todo[1];
						if(!method_exists($this,$f)) {
							throw new \InvalidArgumentException('Processing callback must be string with name of method!');
						}
						$b=$this->$f($b);
					}
					$this->bufferAppend($b);
				}
			}
			return $todo;
	}
	/**
	 * Handy function for subclasses to shedule render
	 * of all elements of a subject's children attribute
	 *
	 * Calls sheduleRender([child],[data]) for all
	 * [subject]->children
	 * @param mixed Subject with an ->children attribute
	 * @param array Data array to be passed to sheduleRender()
	 */
	protected final function renderChildren($ast,$data) {
		for($i=count($ast->children)-1;$i>=0;$i--) {
			$this->sheduleRender($ast->children[$i],$data);
		}
	}
}
