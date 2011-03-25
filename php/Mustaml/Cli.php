<?php
namespace Mustaml;

class Cli {
	static function run($argv) {
		$script=array_shift($argv);
		if(!isset($argv[0])) {
			echo 'Need at least 1 Parameter'."\n";
			return 0;
		}
		if(isset($argv[0])&&isset($argv[1])) {
			$templateString=file_get_contents($argv[1]);
			$data=json_decode(file_get_contents($argv[0]),true);
			if($data===null) throw new \Exception("Invalid JSON!");
		} else {
			$templateString=file_get_contents($argv[0]);
			$data=array();
		}
		
		$al=new Autoloaders\TemplateDirAutoloader('.'); //pwd
		$config=new HtmlCompilerConfig(array(array($al,'autoload')));
		$al_bp=new Mustaml('',array(),$config);
		$al->setMustamlBoilerplate($al_bp);
		
		$p=new Parser();
		$ast=$p->parseString($templateString);
		//var_dump($ast);
		$c=new HtmlCompiler($config);
		$html=$c->render($ast,$data)."\n";
		
		echo $html;
		return 1;
	}
}