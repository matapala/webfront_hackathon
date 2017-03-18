<html>
<head>
    <title>BYOS's Trip</title>
    <meta name="viewport" content="initial-scale=1.0">
    <meta charset="utf-8">
<meta property="og:title" content="TripNShare"/>
<meta property="og:type" content="website"/>
<meta property="og:url" content="http://91.134.142.192/share/tripview.php?pointtrip=58cd11e31af0c500010c5e03"/>
<meta property="og:image" content="http://91.134.142.192/tripandshare.png"/>
<meta property="og:description" content="Voyagez,Immortaliser,Preserver,Reviver,Partager avec votre entourage"/>
<meta property="og:site_name" content="TripAndShare"/>
    <style>
	html, body {
		height: 100%;
		margin: 0;
		padding: 0;
		font-family: sans-serif;
		background-image: url("blueTapaTile.png");
	}
	#map {
		<!-- left: 15%; -->
		height: 100%;
		width: 100%;
	}
    </style>
	
  <script src="vis.js"></script>
  <link href="vis-timeline-graph2d.min.css" rel="stylesheet" type="text/css" />
  
  <style type="text/css">
    .vis-timeline {
      border: 2px solid blue;
      font-size: 12pt;
      background: #0072c6;
    }

    .vis-item {
      border-color: #00b7c6;
      background-color: #00b7c6;
      font-size: 15pt;
      color: #00b7c6;
      box-shadow: 5px 5px 20px rgba(128,128,128, 0.5);
    }

    .vis-item,
    .vis-item.vis-line {
      border-width: 3px;
    }

    .vis-item.vis-dot {
      border-width: 10px;
      border-radius: 10px;
    }

    .vis-item.vis-selected {
      border-color: green;
      background-color: lightgreen;
    }

    .vis-time-axis .vis-text {
      color: #00b7c6;
      padding-top: 10px;
      padding-left: 10px;
    }

    .vis-time-axis .vis-text.vis-major {
      font-weight: bold;
    }

    .vis-time-axis .vis-grid.vis-minor {
      border-width: 2px;
      border-color: #00b7c6;
    }

    .vis-time-axis .vis-grid.vis-major {
      border-width: 2px;
      border-color: #00b7c6;
    }
	
	.divbutton {
		height: 50px;
		background: #000;
	}

	button {
		display: none;
	}

	.divbutton:hover button {
		display: block;
	}

	.image { 
	   position: relative; 
	   width: 100%; /* for IE 6 */
	}

	#PICDSC { 
	   position: absolute; 
	   top: 300px; 
	   left: 0; 
	   width: 92%;
	   margin: 0 2%;
	   color: white; 
	   font: bold 24px/45px Helvetica, Sans-Serif; 
	   letter-spacing: -1px; 
	   background: rgba(0, 0, 0, 0.7);
	   padding: 10px; 
	}

  </style>  
</head>

<script>

<?php

/** 
* Send a GET requst using cURL 
* @param string $url to request 
* @param array $get values to send 
* @param array $options for cURL 
* @return string 
*/ 
function curl_get($url, array $get = NULL, array $options = array()) 
{    
    $defaults = array( 
        CURLOPT_URL => $url. (strpos($url, '?') === FALSE ? '?' : ''). http_build_query($get), 
        CURLOPT_HEADER => 0, 
        CURLOPT_RETURNTRANSFER => TRUE, 
        CURLOPT_TIMEOUT => 4 
    ); 
    
    $ch = curl_init(); 
    curl_setopt_array($ch, ($options + $defaults)); 
    if( ! $result = curl_exec($ch)) 
    { 
        trigger_error(curl_error($ch)); 
    } 
    curl_close($ch); 
    return $result; 
} 

$gget = array();
$options = array();

$resCurl = curl_get('http://91.134.142.192:3000/gettripbyidtrip/'. $_GET['pointtrip'], $gget, $options);

echo 'var dataDB = '. $resCurl .';';

?>

var adresseServer = "http://91.134.142.192/media";

var map;
function initMap() 
{
	map = new google.maps.Map(document.getElementById('map'), {
		center: {lat:-17.555138,lng:-149.609086},
		zoom: 10
	});

	setMarkers( map );
	
	// Define a symbol using SVG path notation, with an opacity of 1.
	var lineSymbol = {
		path: 'M 0,-1 0,1',
		strokeOpacity: 1,
		scale: 3
	};

	var coords = [];
    for( var i=0; i < dataDB.length; i++ ) {
		coords.push( {lat:dataDB[i].Lat,lng:dataDB[i].Lon} );
    }

	// Create the polyline, passing the symbol in the 'icons' property.
	// Give the line an opacity of 0.
	// Repeat the symbol at intervals of 20 pixels to create the dashed effect.
	var line = new google.maps.Polyline({
		path: coords, //[{lat: initLat, lng: initLong}, {lat: initLat+0.1, lng: initLong+0.3}],
		strokeOpacity: 0,
		icons: [{
		icon: lineSymbol,
		offset: '0',
		repeat: '20px'
		}],
		map: map
	});	
}


