<?php
set_include_path(get_include_path().PATH_SEPARATOR.dirname(__FILE__));

require_once 'config.inc.php';
require_once 'helpers/global.php';
require_once 'lib/utils.php';

session_start();

/* global helpers */
function config($name){
    global $config;
    $r = $config;
    $path = explode('.',$name);

    foreach($path as $i){
	$r = $r[$i];
    }
    return $r;
}

/* magic functions */
function __autorun(){
    $path = pathinfo($_SERVER['SCRIPT_NAME']);
    $class_name =  ucfirst($path['filename']).'Controller';

    // We need to create instance after class is defined
    include(basename($_SERVER['SCRIPT_NAME']));
    if(class_exists($class_name))
	new $class_name;

    exit();
}

function __autoload($class_name){
    if(!@include_once("models/$class_name.php"))
	@include_once("lib/$class_name.class.php");
}
spl_autoload_register('__autoload');

if($config['db'])
	DBRecord::connect();

/* autorun page controller */
__autorun();
?>
