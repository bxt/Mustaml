<?php
namespace Mustaml\Ast;

class Node {
	public $type;
	public $children=array();
	public function __construct($type) {
		$this->type=$type;
	}
}



