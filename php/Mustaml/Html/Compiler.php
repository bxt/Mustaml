<?php
namespace Mustaml\Html;

class Compiler extends \Mustaml\Compiler\CompilerBase {
	protected function render_hcomment($ast,$data) {
		$this->sheduleEcho(' -->');
		$this->sheduleBufferPop('process_hcomment');
		$this->renderChildren($ast,$data);
		$this->sheduleBufferPush();
		$this->sheduleEcho('<!-- ');
	}
	protected function process_hcomment($contents) {
		return str_replace(array('--','>'),array('&#x2d;&#x2d;','&gt;'),$contents);
	}
	protected function render_doctype($ast,$data) {
		$this->sheduleEcho('<!DOCTYPE html>');
	}
	protected function render_htag($ast,$data) {
		$selfClose=(count($ast->children)==0&&$this->getConfig()->isHtmlSelfclosingTag($ast->name));
		if ($selfClose) {
			$this->sheduleEcho(' />');
		} else {
			$this->sheduleEcho('</'.htmlspecialchars($ast->name).'>');
			$this->renderChildren($ast,$data);
		}
		$this->sheduleEcho('<'.htmlspecialchars($ast->name).$this->html_attr($ast,$data).($selfClose?'':'>'));
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
					if($this->getConfig()->isHtmlArrayAttr($key)) {
						$attr.= ' '.htmlspecialchars($key).'="'.htmlspecialchars(implode(' ',$val)).'"';
					} else {
						$attr.=' '.htmlspecialchars($key).'="'.htmlspecialchars($val[count($val)-1]).'"';
					}
			}
		}
		return $attr;
	}
}
