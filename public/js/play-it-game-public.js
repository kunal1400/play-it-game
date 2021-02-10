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
});