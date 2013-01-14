<?php
// determines how a website should be fetched
function fetch_wg_gesucht(){
	//insert_url_wg_gesucht('im24.txt');
}

// fetch a specific URL & inserts it into the DB
function insert_url_wg_gesucht($filename){
	$place_separator = '<li class="is24-res-entry';
	$file = file_get_contents($filename);

	while(strpos($file, $place_separator) > 0){
		// set $file to start from next place
		$file = substr($file, strpos($file, $place_separator));

		// pull out place
		$place = (strpos($file, $place_separator, strlen($place_separator)) > 0)? substr($file, 0, strpos($file, $place_separator, strlen($place_separator))): substr($file, 0); 

		// read data from place
		$data = read_place_wg_gesucht($place);

		// insert place
		print_r($data);

		// make sure $file doesn't start with the same place next time
		$file = substr($file, strlen($place_separator));
	}
}

// decodes a place string
function read_place_wg_gesucht($place){
	$data = array();

	// set time inserted
	$data['time_inserted'] = date('Y-m-d H:i:s', time());

	// set vendor info
	$data['vendor_name'] = 'ImmobilienScout24.de';
	$data['vendor_link'] = 'http://www.immobilienscout24.de';

	// get vendor id
	$place = substr($place, strpos($place, 'data-realEstateId="') + strlen('data-realEstateId="'));
	$data['vendor_id'] = strip_tags(substr($place, 0, strpos($place, '">')));

	// get url	
	$data['url'] = 'http://www.immobilienscout24.de/expose/'.$data['vendor_id'];

	// get title
	$place = substr($place, strpos($place, '<a href="/expose/'));
	$data['title'] = trim(strip_tags(substr($place, 0, strpos($place, '</a>') + strlen('</a>'))));

	// get img
	$place = substr($place, strpos($place, 'http://picture.immobilienscout24.de'));
	$data['img'] = strip_tags(substr($place, 0, strpos($place, '?')));

	// get price
	$place = substr($place, strpos($place, '<dt class="'));
	$type = strip_tags(substr($place, 0, strpos($place, '</dt>') + strlen('</dt>')));
	$place = substr($place, strpos($place, '<dd class="'));
	$data['price'] = ($type === 'Kaltmiete: ')?(int)strip_tags(substr($place, 0, strpos($place, '</dd>') + strlen('</dd>'))):'';

	// get size
	$place = substr($place, strpos($place, '<dt class="'));
	$type = strip_tags(substr($place, 0, strpos($place, '</dt>') + strlen('</dt>')));
	$place = substr($place, strpos($place, '<dd class="'));
	$data['size'] = ($type === ' Wohnfl&auml;che: ')?(int)strip_tags(substr($place, 0, strpos($place, '</dd>') + strlen('</dd>'))):'';

	// get rooms
	$place = substr($place, strpos($place, '<dt class="'));
	$type = strip_tags(substr($place, 0, strpos($place, '</dt>') + strlen('</dt>')));
	$place = substr($place, strpos($place, '<dd class="'));
	$data['rooms'] = ($type === 'Zimmer: ')?(int)strip_tags(substr($place, 0, strpos($place, '</dd>') + strlen('</dd>'))):'';

	// get tags
	$data['tags'] = array();
	$place = substr($place, strpos($place, '<ul class="is24-checklist">'));
	$tags = substr($place, 0, strpos($place, '</ul>'));
	while(strpos($tags, '<li') > 0){
		$tags = substr($tags, strpos($tags, '<li'));
		array_push($data['tags'], str_replace('*', '', strip_tags(substr($tags, 0, strpos($tags, '</li>') + strlen('</li>')))));
		$tags = substr($tags, strlen('<li'));
	}

	// get address
	$place = substr($place, strpos($place, '<p class="is24-address">'));
	$address = substr($place, 0, strpos($place, '</p>'));
	$data['address'] = trim(strip_tags(substr($address, strrpos($address, '</span>'))));

	// get lat/lng
	$location = getLatLng($data['address']);
	$data['lat'] = $location['lat'];
	$data['lng'] = $location['lng'];

	return $data;
}
?>
