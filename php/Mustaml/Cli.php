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
			$filename=$argv[1];
			$data=json_decode(file_get_contents($argv[0]),true);
			if($data===null) throw new \Exception("Invalid JSON!");
		} else {
			$filename=$argv[0];
			$data=array();
		}
		$templateString=file_get_contents($filename);
		
		$alList=array();
		$pwd_al=new Autoloaders\TemplateDirAl('.');
		array_push($alList,$pwd_al);
		if(dirname($filename)!='.') {
			$filedir_al=new Autoloaders\TemplateDirAl(dirname($filename));
			array_push($alList,$filedir_al);
		}
		$config=new Html\CompilerConfig($alList);
		$al_bp=new Mustaml('',array(),$config);
		$pwd_al->setMustamlBoilerplate($al_bp);
		if(isset($filedir_al)) {
			$filedir_al->setMustamlBoilerplate($al_bp);
		}
		
		$p=new Parser();
		$ast=$p->parseString($templateString);
		//var_dump($ast);
		$c=new Html\Compiler($config);
		$html=$c->render($ast,$data)."\n";
		
		echo $html;
		return 1;
	}
}