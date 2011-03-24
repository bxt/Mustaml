<?php
namespace Mustaml\Ast;

class TextNode extends Node {
	public $contents='';
	public function __construct($type='text') {
		parent::__construct($type);
	}
}



