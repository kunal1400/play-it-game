<div class="wrap">
	<h1>How to Setup a Game</h1>
	<ol>
		<li>For social login we use Elementor Add On, so setup your own Google/Facebook credentials</li>
		<li>
			<p><b>How to create All Games Page?</b>
			<ol>
				<li>Add this shortcode <i>[all_games]</i> to All Games page</li>
			</ol>
		</p>
		<li><p><b>How to create a Game Home Page?</b></p>
			<ol>
				<li><p>Add a Shortcode <i>[create_team form_heading="Create TEAM:" email_required="yes" css_classes="" button_background_color="" font_family=""]</i> in editor. This will show the create team form.</p></li>
				<li><p>Add a Shortcode <i>[team_score_position table_label="" sno_label="" teamname_label="" timetaken_label="" clues_label="" score_label="" levels_label="" members_label="" actions_label="" css_classes="" heading_background_color="" font_family="" actions_button_color=""]</i> in editor. This will promt users to loged in and after login it will show all teams associated with this game, create team form and start playing button.</p></li>
				<li><p>Create a custom fields:</p>
					<ol>
						<li><p><i>is_game_home_page</i> and set it to <i>true</i></p></li>
						<li><p><i>score_multipler</i> and set any value, if no value is set then 10 will be default</p></li>
					</ol>
				</li>
			</ol>
		</li>
		<li><p><b>How to create Game Levels?</b></p>
			<ol>
				<li><p>Shortcode for timer: <i>
				[show_timer 
					hours_label="HOURS" 
					minutes_label="MINUTES" 
					seconds_label="SECONDS"
					hours_label_font='inherit'
					minutes_label_font='inherit'
					seconds_label_font='inherit'
					hours_font='inherit'
					minutes_font='inherit'
					seconds_font='inherit'
					hours_label_font_color='#fff'
					minutes_label_font_color='#fff'
					seconds_label_font_color='#fff'
					hours_font_color='#fff'
					minutes_font_color='#fff'
					seconds_font_color='#fff'
					hours_background_color="#cccccc9e" 
					minutes_background_color="#cccccc9e" 
					seconds_background_color="#cccccc9e" 
					hours_label_margin='1px 1px'
					minutes_label_margin='1px 1px'
					seconds_label_margin='1px 1px'
					hours_margin='1px 1px 1px 1px'
					minutes_margin='1px 1px 1px 1px'
					seconds_margin='1px 1px 1px 1px'
					css_classes=''
					boxs_padding='20'
				]</i></p></li>
				<li><p>Shortcode for next step: <i>[next_step_form answer="" css_classes="" button_background_color="" font_family=""]</i></p></li>
				<li><p>Shortcode for clue: <i>[show_clue label="" seconds_to_add="50" image_url="" text="" css_classes="" background_color="" text_color="" font_family=""]</i></p></li>
			</ol>
		</li>
		<li><p><b>Allow users to login from anywhere by email</b>(Visible only if user is not loged in)</p>
			<ol>
				<li><p>Shortcode for Login By Code: <i>[join_game_by_code redirect_url="https://reactdemo.play-it.co.il/wp-admin" label="Add Code" submit_button_label="Submit" close_button_label="Close" modal_title="Join Team By Code" background_color="" font_family=""]</i></p></li>
			</ol>
		</li>
		<li><p><b>Allow users to logout from anywhere</b>(Visible only if user is loged in)</p>
			<ol>
				<li><p>Shortcode for Login By Code: <i>[logout_button redirect_url="https://reactdemo.play-it.co.il/wp-admin" label="Add Code" background_color="" font_family=""]</i></p></li>
			</ol>
		</li>
	</ol>
</div>