<?php
namespace Mustaml\Autoloaders;

class TemplateDirAl implements AutoloaderI {
	private $templateDir;
	private $mustamlBoilerplate;
	public function __construct($dir,$mustamlBoilerplate=null) {
		$this->templateDir=$dir;
		$this->mustamlBoilerplate=$mustamlBoilerplate ?: new \Mustaml\Mustaml('');
	}
	public function autoload($key) {
		if(preg_match('/^(.*)\.mustaml$/i',$key,$m)) {
			// only for vars like *.mustaml to avoid loading random stuff
			
			$data=array();
			$possibleJSONString=@file_get_contents($this->templateDir.'/'.$m[1].'.json');
			if($possibleJSONString) {
				$jsonData=json_decode($possibleJSONString,true);
				if($data!==null) $data=$jsonData;
			}
			
			$possibleTemplateString=@file_get_contents($this->templateDir.'/'.$key);
			if(!$possibleTemplateString) {
				return null;
			} else {
				$v=$this->mustamlBoilerplate->getWithTemplate($possibleTemplateString,$data);
				return $v;
			}
		}
	}
	public function setMustamlBoilerplate($mustamlBoilerplate) {
		$this->mustamlBoilerplate=$mustamlBoilerplate;
	}
}
