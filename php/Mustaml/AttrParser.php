<?php
namespace Mustaml;

class AttrParser {
	const ws=' ';
	const a='A-Za-z';
	const gt='>';
	const anum='0-9A-Za-z_-';
	const eq='=';
	const dyneq='=';
	const sep=' ,';
	const q='"';
	function parse_attr($s) {
		
		$attrs=array();
		while($s->is()) {
			$s->get(self::ws);
			if($s->is(self::a)) {
				//identifier
				$key=new Ast\TagNode('attr');
				$key->name=$s->get(self::anum);
				$s->get(self::ws);
				if($s->getOne(self::eq)) {
					//value
					$s->getOne(self::gt);// accept ruby-like hash (k=>v)
					$s->get(self::ws);
					
					if($s->getOne(self::q)) {
						// quoted sting
						$c=$s->getUnless(self::q);
						$s->getOne(self::q);
						$key->children[]=self::construct_textnode($c);
					} else {
						// unquoted value list
						while($s->is()&&!$s->is(self::sep)) {
							$dynval=self::parse_dynval($s);
							if($dynval) {
								// var "val" value
								$key->children[]=$dynval;
							} else {
								// unquoted text value
								$c=$s->getUnless(self::sep,self::dyneq);
								$key->children[]=self::construct_textnode($c);
							}
						}
					}
				}
				$s->get(self::sep);
				$attrs[]=$key;
			}
			$dynval=self::parse_dynval($s);
			if($dynval) $attrs[]=$dynval;
		}
		return $attrs;
	}
	private function construct_textnode($contents) {
		$t=new Ast\TextNode();
		$t->contents=$contents;
		return $t;
	}
	private function parse_dynval($s) {
		if($s->getOne(self::dyneq)) {
			if($s->is(self::sep)) throw new SyntaxErrorException("No varname");
			$varname=$s->getUnless(self::sep);
			$s->getOne(self::sep);
			
			$node=new Ast\DataNode();
			$node->varname=$varname;
			return $node;
		}
		return false;
	}
}

