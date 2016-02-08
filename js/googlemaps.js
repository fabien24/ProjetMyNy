"use strict";

function initGoogleMap(){
	// requires 3 variables to be set before calling: sitLatitude, sitLongitude, sitName
	var mapContainer = document.querySelector(".map");
	mapContainer.style.display = "block"; // the mapContainer stays hidden when JS is disabled
	var options = {
		zoom: 17,
		center: new google.maps.LatLng(sitLatitude, sitLongitude),
		mapTypeId: google.maps.MapTypeId.HYBRID
	};
	var map = new google.maps.Map(mapContainer, options);
	var marker = new google.maps.Marker({
		map: map,
		position: new google.maps.LatLng(sitLatitude, sitLongitude)
	});
	var infowindow = new google.maps.InfoWindow({
		content: sitName
	});
	google.maps.event.addListener(marker, "click", function(){infowindow.open(map,marker);});
	infowindow.open(map, marker);
}

google.maps.event.addDomListener(window, "load", initGoogleMap);