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
	if (current_env && selector.length > 0 ) {
		let cookieName = `_current_env_id_${current_env.id}`
		let secondsP = jQuery("[name='_time_taken']").val()
		if (!secondsP) {
			secondsP = 0
		}

		// var time_shown = selector.text();
		let time_shown = getCookie( cookieName )
		if (!time_shown) {
			time_shown = "00:00:00";
		}

	    let time_chunks = time_shown.split(":");
	    let hour = Number(time_chunks[0]);
	    let mins = Number(time_chunks[1]);
	    let secs = Number(time_chunks[2]);

		secondsP = parseInt(secs) + parseInt(mins*60) + parseInt(hour*60*60)

	    secs++;
		if (secs==60){
			secs = 0;
			mins = mins + 1;
		}
		if (mins==60){
			mins = 0;
			hour = hour + 1;
		}
		if (hour==13){
			hour = 1;
		}

		console.log( secs, `${plz(hour)}:${plz(mins)}:${plz(secs)}`, secondsP, "secondsP")

		// secondsP++
	    selector.find(".h>h1").html(plz(hour));
	    selector.find(".m>h1").html(plz(mins));
	    selector.find(".s>h1").html(plz(secs));

		setCookie( cookieName, `${plz(hour)}:${plz(mins)}:${plz(secs)}`, 7);

		// Inserting the time duration in hidden field also
		jQuery("[name='_time_taken']").val( secondsP )

	}
}

function plz(digit) {
    var zpad = digit + '';
    if (digit < 10) {
        zpad = "0" + zpad;
    }
    return zpad;
}

function convertSerializeArrayToObject( serializeArray ) {
    var o = {};
    jQuery.each(serializeArray, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
}

function showclue(e) {
	let clickedElement = jQuery(e).parent().find('.clue').toggle()
	let secondsToAdd = jQuery(e).attr("data-secondsToAdd")

	// if ( clickedElement.hasClass("showclue") ) {
	// 	clickedElement.removeClass("showclue")
	// } else {
	// 	clickedElement.addClass("showclue")
	// }
	jQuery("input, input[type='submit']").attr("disabled", true)
	console.log(current_env, secondsToAdd, "+current_env+secondsToAdd+")
	if (secondsToAdd && parseInt(secondsToAdd) > 0) {
		jQuery.ajax({
			url: current_env.ajax_url,
			method: 'POST',
			data: {
				action: "add_clue",
				secondsToAdd,
				current_level_id: current_env.current_level_id,
				current_team_id: current_env.current_team_id,
				current_user_id: current_env.current_user_id,
				current_game_id: current_env.current_game_id
			}
		})
		.done(function( response ) {
			jQuery("input, input[type='submit']").attr("disabled", false)
			console.log( response )
		})
	}
}

// function toggleModal(e) {
// 	let targetElement = jQuery(e).attr("data-target")
// 	if(targetElement){
// 		// jQuery(`${targetElement}`).modal("show")
// 		jQuery(`${targetElement}`).addClass("show")
// 	}
// }

// function closeModal(e) {
// 	let targetElement = jQuery(e).closest("div.modal").attr("id")
// 	if(targetElement){
// 		// jQuery(`#${targetElement}`).modal("hide")
// 		jQuery(`#${targetElement}`).removeClass("show")
// 	}
// }

function applyCodeForGame() {
	var formData = jQuery("#codeLoginForm").serializeArray()
	var formObj = convertSerializeArrayToObject(formData)

	if ( formObj['user_name'] ) {
		jQuery(`[name="${formObj['user_name']}"]`).removeClass("required")
		jQuery("#codeLoginForm").find("[type='submit']").attr("disabled", true).text("Submitting...")
		formObj.action = "check_user_name"
		jQuery.ajax({
			url: playit_env.ajax_url,
			method: 'POST',
			data: formObj
		})
		.done(function( response ) {
			jQuery("#codeLoginForm").find("[type='submit']").attr("disabled", false).text("Submit")
			let data = JSON.parse(response)
			console.log( data, "dataaaaa" )
			if ( data.status ) {
				window.location.href = data.redirect_url
			}
		})
	} else {
		jQuery(`[name="${formObj['user_name']}"]`).addClass("required")
	}
	return false
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