var markerAvion;
var imageAvion;
//var posAvion;
function setMarkers( map )
{
	imageAvion = {
		url: 'avion_NE.png',
		size: new google.maps.Size(32, 32),
		origin: new google.maps.Point(0, 0),
		anchor: new google.maps.Point(16, 16)
	};
    markerAvion = new google.maps.Marker({
      position: {lat:-17.555138,lng:-149.609086},
      map: map,
      icon: imageAvion,
      title: "rien",
      zIndex: 0
    });
}




//////////////////////////////////////////////////////////////////
/////////////   le mouvement de la carte d'un point à l'autre

var panPath = [];   // An array of points the current panning action will use
var panQueue = [];  // An array of subsequent panTo actions to take
var STEPS = 50;     // The number of steps that each panTo action will undergo
var waitTime = 10;

var panAvPath = [];

function panTo(newLat, newLng) {
  if (panPath.length > 0) {
    // We are already panning...queue this up for next move
    panQueue.push([newLat, newLng]);
  } else {
    // Lets compute the points we'll use
    panPath.push("LAZY SYNCRONIZED LOCK");  // make length non-zero - 'release' this before calling setTimeout
    var curLat = map.getCenter().lat();
    var curLng = map.getCenter().lng();
    var dLat = (newLat - curLat)/STEPS;
    var dLng = (newLng - curLng)/STEPS;
	
	// afficher la bonne icone en fonction de la direction du mouvement
	var llAvion = markerAvion.getPosition();
	
	var daLat = (newLat - llAvion.lat())/STEPS;
	var daLng = (newLng - llAvion.lng())/STEPS;
	if( daLat >= 0 ){
		if( daLng >= 0 ) imageAvion.url = 'avion_NE.png';  //document.avion.src = 'avion_NE.png';
		else imageAvion.url = 'avion_NW.png';
	} else {
		if( daLng >= 0 ) imageAvion.url = 'avion_SE.png';
		else imageAvion.url = 'avion_SW.png';
	}
	markerAvion.setIcon( imageAvion );

	// on calcule les positions de l'avion durant l'animation : l'avion peut avoir une trajectoire differente de la camera si l'utilisateur a bougé la carte
    for( var i=0; i < STEPS; i++ ) {
		panAvPath.push([llAvion.lat() + daLat * i, llAvion.lng() + daLng * i]);
    }
    panAvPath.push([newLat, newLng]);

	
    for (var i=0; i < STEPS; i++) {
      panPath.push([curLat + dLat * i, curLng + dLng * i]);
    }
    panPath.push([newLat, newLng]);
    panPath.shift();      // LAZY SYNCRONIZED LOCK
    setTimeout(doPan, waitTime);
  }
}

function doPan() 
{
	var next = panPath.shift();
	if (next != null) {
		// Continue our current pan action
		map.panTo( new google.maps.LatLng(next[0], next[1]));

		var avNext = panAvPath.shift();
		markerAvion.setPosition( new google.maps.LatLng( avNext[0], avNext[1] ) );

		setTimeout(doPan, waitTime );
	} else {
		// We are finished with this pan - check if there are any queue'd up locations to pan to 
		var queued = panQueue.shift();
		if (queued != null) {
		  panTo(queued[0], queued[1]);
		}
	}
}

//////////////////////////////////////////////////////////////////

function fade(element) {
    var op = 1;  // initial opacity
    var timer = setInterval(function () {
        if (op <= 0.1){
            clearInterval(timer);
            element.style.display = 'none';
        }
        element.style.opacity = op;
        element.style.filter = 'alpha(opacity=' + op * 100 + ")";
        op -= op * 0.1;
    }, 20);
}
function unfade(element) {
    var op = 0.1;  // initial opacity
    element.style.display = 'block';
    var timer = setInterval(function () {
        if (op >= 1){
            clearInterval(timer);
        }
        element.style.opacity = op;
        element.style.filter = 'alpha(opacity=' + op * 100 + ")";
        op += op * 0.1;
    }, 10);
}
function fadeTransitionImg(element,newSrc) {
    var op = 1;  // initial opacity
    var timer = setInterval(function () {
        if (op <= 0.1){
            clearInterval(timer);
            element.style.display = 'none';
			element.src = newSrc;
			unfade( element );
        }
        element.style.opacity = op;
        element.style.filter = 'alpha(opacity=' + op * 100 + ")";
        op -= op * 0.1;
    }, 20);
}


