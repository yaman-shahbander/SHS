var x = document.getElementById("map");
var m_lat;
var m_long;
var first_marker;
			function getLocation() {
				marker = false;
			  if (navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(showPosition);
			  } else { 
				x.innerHTML = "Geolocation is not supported by this browser.";
			  }
			}

			function showPosition(position) {
			  //x.innerHTML = "Latitude: " + position.coords.latitude + "<br>Longitude: " + position.coords.longitude;
			  
			  m_lat = position.coords.latitude;
			  m_long = position.coords.longitude;
			  get_user_location(m_lat,m_long);
			  //var centerOfMap = new google.maps.LatLng(m_lat, m_long);
			  //var clickedLocation = event.latLng;
			  addMarker({lat: m_lat, lng: m_long});
			}
			function addMarker(coordinates) {
			    first_marker = new google.maps.Marker({
				  position: coordinates, // Passing the coordinates
				  map:map, //Map that we need to add
				  draggarble: false// If set to true you can drag the marker
			   });
			   
			}



//map.js

//Set up some of our variables.
var map; //Will contain map object.
var marker = false; ////Has the user plotted their location marker? 
function get_user_location(m_lat,m_long){
	initMap(m_lat, m_long)
	document.getElementById('lat').value = m_lat; //latitude
    document.getElementById('lng').value = m_long; //longitude
}        
//Function called to initialize / create the map.
//This is called when the page has loaded.
function initMap(m_lat, m_long) {

    //The center location of our map.
    var centerOfMap = new google.maps.LatLng(m_lat, m_long);

    //Map options.
    var options = {
      center: centerOfMap, //Set center.
      zoom: 13 //The zoom value.
    };

    //Create the map object.
    map = new google.maps.Map(document.getElementById('map'), options);

    //Listen for any clicks on the map.
    google.maps.event.addListener(map, 'click', function(event) {                
        //clear previous marker
		first_marker.setMap(null);
		
		//Get the location that the user clicked.
		
        var clickedLocation = event.latLng;
        //If the marker hasn't been added.
        
		if(marker === false){
            //Create the marker.
            marker = new google.maps.Marker({
                position: clickedLocation,
                map: map,
                draggable: true //make it draggable
            });
            //Listen for drag events!
            google.maps.event.addListener(marker, 'dragend', function(event){
                markerLocation();
            });
        } else{
            //Marker has already been added, so just change its location.
            marker.setPosition(clickedLocation);
        }
        //Get the marker's location.
        markerLocation();
    });
}
        
//This function will get the marker's current location and then add the lat/long
//values to our textfields so that we can save the location.
function markerLocation(){
    //Get location.
    var currentLocation = marker.getPosition();
    //Add lat and lng values to a field that we can save.
    document.getElementById('lat').value = currentLocation.lat(); //latitude
    document.getElementById('lng').value = currentLocation.lng(); //longitude
}
        
//Load the map when the page has finished loading.
//google.maps.event.addDomListener(window, 'load', initMap);