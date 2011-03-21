<?php
namespace Mustaml;

class Cli {
	static function run($argv) {
		$script=array_shift($argv);
		if(isset($argv[0])&&isset($argv[1])) {
			$data=json_decode(file_get_contents($argv[0]),true);
			$templateString=file_get_contents($argv[1]);
			
			// Bench:
			/*
			$pr=new \Profile('bench.csv');
			for($i=0;$i<10000;$i++) {
				$p=new Parser();
				$ast=$p->parseString($templateString);
			}
			$pr->end("parse4htmlclass x $i");
			$pr=new \Profile('bench.csv');
			for($i=0;$i<10000;$i++) {
				$c=new HtmlCompiler();
				$c->render($ast,$data);
			}
			$pr->end("htmlclass x $i");
			*/
			
			// Normal run:
			
			$p=new Parser();
			$ast=$p->parseString($templateString);
			$c=new HtmlCompiler();
			$c->render($ast,$data)."\n";
			
			
			// Test:
			//echo md5($c->render($ast,$data))."\n";
			//var_dump($c->render($ast,$data));
		}
	}
}