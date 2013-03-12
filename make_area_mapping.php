<?php
require_once('functions.php');

// ------------------- Interests --------------------
$interests_array = array(
	'trendy' => array(
		'name' => 'Trendy'
	),
	'artsy' => array(
		'name' => 'Artsy'
	),
	'dining' => array(
		'name' => 'Dining'
	),
	'nightlife' => array(
		'name' => 'Nightlife'
	),
	'loved_by_berliners' => array(
		'name' => 'Loved By Berliners'
	),
	'great_transit' => array(
		'name' => 'Great Transit'
	),
	'peace_quiet' => array(
		'name' => 'Peace & Quiet'
	),
	'touristy' => array(
		'name' => 'Touristy'
	),
	'shopping' => array(
		'name' => 'Shopping'
	)
);

$interests = get_db_collection('interests');
$interests->remove();
foreach($interests_array as $id => $interest){
	$obj = array(
		'id' => $id, 
		'name' => $interest['name']
	);
	$interests->insert($obj);
	$interests_array[$id]['id'] = $obj['_id']->{'$id'};
}

// ------------------- Areas --------------------
$areas_array = array(
	'charlottenburg' => array(
		'name' => 'Charlottenburg',
		'interests' => array('dining', 'loved_by_berliners', 'great_transit', 'peace_quiet', 'shopping')
	),
	'friedrichshain' => array(
		'name' => 'Friedrichshain',
		'interests' => array('trendy', 'artsy', 'dining', 'nightlife', 'loved_by_berliners', 'great_transit', 'touristy', 'shopping')
	),
	'kreuzberg' => array(
		'name' => 'Kreuzberg',
		'interests' => array('trendy', 'artsy', 'dining', 'nightlife', 'loved_by_berliners', 'great_transit')
	),
	'mitte' => array(
		'name' => 'Mitte',
		'interests' => array('dining', 'nightlife', 'loved_by_berliners', 'great_transit', 'peace_quiet', 'touristy', 'shopping')
	),
	'moabit' => array(
		'name' => 'Moabit',
		'interests' => array('loved_by_berliners', 'great_transit', 'peace_quiet')
	),
	'neukolln' => array(
		'name' => 'Neukölln',
		'interests' => array('trendy', 'artsy', 'dining', 'nightlife', 'loved_by_berliners', 'great_transit')
	),
	'potsdamer_platz' => array(
		'name' => 'Potsdamer Platz',
		'interests' => array('great_transit', 'touristy', 'shopping')
	),
	'prenzlauer_berg' => array(
		'name' => 'Prenzlauer Berg',
		'interests' => array('trendy', 'artsy', 'dining', 'nightlife', 'loved_by_berliners', 'great_transit', 'touristy', 'shopping')
	),
	'schoneberg' => array(
		'name' => 'Schöneberg',
		'interests' => array('dining', 'nightlife', 'loved_by_berliners', 'great_transit')
	),
	'steglitz' => array(
		'name' => 'Steglitz',
		'interests' => array('great_transit', 'peace_quiet')
	),
	'tiergarten' => array(
		'name' => 'Tiergarten',
		'interests' => array('great_transit', 'peace_quiet', 'touristy')
	),
	'wedding' => array(
		'name' => 'Wedding',
		'interests' => array('great_transit', 'shopping')
	),
);

$areas = get_db_collection('areas');
$areas->remove();
foreach($areas_array as $id => $area){
	$ints = array();
	foreach($area['interests'] as $i => $interest){
		array_push($ints, $interests_array[$interest]);
	}
	$obj = array(
		'id' => $id, 
		'name' => $area['name'],
		'interests' => $ints
	);
	$areas->insert($obj);	
}

close_db();
?>
