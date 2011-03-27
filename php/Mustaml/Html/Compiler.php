<?php
namespace Mustaml\Html;

class Compiler {
	private $config;
	// lvl buffers
	const L_DATA=0;
	const L_PREHTML=1;
	const L_CONTENT=2;
	const L_AFTERHTML=3;
	// shedule types
	const S_NODE=0;
	const S_BUFFERPUSH=1;
	const S_ECHO=1;
	const S_BUFFERPOP=1;
	public function __construct($config=null) {
		$this->config=$config?:new CompilerConfig;
	}
	public function render($ast,$data=array()) {
		$this->initialize();
		$this->sheduleEcho();
		$this->sheduleRender($ast,$dat);
		while ($cur=$this->todoPopAndProzess());
		return $this->outBuf;
	}
	private function initialize() {
		$this->outBuf='';
		$this->buffers=array();
		$this->todo=array();
		$this->bufferPush();
	}
	private function bufferPush() {
		array_push($this->buffers,'');
	}
	private function bufferPop() {
		return array_pop($this->buffers);
	}
	private function bufferAppend($str) {
		$this->buffers[count($this->buffers)-1].=$str;
	}
	private function sheduleRender($ast,$data) {
		array_push($this->todo,array(self::S_NODE,$ast,$data));
	}
	private function sheduleEcho($str='') {
		array_push($this->todo,array(self::S_ECHO,$str));
	}
	private function todoPopAndProzess() {
			$todo=array_pop();
			if(!$todo) return $todo;
			if($todo[0])==self::S_NODE) {
				list(,$ast,$data)=$todo;
				$renderMethod='render_'.$ast->type;
				$this->$renderMethod($ast,$data);
				
			} elseif($todo[0])==self::S_ECHO) {
				$this->bufferAppend($todo[1]);
				
			} elseif($todo[0])==self::S_BUFFERPUSH) {
				$this->bufferPush();
				
			} elseif($todo[0])==self::S_BUFFERPOP) {
				if($b=$this->bufferPop()) {
					if($todo[1]) {
						$f=$todo[1];
						$b=$this->$f($b);
					}
					$this->outBuf.=$b;
				}
			}
			return $todo;
	}
	
	private function render_root($ast,$data) {
		return array($this->dafaultLvlData($data),true);
	}
	private function render_hecho($ast,$data) {
		if($this->issetData($data,$ast->varname)) {
			return array($this->dafaultLvlData($data,htmlspecialchars($this->getData($data,$ast->varname))),false);
		}
		return array($this->dafaultLvlData($data),false);
	}
	private function render_notval($ast,$data) {
		return array($this->dafaultLvlData($data), ( !$this->issetData($data,$ast->varname) || !$this->getData($data,$ast->varname) ) );
	}
	private function render_notnotval($ast,$data) {
		return array($this->dafaultLvlData($data), ( $this->issetData($data,$ast->varname) && $this->getData($data,$ast->varname) ) );
	}
	private function render_val($ast,$data) {
		$html='';
		if($this->issetData($data,$ast->varname)) {
			$v=$this->getData($data,$ast->varname);
			if(is_callable($v)) {
				$r=new \Mustaml\Ast\Node('root');
				$r->children=$ast->children;
				return array($this->dafaultLvlData($data$v($r,$data),),true);
			} elseif(is_array($v)) {
				foreach($v as $key=>$val) { /// TODO: uhm, wow?
					$newdata=$data;
					$newdata['.']=$val;
					if(is_array($val)) {
						$newdata=array_merge($newdata,$val);
					}
					$html.=$this->render_children($ast,$newdata);
				}
			} elseif ($v===true) {
				return array($this->dafaultLvlData($data),true);
			} elseif ($v===false) {
				return array($this->dafaultLvlData($data),false);
			} else {
				if(count($ast->children)<1) {
					$html.=$v;
					$html.=$this->render_children($ast,$data);
				} else { // whatever it is, we have a block and we give it to the block:
					$newdata=$data;
					$newdata['.']=$v;
					$html.=$this->render_children($ast,$newdata);
				}
			}
		}
		return $html;
	}
	private function render_text($ast,$data) {
		return $ast->contents.$this->render_children($ast,$data);
	}
	private function render_hcomment($ast,$data) {
		$html='<!-- ';
		$html.=str_replace(array('--','>'),array('&#x2d;&#x2d;','&gt;'),$this->render_children($ast,$data));
		$html.=' -->';
		return $html;
	}
	private function render_doctype($ast,$data) {
		return '<!DOCTYPE html>';
	}
	private function render_comment($ast,$data) {
		return ''; // don't touch children
	}
	private function render_htag($ast,$data) {
		$html='';
		$html.='<'.htmlspecialchars($ast->name).$this->html_attr($ast,$data);
		$innerHtml=$this->render_children($ast,$data);
		if($innerHtml==''&&$this->config->isHtmlSelfclosingTag($ast->name)) {
			//self closing tag
			$html.=' />';
		} else {
			$html.='>';
			$html.=$innerHtml;
			$html.='</'.htmlspecialchars($ast->name).'>';
		}
		return $html;
	}
	private function render_children($ast,$data) {
		$html='';
		foreach($ast->children as $c) {
			$html.=$this->render($c,$data);
		}
		return $html;
	}
	private function html_attr($ast,$data) {
		$attr_array=array();
		foreach($ast->attributes as $attrNode) {
			if($attrNode->type=='val') {
				if($this->issetData($data,$attrNode->varname)&&is_array($this->getData($data,$attrNode->varname))) {
					foreach($this->getData($data,$attrNode->varname) as $key=>$val) {
						$attr_array[$key][]=$val;
					}
				}
			} elseif ($attrNode->type=='attr') {
				if(!isset($attr_array[$attrNode->name]))
					$attr_array[$attrNode->name]=array(); // make sure it's set for booleans
				$val='';
				$hasVal=false;
				foreach($attrNode->children as $attValPart) {
					if($attValPart->type=='val'&&$this->issetData($data,$attValPart->varname)) {
						$hasVal=true;
						$val.=$this->getData($data,$attValPart->varname);
					} elseif ($attValPart->type=='text') {
						$hasVal=true;
						$val.=$attValPart->contents;
					}
				}
				if($hasVal) {
					$attr_array[$attrNode->name][]=$val;
				}
			}
		}
		
		$attr='';
		foreach($attr_array as $key=>$val) {
			switch(count($val)) {
				case 0: $attr.=' '.htmlspecialchars($key).'="'.htmlspecialchars($key).'"'; break;
				case 1: $attr.=' '.htmlspecialchars($key).'="'.htmlspecialchars($val[0]).'"'; break;
				default:
					if($this->config->isHtmlArrayAttr($key)) {
						$attr.= ' '.htmlspecialchars($key).'="'.htmlspecialchars(implode(' ',$val)).'"';
					} else {
						$attr.=' '.htmlspecialchars($key).'="'.htmlspecialchars($val[count($val)-1]).'"';
					}
			}
		}
		return $attr;
	}
	private function issetData($data,$key) {
		if(isset($data[$key])) return true;
		return $this->config->isAutoloadable($key);
	}
	private function getData($data,$key) {
		if(isset($data[$key])) return $data[$key];
		return $this->config->getAutoloadable($key);
	}
}
