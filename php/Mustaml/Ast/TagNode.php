<?php
namespace Mustaml\Ast;

class TagNode extends Node {
	public $attributes=array();
	public $name='div';
	public function __construct($type='htag') {
		parent::__construct($type);
	}
}



