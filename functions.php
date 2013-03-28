<?php
$GLOBALS['m'] = null;
$GLOBALS['db'] = null;

// gets a collection from the DB
function get_db_collection($collection, $db = 'yournexthome'){
	if($GLOBALS['m'] === null){
		$GLOBALS['m'] = new Mongo();
	}
	if($GLOBALS['db'] === null){
		$GLOBALS['db'] = $GLOBALS['m']->{$db};
	}
	return $GLOBALS['db']->{$collection};
}

// closes a DB connection
function close_db(){
	if($GLOBALS['m'] !== null){
		$GLOBALS['m']->close();
		$GLOBALS['m'] = null;
	}
	if($GLOBALS['db'] !== null){
		$GLOBALS['db'] = null;
	}
}

// takes two arrays and makes sure that the child array has (at least) the keys from the parent array
function fill_array($parent, $child){

	foreach($parent as $key => $value){
		if($value && !isset($child[$key])){
			$child[$key] = '';
		}
	}

	return $child;
}

// calculates the profile score
function calc_profile_score($user){
	$score = 0;

	$score += ($user['name'] !== '') ? 5 : 0;
	$score += ($user['email'] !== '') ? 5 : 0;
	$score += ($user['links'] !== '') ? 3 : 0;
	$score += ($user['phone'] !== '') ? 3 : 0;
	$score += ($user['country'] !== '') ? 1 : 0;
	$score += ($user['city'] !== '') ? 1 : 0;
	$score += ($user['street'] !== '') ? 1 : 0;
	$score += ($user['street_number'] !== '') ? 1 : 0;
	$score += ($user['schufa'] !== '') ? 7 : 0;
	$score += ($user['work_contract'] !== '') ? 7 : 0;
	$score += ($user['payslips'] !== '') ? 7 : 0;
	$score += ($user['bank_statement'] !== '') ? 7 : 0;
	$score += ($user['landlord_notes'] !== '') ? 7 : 0;
	$score += ($user['facebook_id'] !== '') ? 20 : 0;
	$score += ($user['linkedin_id'] !== '') ? 15 : 0;
	$score += ($user['twitter_id'] !== '') ? 10 : 0;

	return $score;
}
?>
