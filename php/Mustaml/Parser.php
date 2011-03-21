<?php
namespace Mustaml;

class Parser {
	final public function parseString($templateString) {
		$rootnode=new RootNode();
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
				if($isum>$change) throw new \Exception("Indent-error");
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
		$tokenMap=array(// $eventRegexpsWithHandlers
			'/^!!!/',    'doctype',     //  !!!
			'/^-\\//',   'comment',     //  -\
			'/^[%\\.#]/','htag',        //  %, . and #
			'/^-\\^/',   'notval',      //  -^
			'/^-/',      'val',         //  -
			'/^=/',      'hecho',       //  =
			'/^\\//',    'hcomment',    //  /
			'/^\\\\/',   'excapedText', //  \
		); 
		for($i=0,$len=count($tokenMap)-1;$i<$len;$i+=2) {
			if(preg_match($tokenMap[$i],$nodecode)===1) {
				$method='parse_'.$tokenMap[$i+1];
				return $this->$method($nodecode);
			}
		}
		return $this->parse_text($nodecode);
	}
	private function parse_text($contents) {
		$node=new TextNode();
		$node->contents=$contents;
		return $node;
	}
	private function parse_excapedText($nodecode) {
		return $this->parse_text(substr($nodecode,1));
	}
	private function parse_hcomment($contents) {
		$node=new HcommentNode();
		$node->children[]=$this->parse_node(substr($contents,1));
		return $node;
	}
	private function parse_hecho($nodecode) {
		$node=new HechoNode();
		$node->varname=substr($nodecode,1);
		return $node;
	}
	private function parse_notval($nodecode) {
		preg_match("/^(.+?)( (.*))?$/",substr($nodecode,2),$m);
		$node=new NotvalNode();
		$node->varname=$m[1];
		if(isset($m[3])) {
			$node->children[]=$this->parse_node($m[3]);
		}
		return $node;
	}
	private function parse_val($nodecode) {
		preg_match("/^(.+?)( (.*))?$/",substr($nodecode,1),$m);
		$node=new ValNode();
		$node->varname=$m[1];
		if(isset($m[3])) {
			$node->children[]=$this->parse_node($m[3]);
		}
		return $node;
	}
	private function parse_doctype($nodecode) {
		$node=new DoctypeNode();
		return $node;
	}
	private function parse_comment($nodecode) {
		$node=new CommentNode();
		return $node;
	}
	private function parse_htag($nodecode) {
		$node=new HtagNode;
		preg_match("/^  (%(.+?))?  (\#(.+?))?  ((\..+?)*)  (\((.*?)\))?  (\ (.*))?  $/x",$nodecode,$m);
		//var_dump($m);
		if(isset($m[2])&&$m[2]!='') {
			$node->name=$m[2];
		} else {
			$node->name="div";
		}
		if(isset($m[4])&&$m[4]!='') {
			$node->attributes["id"]=$m[4];
		}
		if(isset($m[5])&&$m[5]!='') {
			$classes=explode('.',$m[5]);
			array_shift($classes);
			foreach($classes as $class) {
				$node->attributes["class"][]=$class;
			}
		}
		if(isset($m[8])&&$m[8]!='') {
			//attrs
		}
		if(isset($m[10])&&$m[10]!='') {
			$node->children[]=$this->parse_node($m[10]);
		}
		return $node;
	}
}

class Node {
	public $children=array();
	public function html($data) {
		$html='';
		$html.=$this->html_before($data);
		foreach($this->children as $c) {
			$html.=$c->html($data);
		}
		$html.=$this->html_after($data);
		return $html;
	}
	protected function html_before($data) {
		return '';
	}
	protected function html_after($data) {
		return '';
	}
}

class RootNode extends Node {
	public function html($data=array()) {
		return parent::html($data);
	}
}

abstract class DataNode extends Node {

}

class HechoNode extends DataNode {
	public $varname='';
	public function html($data) {
		if(isset($data[$this->varname])) {
			return htmlspecialchars($data[$this->varname]);
		}
	}
}

class NotvalNode extends DataNode {
	public $varname='';
	public function html($data) {
		if(!isset($data[$this->varname]) || $data[$this->varname]===false) {
			return parent::html($data);
		}
	}
}

class ValNode extends DataNode {
	public $varname='';
	public function html($data) {
		$html='';
		if(isset($data[$this->varname])) {
			$v=$data[$this->varname];
			if(is_array($v)) {
				foreach($v as $key=>$val) {
					if(is_array($val)) {
						$html.=parent::html(array_merge($data,$val));
					} else {
						$data['.']=$val;
						$html.=parent::html($data);
					}
				}
			} elseif ($v===true) {
				$html.=parent::html($data);
			} elseif ($v===false) {
			} else {
				$html.=$v;
				$html.=parent::html($data);
			}
		}
		return $html;
	}
}

class TextNode extends Node {
	public $contents='';
	protected function html_before($data) {
		return $this->contents;
	}
}
class HcommentNode extends Node {
	public function html($data) {
		$html='<!-- ';
		foreach($this->children as $c) {
			$html.=str_replace(array('--','>'),array('&#x2d;&#x2d;','&gt;'),$c->html($data));
		}
		$html.=' -->';
		return $html;
	}
}
class DoctypeNode extends Node {
	protected function html_before($data) {
		return '<!DOCTYPE html>';
	}
}

class CommentNode extends Node {
	public function html($data) {
		return ''; // don't touch children
	}
}

class HtagNode extends Node {
	public $attributes=array();
	public $name='div';
	protected function html_before($data) {
		return '<'.htmlspecialchars($this->name).''.$this->html_attr($data).'>';
	}
	protected function html_after($data) {
		return '</'.htmlspecialchars($this->name).'>';
	}
	private function html_attr($data) {
		$attr='';
		foreach($this->attributes as $key=>$val) {
			$attr.=' '.htmlspecialchars($key).'="';
			if(is_array($val)) {
				$attr.=htmlspecialchars(implode(' ',$val));
			} else {
				$attr.=htmlspecialchars($val);
			}
			$attr.='"';
		}
		return $attr;
	}
}


