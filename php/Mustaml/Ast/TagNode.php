<?php
namespace Mustaml\Ast;

/**
 * A Node that stands for an HTML-tag
 */
class TagNode extends Node {
	/**
	 * Holds the node's attribute children nodes
	 */
	public $attributes=array();
	/**
	 * Holds the tag name
	 */
	public $name='div';
	/**
	 * Intialize with a type identifying string
	 */
	public function __construct($type='htag') {
		parent::__construct($type);
	}
}



