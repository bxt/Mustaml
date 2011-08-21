<?php

/**
 * Demosite-Model
 *
 * This is our todo-item model. It will
 * represent one item when instatiated
 * and at the same time staticly manage
 * and persist the items for us. 
 */
class Todos {
	
	/**
	 * Which file to use for saving
	 * @var string
	 */
	private static $datafile='todos.txt';
	
	/**
	 * Holds all the items, once loaded
	 * @var string
	 */
	private static $data=null;
	
	/**
	 * List all items
	 *
	 * @return array List of Todos
	 */
	public static function all() {
		self::load();
		return self::$data;
	}
	
	/**
	 * Get one item by id
	 *
	 * @param int Id of the item
	 * @return Todos wanted item
	 */
	public static function get($id) {
		self::load();
		return self::$data[$id];
	}
	
	/**
	 * Add a new item
	 *
	 * @param bool is item checked
	 * @param string name of item
	 * @return int Id of the new item
	 */
	public static function add($s,$t) {
		self::load();
		$i=count(self::$data);
		$new=new self($i,$s,$t);
		// We write the new item to the file immediately
		$fp=@fopen(self::$datafile,'a');
		if(!$fp) throw new Exception('Could not write data file!');
		fwrite($fp,$new);
		fclose($fp);
		self::$data[]=$new;
		return $i;
	}
	
	/**
	 * Fill self::$data according to file contents
	 */
	private static function load() {
		if(self::$data===null) {
			self::$data=array();
			$lines=@file(self::$datafile);
			if(!$lines) return;
			// Parse all line's content and construct the items
			for($i=0,$len=count($lines);$i<$len;$i++) {
				if(preg_match('/^(X| ) (.*)/',$lines[$i],$t)) {
					if(count($t)==3) {
						self::$data[]=new self($i,$t[1]=='X',$t[2]);
					}
				}
			}
		}
	}
	
	/**
	 * Write items in self::$data back to file
	 */
	private static function save() {
		if(self::$data===null) throw new Excepton('Save before load!');
		$fp=fopen(self::$datafile,'w');
		if(!$fp) throw new Exception('Could not write data file!');
		foreach (self::$data as $item) {
			fwrite($fp,$item); // we use the __toString() here
		}
		fclose($fp);
	}
	
	
	   ////          * * *              ////
	  ////  now the non-static stuff:  ////
	 ////          * * *              ////
	
	/**
	 * If checked or not
	 * @var bool
	 */
	private $state;
	
	/**
	 * Text to display
	 * @var bool
	 */
	private $text;
	
	/**
	 * The id of the item (readonly)
	 * @var bool
	 */
	private $id;
	
	/**
	 * The constructor gets called by our management-functions
	 * 
	 * @param int id
	 * @param bool checked or not
	 * @param string the display text
	 */
	private function __construct($i,$s,$t) {
		$this->id=$i;
		$this->state=!!$s;
		$this->text=(string)$t;
	}
	
	/**
	 * Sets the checked state
	 *
	 * This setter saves to the file
	 * @param bool checked or not
	 * @return Todos this (chainable)
	 */
	public function setState($s) {
		$this->state=!!$s;
		self::save();
		return $this;
	}
	
	/**
	 * Sets the the display text
	 *
	 * This setter saves to the file
	 * @param string the display text
	 * @return Todos this (chainable)
	 */
	public function setText($t) {
		$this->text=(string)$t;
		self::save();
		return $this;
	}
	
	/**
	 * Returns the checked state
	 *
	 * This getter is called by mustaml
	 * @return bool the state
	 */
	public function getState() {
		return $this->state;
	}
	
	/**
	 * Returns the display text
	 *
	 * This getter is called by mustaml
	 * @return string the text
	 */
	public function getText() {
		return $this->text;
	}
	
	/**
	 * Returns the id
	 *
	 * This getter is called by mustaml
	 * @return int the id
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Returns the representation as in the data file
	 * @return string In the format 'X [text]'
	 */
	public function __toString() {
		return ($this->state?'X':' ').' '.$this->text."\n";
	}
}