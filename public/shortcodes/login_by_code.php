<?php
echo '<button type="button" 
		class="btn btn-primary" 
		data-toggle="modal" 
		data-target="#join_game_by_code_modal" 
		style="background-color:'.$attributes['background_color'].';border: 1px solid '.$attributes['background_color'].';font-family:'.$attributes['font_family'].'"
	>'.$attributes['label'].'</button>
	<div class="modal fade" id="join_game_by_code_modal" tabindex="-1" role="dialog" aria-labelledby="join_game_by_code_modal_label" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel">'.$attributes['modal_title'].'</h4>
	      </div>
			<form onsubmit="return applyCodeForGame()" method="post" id="codeLoginForm">
	      		<div class="modal-body">
		        	<div class="md-form mb-5">
						<label for="form34">User Name</label>
						<input type="text" id="form34" name="user_name" class="form-control" value="">
			        </div>
			        <div style="display:none" class="md-form mb-5">
						<label for="form35">Code</label>
						<input type="text" id="form35" name="user_code" class="form-control" value="">
			        </div>
		      	</div>
	      		<div class="modal-footer">
	      			<input type="hidden" name="game_id" value="'.$attributes['game_id'].'" />
	      			<input type="hidden" name="redirect_url" value="'.$attributes['redirect_url'].'" />
	        		<button type="button" class="btn btn-secondary" data-dismiss="modal">'.$attributes['close_button_label'].'</button>
	        		<button type="submit" class="btn btn-primary">'.$attributes['submit_button_label'].'</button>
	      		</div>
	    	</form>
	    </div>
	  </div>
	</div>';