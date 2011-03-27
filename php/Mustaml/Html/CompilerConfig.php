<?php
namespace Mustaml\Html;

class CompilerConfig extends \Mustaml\Compiler\CompilerConfig {
	private $htmlArrayAttrs;
	private $htmlSelfclosingTags;
	public function __construct($valueAutoloaders=array(),$htmlSelfclosingTags=null,$htmlArrayAttrs=null) {
		$at=$htmlArrayAttrs?:array('class','rel','rev');
		$this->htmlArrayAttrs=array_fill_keys($at,true); // ~= convert list to set
		
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
		$ct=$htmlSelfclosingTags?:array('br','img','input','meta','link','hr','frame','param');
		$this->htmlSelfclosingTags=array_fill_keys($ct,true);
		
		parent::__construct($valueAutoloaders);
	}
	public function isHtmlArrayAttr($attr) {
		return isset($this->htmlArrayAttrs[$attr]);
	}
	public function isHtmlSelfclosingTag($tag) {
		return isset($this->htmlSelfclosingTags[$tag]);
	}
}
