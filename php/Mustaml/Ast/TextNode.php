<?php
namespace Mustaml\Ast;

/**
 * A Node that conatins display text
 */
class TextNode extends Node {
	public $contents='';
	/**
	 * Intialize with a type identifying string
	 */
	public function __construct($type='text') {
		parent::__construct($type);
	}
}



