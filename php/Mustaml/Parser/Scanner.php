<?php
namespace Mustaml\Parser;

/**
 * A base for building stream parsers
 * 
 * Crawl through a String step by step using
 * regexp-charclasses. 
 */
class Scanner {
	/**
	 * Holds the rest of the input yet to parse
	 */
	private $input;
	/**
	 * Initalize with input string (scanner can only be used once)
	 */
	public function __construct($input) {
		$this->input=$input;
		//$this->orig=$input;
	}
	/**
	 * Eats and returns next matching chars
	 * 
	 * Use lists of chars allowed as parameters. 
	 * When called without parameters matches the
	 * next input char. 
	 */
	public function get() {
		$a=func_get_args();
		if(count($a)==0) { // the next char:
			$c=isset($this->input[0])?$this->input[0]:false;
			$this->input=substr($this->input,1);
			return $c;
		}
		$new=call_user_func_array(array($this,'is'),$a);
		if($new) {
			$this->input=substr($this->input,strlen($new));
		} 
		return $new;
	}
	/**
	 * Eats and returns next non matching chars
	 * 
	 * Use lists of chars to stop at as parameters. 
	 * When called without parameters matches the
	 * whole rest of the input string. 
	 */
	public function getUnless() {
		$a=func_get_args();
		if(count($a)==0) { // everything
			$c=$this->input;
			$this->input='';
			return $c;
		}
		$a[0]='^'.$a[0];// not
		$new=call_user_func_array(array($this,'is'),$a);
		if($new) {
			$this->input=substr($this->input,strlen($new));
		} 
		return $new;
	}
	/**
	 * Eats and returns next matching char
	 * 
	 * This method does the same as get() except
	 * it will eat zero or one chars. 
	 * Use lists of chars allowed as parameters. 
	 * When called without parameters matches the
	 * next input char. 
	 */
	public function getOne() {
		$a=func_get_args();
		if(count($a)==0) { // the next char:
			return  call_user_func_array(array($this,'get'),$a);
		}
		$new=call_user_func_array(array($this,'is'),$a);
		if($new) {
			if(strlen($new)>1) $new=$new[0];
			$this->input=substr($this->input,strlen($new));
		} 
		return $new;
	}
	/**
	 * Returns next matching chars
	 * 
	 * This method does the same as get() except
	 * it will not eat chars. 
	 * Use lists of chars allowed as parameters. 
	 * When called without parameters matches the
	 * next input char. 
	 */
	public function is() {
		$a=func_get_args();
		if(strlen($this->input)<=0) return false;
		if(count($a)==0) return true;
		if(preg_match('/^['.implode('',$a).']+/',$this->input,$m)===1) {
			return $m[0];
		}
		return false;
	}
}
