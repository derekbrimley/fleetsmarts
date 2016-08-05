
//VALIDATES DATE IN MM/DD/YYYY FORMAT
function isDate(txtDate) 
{
    var objDate,  // date object initialized from the txtDate string
        mSeconds, // txtDate in milliseconds
        day,      // day
        month,    // month
        year;     // year

	if(txtDate.count('/') != 2)
	{
		return false;
	}
		
	var monthfield=txtDate.split("/")[0];
	var dayfield=txtDate.split("/")[1];
	var yearfield=txtDate.split("/")[2];

	//alert(monthfield);
	//alert(dayfield);
	//alert(yearfield);
	//MAKE MONTH 2 DIGITS
	if (monthfield.length == 1)
	{
		monthfield = "0"+monthfield;
	}
	
	//MAKE DAY 2 DIGITS
	if (dayfield.length == 1)
	{
		dayfield = "0"+dayfield;
	}
	
	//MAKE YEAR 4 DIGITS
	if (yearfield.length == 2)
	{
		yearfield = "20"+yearfield;
	}

	txtDate = monthfield+"/"+dayfield+"/"+yearfield;
	
	// date length should be 10 characters (no more no less)
    if (txtDate.length !== 10) {
        return false;
    }
    // third and sixth character should be '/'
    if (txtDate.substring(2, 3) !== '/' || txtDate.substring(5, 6) !== '/') {
        return false;
    }
    // extract month, day and year from the txtDate (expected format is mm/dd/yyyy)
    // subtraction will cast variables to integer implicitly (needed
    // for !== comparing)
    month = txtDate.substring(0, 2) - 1; // because months in JS start from 0
    day = txtDate.substring(3, 5) - 0;
    year = txtDate.substring(6, 10) - 0;
    // test year range
    if (year < 1000 || year > 3000) {
        return false;
    }
    // convert txtDate to milliseconds
    mSeconds = (new Date(year, month, day)).getTime();
    // initialize Date() object from calculated milliseconds
    objDate = new Date();
    objDate.setTime(mSeconds);
    // compare input date and parts from Date() object
    // if difference exists then date isn't valid
    if (objDate.getFullYear() !== year ||
        objDate.getMonth() !== month ||
        objDate.getDate() !== day) {
        return false;
    }
    // otherwise return true
    return true;
}//end isDate

function isTime(txtTime)
{
	//CHECK THAT STRING LENGTH IS 5
	if (txtTime.length != 5)
	{
		return false;
	}
	
	//GET EACH CHARACTER FROM STRING
	var char_1 = txtTime.charAt(0);
	var char_2 = txtTime.charAt(1);
	var char_3 = txtTime.charAt(2);
	var char_4 = txtTime.charAt(3);
	var char_5 = txtTime.charAt(4);
	
	//CHECK THAT FIRST CHAR IS 0 - 2
	if (!(char_1 >= 0 && char_1 <= 2))
	{
		return false;
	}
	
	//CHECK THAT SECOND CHAR IS NUMBER AND TIME IS LESS THAN 24
	if (isNaN(char_2))
	{
		return false;
	}
	
	if (char_1 == 2)
	{
		if (char_2 >= 4)
		{
			return false;
		}
	}
	
	//CHECK THAT THIRD CHAR IS :
	if (char_3 != ":")
	{
		return false;
	}
	
	//CHECK THAT FOURTH CHAR IS 0 - 5
	if (!(char_4 >= 0 && char_4 <= 5))
	{
		return false;
	}
	//CHECK THAT FIFTH CHAR IS NUMBER
	if (isNaN(char_5))
	{
		return false;
	}
	
	return true;
	
}//end isTime

//FIND NUMBER OF OCCURRENCES OF A CHARACTER IN A STRING ... string.count('/');
String.prototype.count=function(s1) 
{ 
    return (this.length - this.replace(new RegExp(s1,"g"), '').length) / s1.length;
}

function validate_email(email) 
{
	//alert('validating email');
	var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
	if( !emailReg.test( email ) ) 
	{
		return false;
	} else 
	{
		return true;
	}
}//END VALIDATE EMAIL

function addCommas(nStr)
{
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}

//FILL GIVEN DIV WITH CITY,STATE
function fill_in_locations(div,gps_coordinates)
{
	//alert('reverse geocoding');
	
	var cell_value = gps_coordinates;
	
	if(cell_value)
	{
		var stripped_address = cell_value.replace(/\./g,'').replace(/\,/g,'').replace(/\s+/g,'').replace(/-/g,'');
		//alert(stripped_address);
		if(stripped_address && !isNaN(stripped_address))
		{
			var latlng_array = cell_value.split(",");
			var lat = latlng_array[0];
			var lng =  latlng_array[1];
			//alert(lat);
			//alert(lng);
			var latlng = new google.maps.LatLng(lat, lng);
			
			var geocoder = new google.maps.Geocoder();
			geocoder.geocode( { 'location': latlng}, function(results, status)
			{
				//alert(status);
				if (status == google.maps.GeocoderStatus.OK) 
				{
					var location_type = results[0].geometry.location_type;
					
					//alert("Google Approves =)");
					// var geo_city = results[0]['address_components'][2]['long_name'];
					// var geo_state = results[0]['address_components'][4]['short_name'];
					
					//alert(geo_city);
					//alert(geo_state);
					
					//$("#gps_city").val(results[0]['address_components'][2]['long_name']);
					//$("#gps_state").val(results[0]['address_components'][4]['short_name']);
					
					//var postCode = extractFromAdress(results[0].address_components, "postal_code");
					//var street = extractFromAdress(results[0].address_components, "route");
					var geo_city = extractFromAdress(results[0].address_components, "locality");//CITY
					var geo_state = extractFromAdress(results[0].address_components, "administrative_area_level_1");//STATE
					
					//alert(geo_city);
					//alert(geo_state);
					
					$("#"+div).html(geo_city+", "+geo_state);
				} 
				else 
				{
					alert('Uh oh!Google returned the following: ' + status);
				}
			});
		}
	}
	else
	{
		$("#"+div).html("");
	}
}

//FILL GIVEN DIV WITH CITY,STATE
function is_valid_latlng(cell_value)
{
	var stripped_address = cell_value.replace(/\./g,'').replace(/\,/g,'').replace(/\s+/g,'').replace(/-/g,'');
	//alert(stripped_address);
	if(stripped_address && !isNaN(stripped_address))
	{
		var latlng_array = cell_value.split(",");
		var new_lat = latlng_array[0];
		var new_lng =  latlng_array[1];
		alert(new_lat);
		alert(new_lng);
		var latlng = new google.maps.LatLng(new_lat, new_lng);
		//latlng = {lat: new_lat, lng: new_lng};
		
		var geocoder = new google.maps.Geocoder();
		geocoder.geocode( { 'location': latlng}, function(results, status)
		{
			alert(status);
			if (status == google.maps.GeocoderStatus.OK) 
			{
				return true; //DOESN'T WORK BECAUSE FUNCTION IS ASYNC AND DOESN'T WAIT FOR RESPONSE
			} 
			else 
			{
				return false;
			}
		});
	}
}

//HELPER FUNCTION TO GET PROPER ADDRESS COMPONENTS FROM GOOGLE GEOCODER
function extractFromAdress(components, type)
{
	for (var i=0; i<components.length; i++)
		for (var j=0; j<components[i].types.length; j++)
			if (components[i].types[j]==type) return components[i].short_name;
	return "";
}
	