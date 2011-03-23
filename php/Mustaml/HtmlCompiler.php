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
	private function render_val($ast,$data) {
		$html='';
		if(isset($data[$ast->varname])) {
			$v=$data[$ast->varname];
			if(is_array($v)) {			
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
		$attr_array=array();
		if(isset($ast->attributes['other'])) {
			$attr_array=$ast->attributes['other'];
		}
		if(isset($ast->attributes['val'])&&isset($data[$ast->attributes['val']])) {
			if(is_array($data[$ast->attributes['val']])) {
				$attr_array=array_merge($attr_array,$data[$ast->attributes['val']]);
			} else {
				$attr.=(string)$data[$ast->attributes['val']];
			}
		}
		/// @TODO: Don't override all classes
		if(isset($ast->attributes['classes'])) {
			$attr_array['class']=$ast->attributes['classes'];
		}
		foreach($attr_array as $key=>$val) {
			$attr.=' '.htmlspecialchars($key).'="';
			if(is_array($val)) {
				$attr.=htmlspecialchars(implode(' ',$val));
			} elseif($val===true) {
				$attr.=htmlspecialchars($key);
			} elseif($val===false) {
				$attr.='';
			} else {
				$attr.=htmlspecialchars($val);
			}
			$attr.='"';
		}
		return $attr;
	}
}
