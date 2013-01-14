<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>IMapper</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="content-language" content="en">
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/jquery-ui.min.js"></script>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css" />    
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
<script src="includes/js.js"></script>
</head>
<body>

<div id="search_map_canvas" style="width:100%;height:100%;"></div>

<div id="search_box">
	Price: €<span id="min_price">0</span> - €<span id="max_price">4000</span><br>
	<div class="slider" id="price_slider"></div>	

	Rooms: <span id="min_rooms">0</span> - <span id="max_rooms">10</span><br>
	<div class="slider" id="rooms_slider"></div>	

	Size: <span id="min_size">0</span>m2 - <span id="max_size">500</span>m2<br>
	<div class="slider" id="size_slider"></div>	

	Extras:<br>
	<div id="extras"></div>

</div>

</body>
</html>

