<?php
$config['db'] = null;

// gets a collection from the DB
function get_db_collection($collection){
	if($config['db'] === null){
		$m = new Mongo();
		$config['db'] = $m->imapper;
	}
	return $config['db']->{$collection};
}

// closes a DB connection
function close_db(){
	if($config['db'] !== null){
		close($config['db']);
		$config['db'] = null;
	}
}
?>
