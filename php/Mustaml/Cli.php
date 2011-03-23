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
			$html=$c->render($ast,$data)."\n";
			
			echo $html;
		}
	}
}