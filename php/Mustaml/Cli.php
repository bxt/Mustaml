<?php
namespace Mustaml;

/**
 * Functions needed for invoking from CLI
 * @see run()
 */
class Cli {
	/**
	 * Cli endpoint called with CLI args
	 * 
	 * When rendering form CLI we expect a template
	 * file as input and optionally a valid json file
	 * of input data. 
	 * We provide Autoloaders, so xyz.mustaml in the
	 * Mustaml-Template refers to that file either in
	 * the the temlate file's directory, or if absent
	 * there in the current working directory. 
	 * Autoloaded templates will be called with the 
	 * data in their corresponing xyt.json file. 
	 */
	static function run($argv) {
		$script=array_shift($argv);
		if(!isset($argv[0])) {
			echo 'Need at least 1 Parameter'."\n";
			return 0;
		}
		if(isset($argv[0])&&isset($argv[1])) {
			$filename=$argv[1];
			$data=json_decode(file_get_contents($argv[0]),true);
			if($data===null) throw new \Exception("Invalid JSON!");
		} else {
			$filename=$argv[0];
			$data=array();
		}
		$templateString=file_get_contents($filename);
		
		$config=new Html\CompilerConfig();
		$config->registerAutoloader(new Autoloaders\TemplateDirAl('.'));
		if(dirname($filename)!='.') {
			$config->registerAutoloader(new Autoloaders\TemplateDirAl(dirname($filename)));
		}
		
		$mustaml=new Mustaml($templateString,$data,$config);
		echo $mustaml();
		echo "\n";
		
		return 1;
	}
}