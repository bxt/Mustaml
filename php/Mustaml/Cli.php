<?php
namespace Mustaml;

class Cli {
	static function run($argv) {
		$script=array_shift($argv);
		if(isset($argv[0])&&isset($argv[1])) {
			$data=json_decode(file_get_contents($argv[0]),true);
			$templateString=file_get_contents($argv[1]);
			$p=new Parser();
			$ast=$p->parseString($templateString);
			$c=new HtmlCompiler();
			//var_dump($c->render($ast,$data));
			echo md5($c->render($ast,$data))."\n";
			echo md5($ast->html($data))."\n";
		}
	}
}