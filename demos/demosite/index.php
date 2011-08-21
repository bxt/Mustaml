<?php
/**
 * Demosite-Controller
 *
 * This script acts as a controlller for our demo site. It 
 * updates the model when the user submits forms and displays 
 * a success/error message. For the AJAX-requests it outputs 
 * this message directly, for "real" request it renders a 
 * full HTML page. 
 */

// Load model
include('Todos.php');

// Do some input processing
$error=false;
$did=false;
try {
	
	// Actions when we add a new item
	if(isset($_POST['text'])) {
		$did=true;
		// Some basic validation
		if(!empty($_POST['text'])) {
			Todos::add(false,$_POST['text']);
		} else {
			$error='Text was empty';
		}
	
	// Actions when (un)checking an item
	} elseif(isset($_POST['id'])) {
		$did=true;
		// Again, valdate the id.
		//  It could still be a non-existing one, but this
		//  will throw an exception, since it is not possible
		//  through our UI. 
		if(intval($_POST['id'])==$_POST['id']) {
			Todos::get(intval($_POST['id']))->setState(isset($_POST['state'])&&$_POST['state']=='X');
		} else $error='Id must be numeric!';
	}
} catch (Exception $e) {
	// If errors are not caught by our validation 
	//  and proceed to our model, we return a 
	//  generic error message. 
	$error='Unknown error';
}

// If it's an AJAX action, just return the message
//  and abort. 
if($did&&isset($_POST['ajax'])&&$_POST['ajax']=='true') {
	die($error?$error:'OK');
}

// When we get here, it's a full-page request
// so it's time to fire up Mustaml!

// Load Mustaml
include('../../mustaml.phar');

// Prepare out view data
$data=array(
	'jquery'=>'http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js',
	'items'=>Todos::all(),
	'did'=>$did,
	'error'=>$error,
);

// Configure a loader for our templates
$config=new \Mustaml\Html\CompilerConfig();
$config->registerAutoloader(new \Mustaml\Autoloaders\TemplateDirAl('tmpl'));

// Render a template which consists of a subtemplate call only
$main=new \Mustaml\Mustaml('-index.mustaml',$data,$config);
echo $main;


