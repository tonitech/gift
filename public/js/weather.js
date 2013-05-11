var global;
$(function(){
	/* Configuration */
	var APPID = 'fa2pT26k';        // Your Yahoo APP id
	var DEG = 'c';        // c for celsius, f for fahrenheit
	
	// Mapping the weather codes returned by Yahoo's API
	// to the correct icons in the img/icons folder
	var nameCodeMap = [
		'storm', 'storm', 'storm', 'lightning', 'lightning', 'snow', 'hail', 'hail',
		'drizzle', 'drizzle', 'rain', 'rain', 'rain', 'snow', 'snow', 'snow', 'snow',
		'hail', 'hail', 'fog', 'fog', 'fog', 'fog', 'wind', 'wind', 'snowflake',
		'cloud', 'cloud_moon', 'cloud_sun', 'cloud_moon', 'cloud_sun', 'moon', 'sun',
		'moon', 'sun', 'hail', 'sun', 'lightning', 'lightning', 'lightning', 'rain',
		'snowflake', 'snowflake', 'snowflake', 'cloud', 'rain', 'snow', 'lightning'
	];
	
	var iconNameMap = new Array();
	iconNameMap['moon'] = 'sunny';
	iconNameMap['sun'] = 'sunny';
	
	iconNameMap['fog'] = 'cloudy';
	iconNameMap['wind'] = 'cloudy';
	iconNameMap['cloud'] = 'cloudy';
	iconNameMap['cloud_moon'] = 'cloudy';
	iconNameMap['cloud_sun'] = 'cloudy';
	
	iconNameMap['storm'] = 'rainy';
	iconNameMap['lightning'] = 'rainy';
	iconNameMap['drizzle'] = 'rainy';
	iconNameMap['rain'] = 'rainy';
	
	iconNameMap['snow'] = 'snowy';
	iconNameMap['hail'] = 'snowy';
	iconNameMap['snowflake'] = 'snowy';
	
	// Does this browser support geolocation?
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(locationSuccess, locationError);
	} else {
		showError("Your browser does not support Geolocation!");
	}
	
	// Get user's location, and use Yahoo's PlaceFinder API
	// to get the location name, woeid and weather forecast
	function locationSuccess(position) {
		var lat = position.coords.latitude;
		var lon = position.coords.longitude;

		// Yahoo's PlaceFinder API http://developer.yahoo.com/geo/placefinder/
		// We are passing the R gflag for reverse geocoding (coordinates to place name)
		var geoAPI = 'http://where.yahooapis.com/geocode?location='+lat+','+lon+'&flags=J&gflags=R&appid='+APPID;
		
		// Forming the query for Yahoo's weather forecasting API with YQL
		// http://developer.yahoo.com/weather/
		
		var wsql = 'select * from weather.forecast where woeid=WID and u="'+DEG+'"',
			weatherYQL = 'http://query.yahooapis.com/v1/public/yql?q='+encodeURIComponent(wsql)+'&format=json&callback=?',
			code, city, results, woeid;
		
		if (window.console && window.console.info){
			console.info("Coordinates: %f %f", lat, lon);
		}
		
		// Issue a cross-domain AJAX request (CORS) to the GEO service.
		// Not supported in Opera and IE.
		$.getJSON(geoAPI, function(r){
			if(r.ResultSet.Found == 1){
				results = r.ResultSet.Results;
				city = results[0].city;
				code = results[0].statecode || results[0].countrycode;
				// This is the city identifier for the weather API
				woeid = results[0].woeid;
				// Make a weather API request:
				$.getJSON(weatherYQL.replace('WID',woeid), function(r){
					if(r.query && r.query.count == 1){
						// Create the weather items in the #scroller UL
						var item = r.query.results.channel.item.condition;
						global = item;
						if(!item){
							showError("We can't find weather information about your city!");
							if (window.console && window.console.info){
								console.info("%s, %s; woeid: %d", city, code, woeid);
							}
							return false;
						}
						addWeather(item.code, item.text, item.temp, city);
					}
					else {
						showError("Error retrieving weather data!");
					}
				});
			}
		}).error(function(){
			showError("Your browser does not support CORS requests!");
		});
	}
	
	function addWeather(code, condition, temperature, city){
		var currentDate = new Date();
		var time = currentDate.getHours();
		var weather = iconNameMap[nameCodeMap[code]];
		var background = '';
		if (time > 6 && time < 18) {
			if (weather == 'sunny' || weather == 'cloudy') {
				background = 'images/weather/daycloudy.jpg';
			} else {
				background = 'images/weather/dayrain.jpg';
			}
		} else {
			if (weather == 'sunny' || weather == 'cloudy') {
				background = 'images/weather/nightcloudy.jpg';
			} else {
				background = 'images/weather/nightrain.jpg';
			}
		}
		
		var sentence = '';
		if (time > 6 && time < 12) {
			sentence = 'Good Morning';
		} else if (time > 12 && time < 18) {
			sentence = 'Good Afternoon';
		} else {
			sentence = 'Good Evening';
		}
		
		$('#weather_greeting').html(sentence);
		$('#weather_bg').html('<img src="' + path + background + '">');
		$('#weatherico').html('<img src="' + path + 'images/weathericon/'+ weather + '.gif' + '">');
		$('#weather_city').html(city);
		$('#weather_temp').html(condition + ' ' + temperature + '°C');
		$('#weatherico').html('<img src="' + path + 'images/weathericon/'+ weather + '.gif' + '">');
	}
	
	/* Error handling functions */
	function locationError(error){
		switch(error.code) {
			case error.TIMEOUT:
				showError("A timeout occured! Please try again!");
				break;
			case error.POSITION_UNAVAILABLE:
				showError('We can\'t detect your location. Sorry!');
				break;
			case error.PERMISSION_DENIED:
				showError('Please allow geolocation access for this to work.');
				break;
			case error.UNKNOWN_ERROR:
				showError('An unknown error occured!');
				break;
		}
	}
	
	function showError(msg){
		alert(msg);
	}
});