<?php
namespace Mustaml\Autoloaders;

/**
 * Loads *.mustaml files from a certain directory when used in template
 */
class TemplateDirAl implements AutoloaderI,MustamlDependentAlI {
	private $templateDir;
	private $mustamlBoilerplate;
	/**
	 * Initialite with the template dir and optionally a mustaml class to use for rendering the loaded templates
	 */
	public function __construct($dir,$mustamlBoilerplate=null) {
		$this->templateDir=$dir;
		$this->mustamlBoilerplate=$mustamlBoilerplate ?: new \Mustaml\Mustaml('');
	}
	/**
	 * Returns a Mustaml for a specified filename, looking through template dir
	 */
	public function autoload($key) {
		if(preg_match('/^(.*)\.mustaml$/i',$key,$m)) {
			// only for vars like *.mustaml to avoid loading random stuff
			
			$data=array();
			$possibleJSONString=@file_get_contents($this->templateDir.'/'.$m[1].'.json');
			if($possibleJSONString) {
				$jsonData=json_decode($possibleJSONString,true);
				if(is_array($jsonData)) $data=$jsonData;
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
	/**
	 * Sets the mustaml class to use for rendering the loaded templates
	 */
	public function setMustamlBoilerplate($mustamlBoilerplate) {
		$this->mustamlBoilerplate=$mustamlBoilerplate;
	}
}
