<?php
/* Global view helpers */
require_once 'config.inc.php';

/* generate <img> tag */
function img_tag($src,$options=array()){
    $options = array_merge(array('alt' =>''),$options);
    echo "<img src=\"$src\"";
    _html_options($options);
    echo '>';
}

function _html_options($options){
    foreach($options as $key => $value){
	echo ' '.$key.'="'.$value.'"';
    }
}

?>
