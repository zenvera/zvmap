<!DOCTYPE html>
<html>
  <head>
    <title>Felucca Map</title>
    <link href="default.css" rel="stylesheet">
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
    <script>
var feluccaTypeOptions = {
  getTileUrl: function(coord, zoom) {
      var normalizedCoord = getNormalizedCoord(coord, zoom);
      if (!normalizedCoord) {
        return null;
      }
      var bound = Math.pow(2, zoom);
      return 'api/maptile.php?' +
          'z=' + zoom + '&x=' + normalizedCoord.x + '&y=' +
          (normalizedCoord.y);
  },
  tileSize: new google.maps.Size(320, 256),
  maxZoom: 7,
  minZoom: 1,
  isPng: true,
  name: 'Felucca'
};

var feluccaMapType = new google.maps.ImageMapType(feluccaTypeOptions);

		function EuclideanProjection() {
			var EUCLIDEAN_RANGE_X = 320;
			var EUCLIDEAN_RANGE_Y = 256
			this.pixelOrigin_ = new google.maps.Point(EUCLIDEAN_RANGE_X / 2, EUCLIDEAN_RANGE_Y / 2);
			this.pixelsPerLatDegree_ = EUCLIDEAN_RANGE_Y / 360;
			this.pixelsPerLatRadian_ = EUCLIDEAN_RANGE_Y / (2 * Math.PI);
			this.pixelsPerLonDegree_ = EUCLIDEAN_RANGE_X / 360;
			this.pixelsPerLonRadian_ = EUCLIDEAN_RANGE_X / (2 * Math.PI);
			this.scaleLat = 14.4;	// Height
			this.scaleLng = 18;	// Width
			this.offsetLat = 0;	// Height
			this.offsetLng = 0;	// Width
		};

		EuclideanProjection.prototype.fromLatLngToPoint = function(latLng, opt_point) {
			var point = opt_point || new google.maps.Point(0, 0);
			var origin = this.pixelOrigin_;
			point.x = (origin.x + (latLng.lng() + this.offsetLng ) * this.scaleLng * this.pixelsPerLonDegree_);
			// NOTE(appleton): Truncating to 0.9999 effectively limits latitude to
			// 89.189.  This is about a third of a tile past the edge of the world tile.
			point.y = (origin.y + (-1 * latLng.lat() + this.offsetLat ) * this.scaleLat * this.pixelsPerLonDegree_);
			return point;
		};

		EuclideanProjection.prototype.fromPointToLatLng = function(point) {
			var me = this;
			var origin = me.pixelOrigin_;
			var lng = (((point.x - origin.x) / me.pixelsPerLonDegree_) / this.scaleLng) - this.offsetLng;
			var lat = ((-1 *( point.y - origin.y) / me.pixelsPerLonDegree_) / this.scaleLat) - this.offsetLat;
			return new google.maps.LatLng(lat, lng, true);
		};

function initialize() {
  feluccaMapType.projection = new EuclideanProjection();

  var cLL = new google.maps.LatLng(<?php include_once "api/functions.php"; list($lX,$lY) = convertToMC(2560,1598); echo $lY . "," . $lX; ?> );

  var mapOptions = {
    center: cLL,
    zoom: 2,
    backgroundColor: "#00314A",
    streetViewControl: false,
    mapTypeControlOptions: {
      mapTypeIds: ['felucca']
    }
  };

  var map = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);
  map.mapTypes.set('felucca', feluccaMapType);
  map.setMapTypeId('felucca');

//google.maps.event.addListener(map, 'click', function(event) {
//   alert(event.latLng);
//  });

<?php
include_once "api/functions.php";

$x = intval($_GET['x']);
$y = intval($_GET['y']);
if (is_numeric($x) && is_numeric($y) && $x != 0 && $y != 0) {
	placeMarker($x,$y,"marker","Location");
}
//placeMarker(0,0,"test","test");
//placeTreasureMarkers();
drawMoongates();
?>
}

// Normalizes the coords that tiles repeat across the x axis (horizontally)
// like the standard Google map tiles.
function getNormalizedCoord(coord, zoom) {
  var y = coord.y;
  var x = coord.x;

  // tile range in one direction range is dependent on zoom level
  // 0 = 1 tile, 1 = 2 tiles, 2 = 4 tiles, 3 = 8 tiles, etc
  var tileRange = 1 << zoom;

  // don't repeat across y-axis (vertically)
  if (y < 0 || y >= tileRange) {
    return null;
  }

  // repeat across x-axis
  if (x < 0 || x >= tileRange) {
    return null; //x = (x % tileRange + tileRange) % tileRange;
  }

  return {
    x: x,
    y: y
  };
}

google.maps.event.addDomListener(window, 'load', initialize);

      </script>
  </head>
  <body>
    <div id="map-canvas"></div>
  </body>
</html>
