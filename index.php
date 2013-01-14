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
	Price:<br>
	<input type="text" id="min_price" size="5"> - <input type="text" id="max_price" size="5"><br><br>

	Rooms:<br>
	<input type="text" id="min_rooms" size="5"> - <input type="text" id="max_rooms" size="5"><br><br>

	Size:<br>
	<input type="text" id="min_size" size="5"> - <input type="text" id="max_size" size="5">
</div>

</body>
</html>

