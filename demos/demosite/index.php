<?php

include('Todos.php');

$error=false;
$did=false;
try {
	if(isset($_POST['text'])) {
		$did=true;
		if(!empty($_POST['text'])) {
			Todos::add(false,$_POST['text']);
		} else {
			$error='Text was empty';
		}
	} elseif(isset($_POST['id'])) {
		$did=true;
		if(intval($_POST['id'])==$_POST['id']) {
			Todos::get(intval($_POST['id']))->setState(isset($_POST['state'])&&$_POST['state']=='X');
		} else $error='Id must be numeric!';
	}
} catch (Exception $e) {
	$error='Unknown error';
}

if($did&&isset($_POST['ajax'])&&$_POST['ajax']=='true') {
	die($error?$error:'OK');
}

include('../../mustaml.php');

$data=array(
'jquery'=>'http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js',
'items'=>Todos::all(),
'did'=>$did,
'error'=>$error,
);

$config=new \Mustaml\Html\CompilerConfig();
$config->registerAutoloader(new \Mustaml\Autoloaders\TemplateDirAl('tmpl'));
$main=new \Mustaml\Mustaml('-index.mustaml',$data,$config);
echo $main;


