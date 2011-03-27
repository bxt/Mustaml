<?php
namespace Mustaml\Html;

/**
 * Holds and manages options for parsing AST>HTML
 */
class CompilerConfig extends \Mustaml\Compiler\CompilerConfig {
	/**
	 * Holds a set of html attributes that contain space-separated values
	 */
	private $htmlArrayAttrs;
	/**
	 * Holds a set of html tag that should be self-closing
	 */
	private $htmlSelfclosingTags;
	/**
	 * Initialize with autoloaders, list of self-closing tag names, list of space-separated values contaning attributes
	 */
	public function __construct($htmlSelfclosingTags=null,$htmlArrayAttrs=null) {
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
	}
	/**
	 * Returns if an attribute does or not contain space-separated values
	 */
	public function isHtmlArrayAttr($attr) {
		return isset($this->htmlArrayAttrs[$attr]);
	}
	/**
	 * Returns if or not an html tag should be self-closing
	 */
	public function isHtmlSelfclosingTag($tag) {
		return isset($this->htmlSelfclosingTags[$tag]);
	}
}
