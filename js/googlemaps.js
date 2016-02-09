"use strict";
var map;
var previousMarker;

function initGoogleMap() {
	// requires 3 variables to be set before calling: sitLatitude, sitLongitude, sitName
	var mapContainer = document.querySelector(".map");
	mapContainer.style.display = "block"; // the mapContainer stays hidden when JS is disabled
	var options = {
		zoom: mapCenter.zoom,
		center: new google.maps.LatLng(mapCenter.latitude, mapCenter.longitude),
		mapTypeId: google.maps.MapTypeId.HYBRID
	};
	map = new google.maps.Map(mapContainer, options);
}

function addSitesToMap(sites) {
	for (var i = 0; i < sites.length; i++) {
		sites[i].marker = new google.maps.Marker({
			map: map,
			position: new google.maps.LatLng(sites[i].latitude, sites[i].longitude)
		});
		previousMarker = sites[i].marker;
		sites[i].marker.infoWindow = new google.maps.InfoWindow({
			content: sites[i].name
		});
		google.maps.event.addListener(sites[i].marker, "click", function(){
			this.infoWindow.open(map, this);
		});
	}
}

function updateMarker() {
	// clear previous marker
	if (previousMarker) {
		previousMarker.setMap(null);
	}
	// get new marker
	var siteList = [
		{
			latitude: document.getElementById("latitude").value,
			longitude: document.getElementById("longitude").value,
			name: document.getElementById("name").value
		}
	];
	addSitesToMap(siteList);
}

google.maps.event.addDomListener(window, "load", initGoogleMap);