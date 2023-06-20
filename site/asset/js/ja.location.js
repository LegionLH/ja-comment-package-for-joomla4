/**
 * ------------------------------------------------------------------------
 * JA Comment Package for Joomla 2.5
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */
var JALocation = {
	vars: {
		err: null,
		elIndex: 0,
		latLng: null,
		geocoder: null,
		initialized: []
	},
	
	initialize: function(){
		JALocation.vars.geocoder = new google.maps.Geocoder();
		JALocation.initAutocomplete();
	},
	
	initAutocomplete: function(elIndex) {
		if (elIndex == undefined) {
			elIndex = 0;
		}
		
		JALocation.vars.elIndex = elIndex;
		
		JALocation.vars.err = jQuery('.location-error')[elIndex];
		if (JALocation.vars.err != undefined) {
			JALocation.vars.err.style.visibility = 'hidden';
		}
		
		var input = jQuery('.comment-location')[elIndex];
		if (input){
			input.value = '';

			if (JALocation.vars.initialized[elIndex]){
				return true;
			}
			JALocation.vars.initialized[elIndex] = true;

			var autocomplete = new google.maps.places.Autocomplete(input, {types: ['geocode']});
			
			google.maps.event.addListener(autocomplete, 'place_changed', function() {
				var place = autocomplete.getPlace();
				
				jQuery('.locationLatitude')[elIndex].value = place.geometry.location.lat();
				jQuery('.locationLongitude')[elIndex].value = place.geometry.location.lng();
			});
		}
	},
	
	detectLocation: function() {
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(JALocation.geoLocation, JALocation.geoError, {timeout: 7000});
		}
	},
	
	geoError: function() {
		if (JALocation.vars.err != undefined) {
			JALocation.vars.err.style.visibility = 'visible';
		}
	},
	
	geoLocation: function(position) {
		JALocation.vars.latLng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
		JALocation.getPlace(JALocation.vars.latLng);
	},
	
	getPlace: function() {
		JALocation.vars.geocoder.geocode({'latLng': JALocation.vars.latLng}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				if (jQuery('.comment-location')[JALocation.vars.elIndex] != undefined) {
					jQuery('.comment-location')[JALocation.vars.elIndex].value = results[0].formatted_address;
					
					jQuery('.locationLatitude')[JALocation.vars.elIndex].value = JALocation.vars.latLng.lat();
					jQuery('.locationLongitude')[JALocation.vars.elIndex].value = JALocation.vars.latLng.lng();
				}
			}
			else {
				JALocation.geoError();
			}
		});
	}
};