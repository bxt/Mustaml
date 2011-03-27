<?php
namespace Mustaml;

class Parser {
	private $restNodecode=false;
	public function parseString($templateString) {
		$rootnode=new Ast\Node('root');
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
			$this->restNodecode=$nodecode;
			$parentNode=$parentBlocks[$level-1];
			while ($this->restNodecode) {
				$node=$this->parse_node($this->restNodecode);
				$parentNode->children[]=$node;
				$parentNode=$node;
			}
			$parentBlocks[$level]=$parentNode;
		}
		return $rootnode;
	}
	private function parse_node($nodecode) {
		$this->restNodecode=false; // usualy we don't expect more nodes in this line
		switch(true) {
			case (!isset($nodecode[1])): $node=$this->parse_text($nodecode);return $node; // at least 2 chars
			case ($nodecode[0]=='-'): switch(true) {
				case ($nodecode[1]=='/'): $node=$this->parse_comment($nodecode); return $node;
				case ($nodecode[1]=='^'): if(isset($nodecode[2])&&$nodecode[2]=='^') {
						$node=$this->parse_notnotval($nodecode); return $node;
					} else { $node=$this->parse_notval($nodecode); return $node; }
				default: $node=$this->parse_val($nodecode);return $node;
			}
			case ($nodecode[0]=='='): $node=$this->parse_hecho($nodecode); return $node;
			case ($nodecode[0]=='/'): $node=$this->parse_hcomment($nodecode) ;return $node;
			case ($nodecode[0]=='\\'): $node=$this->parse_excapedText($nodecode); return $node;
			case ($nodecode[0]=='%'):
			case ($nodecode[0]=='.'):
			case ($nodecode[0]=='#'): $node=$this->parse_htag($nodecode); return $node;
			case ($nodecode[0]=='!'&&$nodecode[1]=='!'&&isset($nodecode[2])&&$nodecode[2]=='!'): $node=$this->parse_doctype($nodecode); return $node;
			default:$node=$this->parse_text($nodecode); return $node;
		}
	}
	private function parse_text($contents) {
		$node=new Ast\TextNode();
		$node->contents=$contents;
		return $node;
	}
	private function parse_excapedText($nodecode) {
		return $this->parse_text(substr($nodecode,1));
	}
	private function parse_hcomment($contents) {
		$node=new Ast\Node('hcomment');
		$this->restNodecode=substr($contents,1);
		return $node;
	}
	private function parse_hecho($nodecode) {
		$node=new Ast\DataNode('hecho');
		$node->varname=substr($nodecode,1);
		return $node;
	}
	private function parse_notval($nodecode) {
		preg_match("/^(.+?)( (.*))?$/",substr($nodecode,2),$m);
		$node=new Ast\DataNode('notval');
		$node->varname=$m[1];
		if(isset($m[3])) {
			$this->restNodecode=$m[3];
		}
		return $node;
	}
	private function parse_notnotval($nodecode) {
		preg_match("/^(.+?)( (.*))?$/",substr($nodecode,3),$m);
		$node=new Ast\DataNode('notnotval');
		$node->varname=$m[1];
		if(isset($m[3])) {
			$this->restNodecode=$m[3];
		}
		return $node;
	}
	private function parse_val($nodecode) {
		preg_match("/^(.+?)( (.*))?$/",substr($nodecode,1),$m);
		$node=new Ast\DataNode();
		$node->varname=$m[1];
		if(isset($m[3])) {
			$this->restNodecode=$m[3];
		}
		return $node;
	}
	private function parse_doctype($nodecode) {
		$node=new Ast\Node('doctype');
		return $node;
	}
	private function parse_comment($nodecode) {
		$node=new Ast\Node('comment');
		return $node;
	}
	private function parse_htag($nodecode) {
		$node=new Ast\TagNode();
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
			//$node->children[]=$this->parse_node($m[10]);
			$this->restNodecode=$m[10];
		}
		return $node;
	}
	private function get_attr_node($key,$val) {
		$attr=new Ast\TagNode('attr');
		$attr->name=$key;
		$t=new Ast\TextNode();
		$t->contents=$val;
		$attr->children[]=$t;
		return $attr;
	}
}
