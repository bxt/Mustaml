<?php
namespace Mustaml;

//xdebug_start_trace("mustaml");

init();

if(isset($argv[0])&&__FILE__==realpath($argv[0])) {
	Cli::run($argv);
}

function init(){
	if (version_compare('5.3.0', PHP_VERSION, '>')) {
			die("mustaml's phar requires PHP 5.3.0 or newer. \n");
	}
	if (!extension_loaded('phar')) {
			die("mustaml's phar requires PHAR Extension. \n");
	}
	if(!\phar::canCompress()) {
			die("mustaml's phar requires PHAR Extension with compression support. \n");
	}
	
	if(strrchr(__FILE__, '.')==".phar") {
		$path='phar://'.__FILE__.'/';
	} else {
		$path=__DIR__.'/';
	}
	
	if(!class_exists("\\Bxt\\ClassLoader")) {
		require $path.'lib/Bxt/ClassLoader.php';
	}
	$l_own=new \Bxt\ClassLoader(__NAMESPACE__,$path.'php/');
	$l_own->register();
	$l_lib=new \Bxt\ClassLoader(null,$path.'lib/');
	$l_lib->register();
}

__HALT_COMPILER(); ?>