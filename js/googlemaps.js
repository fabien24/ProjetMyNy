"use strict";

function initGoogleMap(){
	// requires 3 variables to be set before calling: sitLatitude, sitLongitude, sitName
	var mapContainer = document.querySelector(".map");
	mapContainer.style.display = "block"; // the mapContainer stays hidden when JS is disabled
	var options = {
		zoom: mapCenter.zoom,
		center: new google.maps.LatLng(mapCenter.latitude, mapCenter.longitude),
		mapTypeId: google.maps.MapTypeId.HYBRID
	};
	var map = new google.maps.Map(mapContainer, options);
	for (var i = 0; i < siteList.length; i++) {
		var marker = new google.maps.Marker({
			map: map,
			position: new google.maps.LatLng(siteList[i].latitude, siteList[i].longitude)
		});
		marker.infoWindow = new google.maps.InfoWindow({
			content: siteList[i].name
		});
		google.maps.event.addListener(marker, "click", function(){
			this.infoWindow.open(map, this);
		});
	}
}

google.maps.event.addDomListener(window, "load", initGoogleMap);