<?php
namespace Mustaml\Ast;

class DataNode extends Node {
	public $varname='';
	public function __construct($type='val') {
		parent::__construct($type);
	}
}



