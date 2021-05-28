<div class="wrap">
	<h1>How to Setup a Game</h1>
	<ol>
		<li>From the Game Page Settings, uncheck "Is Team Game?" checkbox</li>
		<li>
			<p><b>How to create All Games Page?</b>
			<ol>
				<li>Add this shortcode <i>[all_games]</i> to All Games page</li>
			</ol>
		</p>
		<li><p><b>How to create a Game Home Page?</b></p>
			<ol>
				<li><p>Add a Shortcode <i>[create_team form_heading="Create TEAM:" email_required="yes" css_classes=""]</i> in editor. This will show the create team form.</p></li>
				<li><p>Add a Shortcode <i>[team_score_position table_label="" sno_label="" teamname_label="" timetaken_label="" clues_label="" score_label="" levels_label="" members_label="" actions_label="" css_classes="" heading_background_color="" font_family=""]</i> in editor. This will promt users to loged in and after login it will show all teams associated with this game, create team form and start playing button.</p></li>
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
				<li><p>Shortcode for timer: <i>[show_timer hours_label="HOURS" minutes_label="MINUTES" seconds_label="SECONDS" hours_background_color="#cccccc9e" minutes_background_color="#cccccc9e" seconds_background_color="#cccccc9e" hours_font_color="#FFF" minutes_font_color="#FFF" seconds_font_color="#FFF"]</i></p></li>
				<li><p>Shortcode for next step: <i>[next_step_form answer="" css_classes=""]</i></p></li>
				<li><p>Shortcode for clue: <i>[show_clue label="" seconds_to_add="50" image_url="" text="" css_classes=""]</i></p></li>
			</ol>
		</li>
		<li><p><b>Allow users to login from anywhere by email</b></p>
			<ol>
				<li><p>Shortcode for Login By Code: <i>[join_game_by_code redirect_url="https://reactdemo.play-it.co.il/wp-admin" label="Add Code" submit_button_label="Submit" close_button_label="Close" modal_title="Join Team By Code"]</i></p></li>
			</ol>
		</li>
	</ol>
</div>