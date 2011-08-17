<?php
namespace Mustaml\Autoloaders;

/**
 * Loads *.mustaml files from a certain directory when used in template
 *
 * If you use -foo.mustaml it will look for $dir/foo.mustaml, parse
 * it, and looks if there is a $dir/foo.json with valid JSON, if so
 * it's contents will override current scope for foo template. 
 * Then renders foo.mustaml and returns it's values. 
 * Inside the foo template you can render possible subnotes
 * of the caller as minus, which will then first of all have
 * the outer scope vairables, but also stuff from foo.json
 * addded if they don't collide. 
 */
class TemplateDirAl implements AutoloaderI,MustamlDependentAlI {
	/**
	 * Holds a path (without trailing /) to get the templates from
	 * @var String
	 */
	private $templateDir;
	/**
	 * Holds a Mustaml object that can create new Mustaml objects with
	 * same properties
	 * @var \Mustaml\Mustaml
	 */
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
