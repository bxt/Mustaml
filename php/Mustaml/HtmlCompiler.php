<?php
namespace Mustaml;

class HtmlCompiler {
	public function render($ast,$data=array()) {
		$renderMethod='render_'.$ast->type;
		return $this->$renderMethod($ast,$data);
	}
	private function render_root($ast,$data) {
		return $this->render_children($ast,$data);
	}
	private function render_hecho($ast,$data) {
		if(isset($data[$ast->varname])) {
			return htmlspecialchars($data[$ast->varname]);
		}
		return '';
	}
	private function render_notval($ast,$data) {
		if( !isset($data[$ast->varname]) || empty($data[$ast->varname]) ) {
			return $this->render_children($ast,$data);
		}
		return '';
	}
	private function render_notnotval($ast,$data) {
		if( isset($data[$ast->varname]) && !empty($data[$ast->varname]) ) {
			return $this->render_children($ast,$data);
		}
		return '';
	}
	private function render_val($ast,$data) {
		$html='';
		if(isset($data[$ast->varname])) {
			$v=$data[$ast->varname];
			if(is_callable($v)) {
				$r=new Ast\Node('root');
				$r->children=$ast->children;
				return $v($r,$data);
			} elseif(is_array($v)) {			
				foreach($v as $key=>$val) {
					$newdata=$data;
					$newdata['.']=$val;
					if(is_array($val)) {
						$newdata=array_merge($newdata,$val);
					}
					$html.=$this->render_children($ast,$newdata);
				}
			} elseif ($v===true) {
				$html.=$this->render_children($ast,$data);
			} elseif ($v===false) {
			} else {
				$html.=$v;
				$html.=$this->render_children($ast,$data);
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
		$html.='<'.htmlspecialchars($ast->name).$this->html_attr($ast,$data).'>';
		$html.=$this->render_children($ast,$data);
		$html.='</'.htmlspecialchars($ast->name).'>';
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
				if(isset($data[$attrNode->varname])&&is_array($data[$attrNode->varname])) {
					foreach($data[$attrNode->varname] as $key=>$val) {
						$attr_array[$key][]=$val;
					}
				}
			} elseif ($attrNode->type=='attr') {
				if(!isset($attr_array[$attrNode->name]))
					$attr_array[$attrNode->name]=array(); // make sure it's set for booleans
				$val='';
				$hasVal=false;
				foreach($attrNode->children as $attValPart) {
					if($attValPart->type=='val'&&isset($data[$attValPart->varname])) {
						$hasVal=true;
						$val.=$data[$attValPart->varname];
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
					if($key=='class') { /// TODO: Array of multi-value attrs
						$attr.= ' '.htmlspecialchars($key).'="'.htmlspecialchars(implode(' ',$val)).'"';
					} else {
						$attr.=' '.htmlspecialchars($key).'="'.htmlspecialchars($val[count($val)-1]).'"';
					}
			}
		}
		return $attr;
	}
}