var lastId = -1;
var timeline;
function showTravel( id )
{
	if( id == lastId ) return;
	if( id < 0 || id >= dataDB.length ) return;
	
	lastId = id;
	timeline.focus( id );
	timeline.setSelection( id );
	
	// document.PIC.src = dataDB[ id ].src;
	fadeTransitionImg( document.PIC, adresseServer +"/"+ dataDB[ id ].mediaName );
	panTo( dataDB[id].Lat, dataDB[id].Lon );

	var imgDsc = document.getElementById('PICDSC');
	imgDsc.innerHTML = dataDB[id].desc;
}



var indRun = 0;
var diaporamaSpeed = 3000;
function diaporamaRunning()
{
	if( indRun < dataDB.length ) { 
		showTravel( indRun ); 
		indRun++; 
		setTimeout( diaporamaRunning, diaporamaSpeed );
	}
	
}

function diaporama()
{
	indRun = 0;
	setTimeout( diaporamaRunning, diaporamaSpeed/2 );
}

function firstStep(step)
{
	step = (step)?step:0;
	showTravel(step);
	timeline.fit();
	timeline.setSelection(step);
}


</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDd8ohSW6WXH9M4U2bMFVCRGVP92dBK_tY&callback=initMap" async defer></script>

<body onload="firstStep(0);">


<center>
	<table style="width:80%; height:100px">
		<tr>
			<td><center>
			<!-- <img name="BANDEAV" src="banderolleAvion.png" style="height:100px;"> -->
			<canvas id="myCanvas" width="1250px" height="200px"></canvas>
			</center></td>
		</tr>
	</table>
	<table style="width:80%">
		<tr>
			<td width="40%" height="500px"><div id="map" style="width:100%; height:100%"></div>
			</td>
			<td width="60%"><center>
					<div class="image">
					<img name="PIC" src="" style="height:500px;max-width:900px;width: expression(this.width > 900 ? 900: this.width);">
					<div id="PICDSC"></div>
					</div>
			</center> 
			</td>
		</tr>
	</table>
	<div id="visualization" style="width:80%;"></div>
	</br>
	<img src="prev.png" alt="Previous" style="width:50px;height:50px;" onclick="if( lastId>0 ) showTravel( lastId-1 );">
	<img src="diaporama.png" alt="Diaporama" style="width:50px;height:50px;" onclick="diaporama();">
	<img src="next.png" alt="Next" style="width:50px;height:50px;" onclick="if( lastId < dataDB.length-1 ) showTravel( lastId+1 );">
</center>
	
    <script>
		var canvas = document.getElementById('myCanvas');
		var context = canvas.getContext('2d');
		var imageObj = new Image();

		imageObj.onload = function() {
			context.drawImage(imageObj, 0, 0);
			context.font = "40pt Calibri";
			context.strokeText("BYOS's Trip", 60, 80);
		};
		imageObj.src = 'banderolleAvion.png';
		
    </script>
	
	<script type="text/javascript">
		// DOM element where the Timeline will be attached
		var container = document.getElementById('visualization');
		var timelineItems = [];
		//alert( dataDB.length );
		for( var i=0; i < dataDB.length; i++ ) {
			if( dataDB == null ) {
				alert( 'NULL !!');
				continue;
			}
			//var datetime = dataDB[i].mediaDate.replace('T', ' ').replace( 'Z', '' );
			//datetime = datetime.replace(/Z/g, '');
			//alert( datetime );
			var datetime = dataDB[i].mediaDate;
			timelineItems.push( {id: i, content: '<img src="'+ adresseServer +'/'+ dataDB[i].mediaName +'" onclick="showTravel('+ i +');"  style="width:32px; height:32px;">', start:datetime } );
		}	  
		var items = new vis.DataSet( timelineItems );
		var options = {};
		timeline = new vis.Timeline( container, items, options );
	</script>

    <!-- <div id="plane"><img src="https://www.airtahitinui.com/sites/all/themes/bootstrap_atn/images/icone-avion-on.png" alt="avion" style="width:50;height:50;"></div> -->
	
	<!-- <canvas id="canvas" width="400" height="400" style="background:#000;"></canvas> -->
	

</body>
   
</html>
