function count() {
	let selector = jQuery(".timer")
	let secondsP = jQuery("[name='_time_taken']").val()
	secondsP++

	var time_shown = selector.text();
	if (!time_shown) {
		time_shown = "0:0:0";
	}

    var hour, mins, secs;
    var time_chunks = time_shown.split(":");

    hour=Number(time_chunks[0]);
    mins=Number(time_chunks[1]);
    secs=Number(time_chunks[2]);
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
	var html = `<div class="timerwrapper"><span class="h">${hour}</span><span class="seprator">:</span><span class="m">${plz(mins)}</span><span class="seprator">:</span><span class="s">${plz(secs)}</span></div>`
	// Inserting the time duration in hidden field also
	jQuery("[name='_time_taken']").val( secondsP )

    selector.html(html);
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