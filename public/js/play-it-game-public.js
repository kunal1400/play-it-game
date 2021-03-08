function setCookie(name, value, daysToLive) {
    // Encode value in order to escape semicolons, commas, and whitespace
    var cookie = name + "=" + encodeURIComponent(value);
    
    if(typeof daysToLive === "number") {
        /* Sets the max-age attribute so that the cookie expires
        after the specified number of days */
        cookie += "; max-age=" + (daysToLive*24*60*60)+"; path=/";
        
        document.cookie = cookie;
    }
}

function getCookie(name) {
    // Split cookie string and get all individual name=value pairs in an array
    var cookieArr = document.cookie.split(";");
    
    // Loop through the array elements
    for(var i = 0; i < cookieArr.length; i++) {
        var cookiePair = cookieArr[i].split("=");
        
        /* Removing whitespace at the beginning of the cookie name
        and compare it with the given string */
        if(name == cookiePair[0].trim()) {
            // Decode the cookie value and return
            return decodeURIComponent(cookiePair[1]);
        }
    }
    
    // Return null if not found
    return null;
}

function count() {
	let selector = jQuery(".timer")
	if (selector.length > 0 && current_env ) {
		let cookieName = `_current_env_id_${current_env.id}`
		let secondsP = jQuery("[name='_time_taken']").val()
		secondsP++

		// var time_shown = selector.text();
		let time_shown = getCookie( cookieName )
		if (!time_shown) {
			time_shown = "00:00:00";
		}

	    let time_chunks = time_shown.split(":");
	    let hour = Number(time_chunks[0]);
	    let mins = Number(time_chunks[1]);
	    let secs = Number(time_chunks[2]);
	    secs++;
		if (secs==60){
			secs = 0;
			mins=mins + 1;
		} 
		if (mins==60){
			mins=0;
			hour=hour + 1;
		}
		if (hour==13){
			hour=1;
		}
		console.log( secs, `${plz(hour)}:${plz(mins)}:${plz(secs)}`)
		let html = `<div class="timerwrapper"><span class="h">${plz(hour)}</span><span class="seprator">:</span><span class="m">${plz(mins)}</span><span class="seprator">:</span><span class="s">${plz(secs)}</span></div>`
		setCookie( cookieName, `${plz(hour)}:${plz(mins)}:${plz(secs)}`, 7);

		// Inserting the time duration in hidden field also
		jQuery("[name='_time_taken']").val( secondsP )

	    selector.html(html);
	}
}
 
function plz(digit) { 
    var zpad = digit + '';
    if (digit < 10) {
        zpad = "0" + zpad;
    }
    return zpad;
}

jQuery(document).ready(function(){
  	jQuery.validator.addMethod("multiemail", function (value, element) {
        if (this.optional(element)) {
            return true;
        }
        var emails = value.split(','),
            valid = true;
        for (var i = 0, limit = emails.length; i < limit; i++) {
            value = emails[i];
            valid = valid && jQuery.validator.methods.email.call(this, value, element);
        }
        return valid;
    }, "Please separate email addresses with a comma and do not use spaces.");


	jQuery("#emailFrm").validate({
	    errorElement:'div',
	    rules: {
	    	playit_team_name: {
	            required: true
	        },
	        playit_member_emails: {
	            required: true,
	            multiemail:true
	        }
	    },
	    messages: 
	    {
	        playit_member_emails: {
	            required:"Please enter email address."
	        },
	        playit_team_name: {
	            required:"Please enter team name."
	        }
	    }
	});

	// jQuery(".timer")
	setInterval(count, 1000)
});