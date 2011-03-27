<?php
namespace Mustaml\Ast;

/**
 * A node using data provided at rendertime
 */
class DataNode extends Node {
	/**
	 * Holds the key string of the loaded data
	 */
	public $varname='';
	/**
	 * Intialize with a type identifying string
	 */
	public function __construct($type='val') {
		parent::__construct($type);
	}
}



