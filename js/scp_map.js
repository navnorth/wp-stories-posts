jQuery(document).ready(function($){
	$('.map-refresh-btn').on("click", function(){
		var api_key = googlemap.apikey;
		var uuid = gen_uuid();

		const lat = document.getElementById("map-latitude").value;
  	const lng = document.getElementById("map-longitude").value;
  	const input = document.getElementById("story-mapaddress").value;
		
		const geocoder = new google.maps.Geocoder();
		
		if (lat!=="" && lng!=="")
			geocodeLatLng(geocoder, map);
		else
			geocodeAddress(geocoder, map);
	});
  $('#story-mapaddress,#map-latitude,#map-longitude').on('keypress', function(e){
    if (e.keyCode==13){
      e.preventDefault();
      $('.map-refresh-btn').trigger("click");
    } 
  });
});

// Generate Version 4 UUID
function gen_uuid() {    
    var uuid = "", i, random;    

    for (i = 0; i < 32; i++) {      
        random = Math.random() * 16 | 0;        
        
        if (i == 8 || i == 12 || i == 16 || i == 20) {        
            uuid += "-";      
        }
      
        uuid += (i == 12 ? 4 : (i == 16 ? (random & 3 | 8) : random)).toString(16);    
     }   
 
     return uuid;  
}

// Reverse Geocoding to get Address and Zip Code
function geocodeLatLng(geocoder, map) {
  const lat = document.getElementById("map-latitude").value;
  const lng = document.getElementById("map-longitude").value;
  const input = document.getElementById("story-mapaddress");
  const zip = document.getElementById("story-zipcode");
  const latlng = {
    lat: parseFloat(lat),
    lng: parseFloat(lng),
  };
  geocoder.geocode({ location: latlng }, (results, status) => {
    if (status === "OK") {
      if (results[0]) {
      	input.value = results[0].formatted_address;
      	let address_components = results[0].address_components;
      	address_components.map(function(component){
      		if (component.types.indexOf("postal_code")!==-1){
      			zip.value = component.long_name;
      		}
      	});
        const marker = new google.maps.Marker({
          anchorPoint: new google.maps.Point(0, -29),
          map: map,
        });
        marker.setVisible(false);
        var loc = new google.maps.LatLng(latlng.lat, latlng.lng);
        map.setCenter(loc);
        map.setZoom(15);
        marker.setPosition(loc);
        marker.setVisible(true);
      } else {
        jQuery('#map-error-msg').html("No results found").show();
        window.setTimeout(function(){ jQuery('#map-error-msg').hide(); },5000)
      }
    } else {
      jQuery('#map-error-msg').html("Geocoder failed due to: " + status).show();
      window.setTimeout(function(){ jQuery('#map-error-msg').hide(); },5000)
    }
  });
}

// Geocoding to get zip and coordinates
function geocodeAddress(geocoder, map) {
  const address = document.getElementById("story-mapaddress").value;
  const lat = document.getElementById("map-latitude");
  const lng = document.getElementById("map-longitude");
  const zip = document.getElementById("story-zipcode");
  geocoder.geocode({ address: address }, (results, status) => {
    if (status === "OK") {
  		lat.value = results[0].geometry.location.lat();
  		lng.value = results[0].geometry.location.lng();
  		let address_components = results[0].address_components;
  	  	address_components.map(function(component){
  	  		if (component.types.indexOf("postal_code")!==-1){
  	  			zip.value = component.long_name;
  	  		}
  	  	});
  		map.setCenter(results[0].geometry.location);
  		map.setZoom(15);
  		const marker = new google.maps.Marker({
  			map: map,
        anchorPoint: new google.maps.Point(0, -29),
  			position: results[0].geometry.location,
  		});
      marker.setVisible(true);
    } else {
      jQuery('#map-error-msg').html("Geocode was not successful for the following reason: " + status).show();
      window.setTimeout(function(){ jQuery('#map-error-msg').hide(); },5000)
    }
  });
}