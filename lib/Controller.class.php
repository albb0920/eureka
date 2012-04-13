<?php
abstract class Controller{
    protected $pageName;
    protected $action;

    function __construct(){
	$this->pageName = strtolower(strstr(get_class($this),'Controller',true));
	$this->action = $_GET['action'];

	//TODO: Should we check whether it's public?
	if(!method_exists($this, $this->action))
            $this->action = 'index';
        
	$this->{$this->action}();
    }

    protected function render(){
	require "views/$this->pageName.$this->action.php";
    }

    abstract function index();
}
?>
