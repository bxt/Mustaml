<?php
namespace Mustaml;

class Parser {
	public function parseString($templateString) {
		$rootnode=new Node();
		$rootnode->type='root';
		$lines=explode("\n",$templateString);
		$indentLevels=array();
		$parentBlocks=array();
		$parentBlocks[-1]=$rootnode;
		foreach($lines as $line) {
			preg_match('/^([\\t ]*)(.*)/s',$line,$startWs);
			$indent=strlen(str_replace("\t",'        ',$startWs[1]));
			$nodecode=$startWs[2];
			if($nodecode=='') continue; // ignore empty lines
			$preIndent=array_sum($indentLevels);
			if($indent>$preIndent) {
				$indentLevels[]=$indent-$preIndent;
			}
			if($indent<$preIndent) {
				for($isum=0,$change=$preIndent-$indent;$isum<$change;$isum+=array_pop($indentLevels));
				if($isum>$change) throw new SyntaxErrorException("Indent-error");
			}
			$level=count($indentLevels);
			//echo $level.'---'.$nodecode."\n";
			$node=$this->parse_node($nodecode);
			$parentBlocks[$level]=$node;
			//got multiple nodes from one line? use innermost one:
			while(isset($parentBlocks[$level]->children[0])) {
				$parentBlocks[$level]=$parentBlocks[$level]->children[0];
			}
			$parentBlocks[$level-1]->children[]=$node;
		}
		return $rootnode;
	}
	private function parse_node($nodecode) {
		switch(true) {
			case (!isset($nodecode[1])): $node=$this->parse_text($nodecode);$node->type='text';return $node; // at least 2 chars
			case ($nodecode[0]=='-'): switch(true) {
				case ($nodecode[1]=='/'): $node=$this->parse_comment($nodecode); $node->type='comment';return $node;
				case ($nodecode[1]=='^'): if(isset($nodecode[2])&&$nodecode[2]=='^') {
						$node=$this->parse_notnotval($nodecode); $node->type='notnotval';return $node;
					} else { $node=$this->parse_notval($nodecode); $node->type='notval';return $node; }
				default: $node=$this->parse_val($nodecode); $node->type='val';return $node;
			}
			case ($nodecode[0]=='='): $node=$this->parse_hecho($nodecode); $node->type='hecho';return $node;
			case ($nodecode[0]=='/'): $node=$this->parse_hcomment($nodecode); $node->type='hcomment';return $node;
			case ($nodecode[0]=='\\'): $node=$this->parse_excapedText($nodecode); $node->type='text';return $node;
			case ($nodecode[0]=='%'):
			case ($nodecode[0]=='.'):
			case ($nodecode[0]=='#'): $node=$this->parse_htag($nodecode); $node->type='htag';return $node;
			case ($nodecode[0]=='!'&&$nodecode[1]=='!'&&isset($nodecode[2])&&$nodecode[2]=='!'): $node=$this->parse_doctype($nodecode); $node->type='doctype';return $node;
			default:$node=$this->parse_text($nodecode);$node->type='text';return $node;
		}
	}
	private function parse_text($contents) {
		$node=new Node();
		$node->contents=$contents;
		return $node;
	}
	private function parse_excapedText($nodecode) {
		return $this->parse_text(substr($nodecode,1));
	}
	private function parse_hcomment($contents) {
		$node=new Node();
		$node->children[]=$this->parse_node(substr($contents,1));
		return $node;
	}
	private function parse_hecho($nodecode) {
		$node=new Node();
		$node->varname=substr($nodecode,1);
		return $node;
	}
	private function parse_notval($nodecode) {
		preg_match("/^(.+?)( (.*))?$/",substr($nodecode,2),$m);
		$node=new Node();
		$node->varname=$m[1];
		if(isset($m[3])) {
			$node->children[]=$this->parse_node($m[3]);
		}
		return $node;
	}
	private function parse_notnotval($nodecode) {
		preg_match("/^(.+?)( (.*))?$/",substr($nodecode,3),$m);
		$node=new Node();
		$node->varname=$m[1];
		if(isset($m[3])) {
			$node->children[]=$this->parse_node($m[3]);
		}
		return $node;
	}
	private function parse_val($nodecode) {
		preg_match("/^(.+?)( (.*))?$/",substr($nodecode,1),$m);
		$node=new Node();
		$node->varname=$m[1];
		if(isset($m[3])) {
			$node->children[]=$this->parse_node($m[3]);
		}
		return $node;
	}
	private function parse_doctype($nodecode) {
		$node=new Node();
		return $node;
	}
	private function parse_comment($nodecode) {
		$node=new Node();
		return $node;
	}
	private function parse_htag($nodecode) {
		$node=new Node();
		preg_match("/^  (%(.+?))?  (\#(.+?))?  ((\..+?)*)  (\((.*?)\))?  (\ (.*))?  $/x",$nodecode,$m);
		//var_dump($m);
		if(isset($m[2])&&$m[2]!='') {
			$node->name=$m[2];
		} else {
			$node->name="div";
		}
		if(isset($m[4])&&$m[4]!='') {
			$node->attributes[]=$this->get_attr_node('id',$m[4]);
		}
		if(isset($m[5])&&$m[5]!='') {
			$classes=explode('.',$m[5]);
			array_shift($classes);
			foreach($classes as $class) {
				$node->attributes[]=$this->get_attr_node('class',$class);
			}
		}
		if(isset($m[8])&&$m[8]!='') {
			$input=$m[8];
			$s=new Scanner($input);
			$p=new AttrParser();
			$attr=$p->parse_attr($s);
			$node->attributes=array_merge($node->attributes,$attr);
		}
		if(isset($m[10])&&$m[10]!='') {
			$node->children[]=$this->parse_node($m[10]);
		}
		return $node;
	}
	private function get_attr_node($key,$val) {
		$attr=new Node();
		$attr->type="attr";
		$attr->name=$key;
		$t=new Node();
		$t->type="text";
		$t->contents=$val;
		$attr->children[]=$t;
		return $attr;
	}
}
