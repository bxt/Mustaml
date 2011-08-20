<?php

class Todos {
	private static $datafile='todos.txt';
	private static $data=null;
	public static function all() {
		self::load();
		return self::$data;
	}
	public static function get($id) {
		self::load();
		return self::$data[$id];
	}
	public static function add($s,$t) {
		self::load();
		$i=count(self::$data);
		$new=new self($i,$s,$t);
		$fp=@fopen(self::$datafile,'a');
		if(!$fp) throw new Exception('Could not write data file!');
		fwrite($fp,$new);
		fclose($fp);
		self::$data[]=$new;
		return $i;
	}
	private static function load() {
		if(self::$data===null) {
			self::$data=array();
			$lines=@file(self::$datafile);
			if(!$lines) return;
			for($i=0,$len=count($lines);$i<$len;$i++) {
				if(preg_match('/^(X| ) (.*)/',$lines[$i],$t)) {
					if(count($t)==3) {
						self::$data[]=new self($i,$t[1]=='X',$t[2]);
					}
				}
			}
		}
	}
	private static function save() {
		if(self::$data===null) throw new Excepton('Save before load!');
		$fp=fopen(self::$datafile,'w');
		if(!$fp) throw new Exception('Could not write data file!');
		foreach (self::$data as $item) {
			fwrite($fp,$item);
		}
		fclose($fp);
	}
	
	private $state;
	private $text;
	private $id;
	private function __construct($i,$s,$t) {
		$this->id=$i;
		$this->state=!!$s;
		$this->text=(string)$t;
	}
	public function setState($s) {
		$this->state=!!$s;
		self::save();
		return $this;
	}
	public function setText($t) {
		$this->text=(string)$t;
		self::save();
		return $this;
	}
	public function getState() {
		return $this->state;
	}
	public function getText() {
		return $this->text;
	}
	public function getId() {
		return $this->id;
	}
	public function __toString() {
		return ($this->state?'X':' ').' '.$this->text."\n";
	}
}