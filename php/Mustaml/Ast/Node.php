<?php
namespace Mustaml\Ast;

/**
 * A Node is a part of an AST containing other nodes as children
 */
class Node {
	/**
	 * Holds  a type identifying string
	 */
	public $type;
	/**
	 * Holds the node's children nodes
	 */
	public $children=array();
	/**
	 * Intialize with a type identifying string
	 */
	public function __construct($type) {
		$this->type=$type;
	}
}



