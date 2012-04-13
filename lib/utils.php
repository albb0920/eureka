<?php
/* Utility functions */

// Return string tail from needle, this is used to generate find_by_* helpers
function tail($haystack, $needle){
    $length = strlen($needle);
    if(substr($haystack, 0, $length) === $needle)
	return substr($haystack, $length); 
    else
	return false;
}

// Sanitize html
function s($html){
    return htmlentities($html, ENT_COMPAT, 'UTF-8');
}

// Return the first argument that isn't empty
function any(/* ... */){
    foreach(func_get_args() as $arg){
	if($arg != '')
	    return $arg;
    }
}

?>
