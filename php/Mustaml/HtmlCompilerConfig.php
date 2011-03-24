<?php
namespace Mustaml;

class HtmlCompilerConfig {
	private $htmlArrayAttrs;
	private $htmlSelfclosingTags;
	private $valueAutoloader;
	private $autoloadedValues=array();
	public function __construct($valueAutoloader=null,$htmlSelfclosingTags=null,$htmlArrayAttrs=null) {
		$at=$htmlArrayAttrs?:array('class','rel','rev');
		$this->htmlArrayAttrs=array_fill_keys($at,true); // ~= convert list to set
		
		$ct=$htmlSelfclosingTags?:array('br','img','input','meta','link','hr','frame','param');
		/*
		 * A word about self closing tags:
		 * 
		 * Not using self-closing tags can be confusing, 
		 * and leed to errors, say <br></br>. 
		 * However using only <br> would break our
		 * outpur for XHTML. And in HTML5 the <br /> are
		 * allowed too, so it the beast way to go. 
		 * We need this list, since there are tags
		 * like <script> that go rouge when self-closed. 
		 */
		$this->htmlSelfclosingTags=array_fill_keys($ct,true);
		
		$this->valueAutoloader=$valueAutoloader;
	}
	public function isHtmlArrayAttr($attr) {
		return isset($this->htmlArrayAttrs[$attr]);
	}
	public function isHtmlSelfclosingTag($tag) {
		return isset($this->htmlSelfclosingTags[$tag]);
	}
	public function isAutoloadable($key) {
		if(isset($this->autoloadedValues[$key])) return true;
		if(!$this->valueAutoloader) return false;
		$loaded=call_user_func_array($this->$valueAutoloader,array($key));
		if($loaded!==null) {
			$this->autoloadedValues[$key]=$loaded;
			return true;
		}
		return false;
	}
	public function getAutoloadable($key) {
		if(!isAutoloadable($key)) throw new \OutOfRangeException("Tried to autoload an not-autoloadable key. ");
		return $this->autoloadedValues[$key];
	}
}
