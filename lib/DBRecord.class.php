<?php
class DBRecord{
    /* Share a static db connection over all model */
    public static $db;
    static $tableName;

    //Query scope
    static $scope;
    public $columns = array();

    function __construct(){
    }

    function __set($property, $value){
	$this->columns[$property] = $value;
    }

    function __get($property){
	return $this->columns[$property];
    }

    function __unset($property){
	unset($this->columns[$property]);
    }

    function __isset($property){
	return isset($this->columns['property']);
    }

    static function __callStatic($name, $arguments){
	// finder
	if($tail = tail($name, 'find_by_')){
	    $smt = self::$db->prepare('SELECT * FROM '.static::tableName().' WHERE '.$tail.'=?');
	    $smt->bindParam(1, $arguments[0]);
	    if($smt->execute() === false)
		self::dumpErr($smt);

	    return $smt->fetchAll(PDO::FETCH_CLASS, get_called_class());
	}else if($tail = tail($name, 'search_by_')){
	    if(!isset($arguments[0]))
		return false;
	    $smt = self::$db->prepare('SELECT * FROM '.static::tableName().' WHERE '.$tail.' LIKE ?');
	    $smt->bindValue(1, '%'.$arguments[0].'%');
	    if($smt->execute() === false)
		self::dumpErr($smt);
	    return $smt->fetchAll(PDO::FETCH_CLASS, get_called_class());
	}
    }

    static function create($arr){
	$sql = 'INSERT INTO '.static::tableName().' SET ';
	foreach(array_keys($arr) as $key){
	    $sql .= $key . ' = ?, ';
	}
	$sql = substr($sql, 0, -2);
	$smt = self::$db->prepare($sql);

	$i = 1;
	foreach($arr as $value){
	    $smt->bindValue($i++, $value);
	}
	if($smt->execute() === FALSE)
	    self::dumpErr($smt);

	return static::find(self::$db->lastInsertId());
    }

    function updateAttributes($arr){
 	$sql = 'UPDATE '.static::tableName().' SET ';
	foreach(array_keys($arr) as $key){
	    $sql .= $key . ' = ?, ';
	}
	$sql = substr($sql, 0, -2).' WHERE id=?';
	
	$smt = self::$db->prepare($sql);

	$i = 1;
	foreach($arr as $value){
	    $smt->bindValue($i++, $value);
	}

	$smt->bindValue($i, $this->id, PDO::PARAM_INT);

	if($smt->execute() === FALSE)
	    self::dumpErr($smt);

	return $this;
    }

    static function find($id){
	$smt = self::$db->prepare('SELECT * FROM '.static::tableName().' WHERE id = :id');
	$smt->bindParam(':id', $id);
	$smt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
	if($smt->execute())
	    return $smt->fetch();
	else
	    self::dumpErr($smt);
    }

    static function all(){
	$sql = 'SELECT * FROM '.static::tableName();
	if(static::$scope)
	    $sql .= ' '.static::$scope;

	if(($smt = self::$db->query($sql)) === FALSE)
	    dumpErr($smt);

	return $smt->fetchAll(PDO::FETCH_CLASS, get_called_class());
    }

    static function tableName(){
	if(static::$tableName)
	    return static::$tableName;
	else
	    return strtolower(get_called_class()).'s';
    }

    static function connect(){
	self::$db = new PDO(
			'mysql:dbname='.config('db.dbname').';host='.config('db.host'),
			config('db.user'),
			config('db.pass'));
	self::$db->query('SET NAMES UTF8');
    }

    static function dumpErr($smt){
	debug_print_backtrace();
	die(print_r($smt->errorInfo(),true));
    }
} 

