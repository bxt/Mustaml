<?php
namespace Mustaml\Autoloaders;

class TemplateDirAutoloader implements AutoloaderI {
	private $templateDir;
	private $mustamlBoilerplate;
	public function __construct($dir,$mustamlBoilerplate=null) {
		$this->templateDir=$dir;
		$this->mustamlBoilerplate=$mustamlBoilerplate ?: new \Mustaml\Mustaml('');
	}
	public function autoload($key) {
		if(preg_match('/\.mustaml$/i',$key)) {
			// only for vars like *.mustaml to avoid loading random stuff
			$possibleTemplateString=@file_get_contents($this->templateDir.'/'.$key);
			if(!$possibleTemplateString) {
				return null;
			} else {
				$v=$this->mustamlBoilerplate->getWithTemplate($possibleTemplateString);
				return $v;
			}
		}
	}
	public function setMustamlBoilerplate($mustamlBoilerplate) {
		$this->mustamlBoilerplate=$mustamlBoilerplate;
	}
}
