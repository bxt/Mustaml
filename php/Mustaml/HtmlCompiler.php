<?php
namespace Mustaml;

class HtmlCompiler {
	final public function render($ast,$data=array()) {
		$renderMethod='render_'.lcfirst(preg_replace('/Node$|^Mustaml\\\\/','',get_class($ast)));
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
		if(!isset($data[$ast->varname]) || $data[$ast->varname]===false) {
			return $this->render_children($ast,$data);
		}
		return '';
	}
	private function render_val($ast,$data) {
		$html='';
		if(isset($data[$ast->varname])) {
			$v=$data[$ast->varname];
			if(is_array($v)) {
				foreach($v as $key=>$val) {
					if(is_array($val)) {
						$html.=$this->render_children($ast,array_merge($data,$val));
					} else {
						$data['.']=$val;
						$html.=$this->render_children($ast,$data);
					}
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
		$html.='<'.htmlspecialchars($ast->name).''.$this->html_attr($ast,$data).'>';
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
		$attr='';
		foreach($ast->attributes as $key=>$val) {
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
