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
				$key=new Node();
				$key->type="attr";
				$key->name=$s->get(self::anum);
				$s->get(self::ws);
				if($s->getOne(self::eq)) {
					//value
					$val=new Node();
					$val->type="text";
					$key->children[]=$val;
					
					$s->getOne(self::gt);// accept ruby-like hash
					$s->get(self::ws);
					
					if($s->getOne(self::q)) {
						// quoted sting
						$c=$s->getUnless(self::q);
						$s->getOne(self::q);
						if($s->is()&&!$s->is(self::sep)) {
							throw new SyntaxErrorException("Text after quote");
						}
						$valinner=new Node();
						$valinner->type="text";
						$valinner->contents=$c;
						$val->children[]=$valinner;
					} else {
						// unquotet value list
						while($s->is()&&!$s->is(self::sep)) {
							$dynval=self::parse_dynval($s);
							if($dynval) {
								// var "val" value
								$val->children[]=$dynval;
							} else {
								// unquoted text value
								
								$c=$s->getUnless(self::sep,self::dyneq);
								$valinner=new Node();
								$valinner->type="text";
								$valinner->contents=$c;
								$val->children[]=$valinner;
								
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
	function parse_dynval($s) {
		if($s->getOne(self::dyneq)) {
			if($s->is(self::sep)) throw new SyntaxErrorException("No varname");
			$varname=$s->getUnless(self::sep);
			$s->getOne(self::sep);
			
			$node=new Node();
			$node->type="val";
			$node->varname=$varname;
			return $node;
		}
		return false;
	}
}

