<?php
namespace Mustaml\Parser;

/**
 * Parsing an attribute string into an AST
 */
class AttrParser {
	/**
	 * Charlist considered as whitepsace token
	 */
	const ws=' ';
	/**
	 * Charlist considered as indentifier start token
	 */
	const a='A-Za-z';
	/**
	 * Charlist considered as optinal tokens after equals
	 */
	const gt='>';
	/**
	 * Charlist considered as indentifier content tokens
	 */
	const anum='0-9A-Za-z_-';
	/**
	 * Charlist considered as equals token
	 */
	const eq='=';
	/**
	 * Charlist considered as equals token introducing a dynamic attribute
	 */
	const dyneq='=';
	/**
	 * Charlist considered as separating token
	 */
	const sep=' ,';
	/**
	 * Charlist considered as quoting token
	 */
	const q='"';
	/**
	 * Parses an attribute string into an AST
	 * @param Scanner A Scanner holding the attribute string
	 */
	function parse_attr($s) {
		
		$attrs=array();
		while($s->is()) {
			$s->get(self::ws);
			if($s->is(self::a)) {
				//identifier
				$key=new \Mustaml\Ast\TagNode('attr');
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
			$dynval=self::parse_dynval($s,true);
			if($dynval) $attrs[]=$dynval;
		}
		return $attrs;
	}
	/**
	 * Parses an text attribute string into an AST
	 * @param String value of the text node
	 */
	private function construct_textnode($contents) {
		$t=new \Mustaml\Ast\TextNode();
		$t->contents=$contents;
		return $t;
	}
	/**
	 * Parses an dynamic attribute string into an AST if applicable
	 * @param Scanner A Scanner with cursor at possible dyn. attr.
	 * @return boolean|\Mustaml\Ast\DataNode False, if not a dyn. attr., else the node
	 */
	private function parse_dynval($s,$outer=false) {
		if($s->getOne(self::dyneq)) {
			if($s->is(self::sep)||!$s->is()) throw new SyntaxErrorException("No varname");
			$varname=$s->getUnless(self::sep,self::dyneq);
			$s->getOne($outer?self::sep:self::dyneq);
			
			$node=new \Mustaml\Ast\DataNode();
			$node->varname=$varname;
			return $node;
		}
		return false;
	}
}

