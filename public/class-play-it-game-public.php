<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/kunal1400
 * @since      1.0.0
 *
 * @package    Play_It_Game
 * @subpackage Play_It_Game/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Play_It_Game
 * @subpackage Play_It_Game/public
 * @author     Kunal Malviya <lucky.kunalmalviya@gmail.com>
 */
class Play_It_Game_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Play_It_Game_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Play_It_Game_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/play-it-game-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		
		wp_enqueue_script('jquery-validate-min', plugin_dir_url( __FILE__ ) . 'js/jquery.validate.min.js', 
			array( 'jquery' ), $this->version, false );

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/play-it-game-public.js', array( 'jquery' ), $this->version, false );

	}

	public function page_load_actions() {
		global $post;		
		global $table_prefix, $wpdb;		
	}

	public function game_home_page_cb( $atts ) {
		global $table_prefix;
		global $wpdb;
		global $wp;
		global $post;

		// $attributes = shortcode_atts( array(
		// 	'productid' => 'current'
		// ), $atts );

		/**
		* #1: Getting all the levels of the current game (i.e Page)
		**/
		$gameLevels = get_pages(array(
			'child_of' => $post->ID
		));		

		if( !is_user_logged_in() ) {
			/**
			* #2: Show login/signup form if user is not loged-in
			* https://developer.wordpress.org/reference/functions/wp_login_form/
			**/
			$loginMessage = '<h2><center>Please login to play this game</center></h2>';
			$loginMessage .= wp_login_form(array(
				'echo' => false,
				// 'redirect' => home_url( $wp->request )
				// 'redirect' => home_url( 'all-games' )
			));
			$loginMessage .= '<a href="'.home_url("/wp-login.php?action=register").'">Sign Up</a>';
			return $loginMessage;
		}
		else {			
			/**
			* #2: Show all games link
			**/
			$html = "";			
			$html .= "<div style='text-align:right'><a style='color: #48bb48;' href='".home_url( 'all-games' )."'>View All My Games</a></div>";

			if ( !empty($_GET['already_emails']) ) {
				$html .= "<div style='color: #f52f2f;font-style: italic;'>".$_GET['already_emails']." already associated with other teams</div>";
			}
			
			/**
			* #3: Getting all the teams of the current user
			**/
			$userTeamIds = $this->getUserTeamId( get_current_user_id() );

			/**
			* #4: Listing all teams of the current game
			**/
			$teams = $this->getAllTeams( $post->ID );			
			if ( is_array($teams) && count($teams) > 0 ) {
				$html .= "<h3>Choose Team:</h3>";				
				$html .= "<table id='teams'>
					<thead>
						<th width='100'>S.No</th>
						<th>Team Name</th>
						<th>Team Position</th>
						<th>Member Emails</th>
						<th>Action</th>
					</thead>";
				foreach ($teams as $i => $team) {
					/**
					* #5: Showing team info in table row and showing the buttons
					**/
					$buttons = "";
					// User is not associated to any team
					if (count($userTeamIds) == 0) {
						$buttons = '<a href="?game_action=joinme&team_id='.$team['id'].'" class="button button-blue">Join</a>';
					}
					// User is associated to this team then show Start Playing button				
					else if ( in_array($team['id'], $userTeamIds) ) {
						$nextLevelLink = $this->getGameNextLevelLink( $post->ID );						
						if ( !$nextLevelLink ) {
							$nextLevelLink = $post->guid;
						}
						$buttons = '<a href="'.$nextLevelLink.'&currentTeamId='.$team['id'].'" class="button button-black">Start Playing</a>';
					} else {
						$buttons = '-';
					}

					$html .= '<tr>
						<td>'.($i+1).'</td>
						<td>'.$team['team_name'].'</td>
						<td>-</td>
						<td>'.$team['members_email'].'</td>
						<td>'.$buttons.'</td>
					</tr>';
				}
				$html .= "</table><br/>";				
			}
			else {
				$html .= "<p><i>No teams for this game</i></p>";
			}		
			
			/**
			* This is very important line of code which is giving the number of levels cleared for the current game by current user. It is not required now so commenting it
			**/
			/*$html .= "<table>";
			$html .= "<tr>
				<th width='100'>Level Id</th>
				<th>Level Name</th>
				<th>Time Taken</th>
				<th>Is Cleared</th>
			</tr>";			
			$currentUserId = get_current_user_id();
			foreach ($gameLevels as $i => $levelData) {
				$levelInfo = $this->getUserGameLevel( $currentUserId, $levelData->ID );
				if ( is_array($levelInfo) && count($levelInfo) > 0 ) {
					if ( isset($levelInfo['is_cleared']) ) {
						$html .= "<tr>
							<td>".$levelData->ID."</td>
							<td><a href='".$levelData->guid."'>".$levelData->post_title."</a></td>
							<td>".$levelInfo['time_taken']."</td>
							<td>".($levelInfo['is_cleared'] == 1 ? 'Yes' : 'In Progress')."</td>
						</tr>";
					} 
					else {
						$html .= "<tr>
							<td>".$levelData->ID."</td>
							<td>".$levelData->post_title."</td>
							<td>".$levelInfo['time_taken']."</td>
							<td>-</td>
						</tr>";
					}
				}
				else if( $i === 0 ){
					$html .= "<tr><td>".$levelData->ID."</td>
						<td><a href='".$levelData->guid."'>".$levelData->post_title."</a></td>
						<td>-</td>
						<td>-</td>
					</tr>";
				}
				else {
					$html .= "<tr><td>".$levelData->ID."</td>
						<td>".$levelData->post_title."</td>
						<td>-</td>
						<td>-</td>
					</tr>";
				}
			}
			$html .= "</table>";*/

			$html .= '<h2>Create Team:</h2><div class="form-container">
				<form id="emailFrm" method="post">
					<p>
						<label for="playit_team_name">Team Name</label>
						<input type="text" id="playit_team_name" name="playit_team_name" placeholder="Your name..">
					</p>
					<p>
						<label>Member Emails:</label>
                        <input type="text" id="example_emailBS" name="playit_member_emails">
					</p>
					<p>
						<input type="hidden" name="playit_current_page_id" value="'.$post->ID.'">
						<input type="submit" value="Submit">
					</p>
				</form>
			</div>';

			return $html;
		}		
	}

	public function init_actions() {
		global $wp;
		// if ( 'page' === get_post_type() AND is_singular() ) {

		if( isset($_REQUEST['playit_member_emails']) && isset($_REQUEST['playit_team_name']) ) {
			$playit_team_name 		= $_REQUEST['playit_team_name'];
			$playit_member_emails 	= explode(",", $_REQUEST['playit_member_emails']);
			$playit_current_page_id = $_REQUEST['playit_current_page_id'];
			$playit_team_created_by = get_current_user_id();

			// Checking if member emails is associated in team or not
			$emailsToInsert = array();
			$alreadyEmails = array();
			if ($playit_member_emails && count($playit_member_emails)) {
				foreach ($playit_member_emails as $i => $email) {
					$teamForThisEmail = $this->getUserTeamsByEmail($email);
					if (is_array($teamForThisEmail) && count($teamForThisEmail) > 0) {
						$alreadyEmails[] = $email;						
					}
					else {
						$emailsToInsert[] = $email;
					}					
				}
			}			

			// If error is present then show error message
			if (is_array($emailsToInsert) && count($emailsToInsert) > 0) {
				$manageTeamResponse = $this->manageTeam($playit_team_name, implode(",", $emailsToInsert), $playit_current_page_id, $playit_team_created_by);
				if ($manageTeamResponse) {
					wp_redirect( $_SERVER['HTTP_REFERER'] );
					exit;
				}
			}

			// If error is present then show error message
			if (is_array($alreadyEmails) && count($alreadyEmails) > 0) {
				$redirectWithQueryString = $_SERVER['HTTP_REFERER']."?error_msg=true&already_emails=".implode(",", $alreadyEmails);
				wp_redirect( $redirectWithQueryString );
				exit;
			}
		}

		if ( isset($_GET['game_action']) && $_GET['game_action'] == 'joinme' && isset($_GET['team_id'])) {
			// Getting current user email
			$currentUserId 	= get_current_user_id();
			if ( !empty($currentUserId) ) {
				$teamId = $_GET['team_id'];
				$currentUser = get_userdata($currentUserId);

				$teamInfo = $this->getTeamById($teamId);
				if ( is_array($teamInfo) ) {
					$allEmails 	 = explode(",", $teamInfo['members_email']);
					$allEmails[] = $currentUser->data->user_email;
					$allEmails 	 = array_unique($allEmails);
					$allEmailStrings = implode(",", $allEmails);

					$this->updateEmailInTeam( $allEmailStrings, $teamId );
				}
			}					
		}
		// }
	}

	public function manageTeam($teamName, $memberEmails, $gameId, $createdBy) {
		global $table_prefix, $wpdb;
		$tblname = $table_prefix . 'gm_teams';
		
		// Checking the attribute value in db
		$row = $wpdb->get_row( "SELECT * FROM $tblname WHERE game_id = $gameId AND created_by=$createdBy", ARRAY_A );

		if ( is_array($row) && count($row) > 0 ) {
	    	$sql = "UPDATE $tblname SET team_name='$teamName', members_email='$memberEmails' WHERE game_id = $gameId AND created_by=$createdBy";
		} 
		else {
			$sql = "INSERT INTO $tblname (team_name, members_email, game_id, created_by) VALUES ('$teamName', '$memberEmails', $gameId, $createdBy)";
		}
		return $wpdb->query($sql);
	}
	
	/**
	* Returning the games associated with the current logged in user
	**/
	public function list_user_games_cb( $atts ) {
		$attributes = shortcode_atts( array(
			'productid' => 'current'
		), $atts );

		if( !is_user_logged_in() ) {			
			return;
		}
		else {
			$html = "";
			$html .= "<p>Your Games</p>";
			$currentUserId = get_current_user_id();	
			$userGames = $this->getUserTeamsById( $currentUserId );
			if ( is_array($userGames) && count($userGames) > 0 ) {
				$html .= '<table>
				<tr>
					<th width="30">S.No</th>
					<th>Name</th>
					<th>Associated Team</th>
				</tr>';
				foreach ($userGames as $i => $gameInfo) {
					if (!empty($gameInfo['game_id'])) {
						$pageData = get_post( $gameInfo['game_id'] );
						$html .= '<tr>
							<th>'.($i+1).'</th>
							<th><a href="'.$pageData->guid.'">'.$pageData->post_title.'</a></th>
							<th>'.$gameInfo['team_name'].'</th>
						</tr>';

						$html .= '<p></p>';
					}
				}
				$html .= '</table>';				
			}
			
			return $html;
		}
	}

	public function getAllTeams( $gamesId ) {
		global $wpdb;
		$tblname = $wpdb->prefix . 'gm_teams';
		$sql = "SELECT * FROM $tblname WHERE game_id = $gamesId";
		return $wpdb->get_results($sql, ARRAY_A);
	}

	public function getTeamById( $teamId ) {
		global $wpdb;
		$tblname = $wpdb->prefix . 'gm_teams';
		$sql = "SELECT * FROM $tblname WHERE id = $teamId";
		return $wpdb->get_row($sql, ARRAY_A);
	}

	public function updateEmailInTeam( $emails, $teamId ) {
		global $wpdb;
		$tblname = $wpdb->prefix . 'gm_teams';		
		return $wpdb->query( "UPDATE $tblname SET members_email='$emails' WHERE id = $teamId" );
	}

	public function getUserTeamsById( $userId ) {
		global $wpdb;
		$tblname = $wpdb->prefix . 'gm_teams';
		$sql = "SELECT * FROM $tblname WHERE created_by=$userId";
		return $wpdb->get_results($sql, ARRAY_A);
	}

	public function getUserTeamsByEmail( $userEmail ) {
		global $wpdb;
		$tblname = $wpdb->prefix . 'gm_teams';
		$sql = "SELECT * FROM $tblname WHERE members_email LIKE '%$userEmail%'";
		return $wpdb->get_results($sql, ARRAY_A);
	}

	public function getUserTeamId( $userId ) {
		$userTeams = $this->getUserTeamsById( $userId );		
		$teamIds = array();
		if ( is_array($userTeams) && count($userTeams) > 0 ) {
			foreach ($userTeams as $i => $userTeam) {
				$teamIds[] = $userTeam['id'];
			}
		}
		return $teamIds;
	}

	public function getUserGameLevel( $userId, $levelId ) {
		global $wpdb;
		$tblname = $wpdb->prefix . 'gm_games';
		$sql = "SELECT * FROM $tblname WHERE level_id=$levelId AND user_id=$userId";
		return $wpdb->get_row($sql, ARRAY_A);
	}

	public function manageGameLevel($team_id, $game_id, $user_id, $level_id, $time_taken, $is_cleared) {
		global $table_prefix, $wpdb;
		$time_taken = time();
		$tblname = $table_prefix . 'gm_games';
		
		// Checking the attribute value in db
		$row = $this->getUserGameLevel($user_id, $level_id);

		if ( is_array($row) && count($row) > 0 ) {
	    	$sql = "UPDATE $tblname SET team_id=$team_id, game_id=$game_id, level_id=$level_id, user_id=$user_id, time_taken='$time_taken', is_cleared=$is_cleared WHERE level_id=$level_id AND user_id=$user_id AND team_id=$team_id";
		} 
		else {
			$sql = "INSERT INTO $tblname (team_id, game_id, level_id, user_id, is_cleared, time_taken) VALUES ($team_id, $game_id, $level_id, $user_id, $is_cleared, '$time_taken')";
		}
		return $wpdb->query($sql);
	}

	public function next_step_form_cb( $atts ) {
		global $post;		

		$attributes = shortcode_atts( array('answer' => ''), $atts );
		
		$errorMessage = "";

		// #1: Getting the current user id
		$currentUserId = get_current_user_id();				

		/**
		* #2: Getting the game home page info
		**/
		$gameHomePage = null;
		if ( !empty($post->post_parent) ) {
			$gameHomePage = get_post($post->post_parent);
		}

		/**
		* #2: If user is not loged in then redirect them to game home page
		**/
		if (!$currentUserId) {
			wp_redirect($gameHomePage->guid);
			exit;
		}

		/**
		* #3: If user doesn't belongs to any team then redirect it to game page
		**/
		$currentTeamId = 0;
		if (!empty($_GET['currentTeamId'])) {
			$currentTeamId = $_GET['currentTeamId'];
		}
		else {
			// wp_redirect($gameHomePage->guid);
			// exit;
			return "Please choose a team";
		}

		// // #3: Getting All Levels
		// $allLevels = $this->getOtherLevels( $post->ID );

		// // #4: Getting Next Level Link
		// $nextLevel = $this->get_next($allLevels, $post->ID);

		$nextLevel = $this->getGameNextLevelLink( $post->ID )."&currentTeamId=$currentTeamId";
		if ( !$nextLevel ) {
			$nextLevel = $gameHomePage->guid;
		}

		/**
		* #5: Process to save data in db after user answer a level, here we are also 
		* checking if user is on page and so that it will not affect other pages.
		**/
		if ( 'page' === get_post_type() AND is_singular() ) {		

			/**
			* #6: If current is page is game level then only perform db operations
			**/
			if ( !empty( $post->post_parent ) ) {
				if ( !empty($_POST['_next_step_answer']) ) {
					// 45HH
					if ( strtolower($_POST['_next_step_answer']) == strtolower($attributes['answer']) ) {

						$gameLevelRes = $this->manageGameLevel($currentTeamId, $post->post_parent, $currentUserId, $post->ID, time(), 1);

						if ($gameLevelRes) {
							wp_redirect($nextLevel);
							exit;
						}
					}
					else {
						$errorMessage = "<p style='color:red'>Answer Not Matched</p>";
					}			
				}
			}

			/**
			* #7: Checking if current user has already cleared the level or not
			**/
			$isCurrentLevelCleared = false;
			$userLevelInfo = $this->getUserGameLevel($currentUserId, $post->ID);
			if ( is_array($userLevelInfo) && count($userLevelInfo) > 0 ) {
				// Level Cleared
				if (isset($userLevelInfo['is_cleared']) && $userLevelInfo['is_cleared'] == 1 ) {
					$isCurrentLevelCleared = true;					
				}				
			}
		}		

		/**
		* #7: Finally rendering the form/next level message
		**/
		if ($isCurrentLevelCleared) {
			return '<div>You already cleared this level <a href="'.$nextLevel.'">click here</a> to go next level</div>';
		}
		else {
			return '<form method="post" action="">
			<input type="text" name="_next_step_answer" />
			<input type="submit" value="Submit" />
		</form>'.$errorMessage;
		}		
	}

	/**
	* $id can be gameid or level id
	**/
	public function getOtherLevels( $pageId ) {
		$levelsWithUrl = array();
		$levelInfo = get_post( $pageId );
		
		// Implies that it is level
		if ( isset($levelInfo->post_parent) && $levelInfo->post_parent != 0 ) {
			$gameLevels = get_pages(array(
				'child_of' => $levelInfo->post_parent
			));
		}
		else {
			$gameLevels = get_pages(array(
				'child_of' => $pageId
			));			
		}

		foreach ($gameLevels as $id => $level) {
			$levelsWithUrl[$level->ID] = $level->guid;				
		}

		return $levelsWithUrl;
	}

	public function get_next($array, $key) {
	   	$currentKey = key($array);
	   	while ($currentKey !== null && $currentKey != $key) {
	    	next($array);
	       	$currentKey = key($array);
	   	}
	   	return next($array);
	}

	public function all_games_cb( $atts ) {
		// $attributes = shortcode_atts( array(
		// 	'productid' => 'current'
		// ), $atts );
		
		$allPages = get_pages(array(
			'child_of' => 0,
			'echo' => false,
			'meta_key' => 'is_game_home_page',
			'meta_value' => 'true',
		));

		$html = '';
		if ( is_array($allPages) && count($allPages) > 0 ) {
			$html .= '<ul>';
			foreach ($allPages as $key => $page) {					
				$html .= '<li><a href="'.$page->guid.'">'.$page->post_title.'</a></li>';
			}
			$html .= '<ul>';
		}
		
		return $html;	
	}

	public function getGameNextLevelLink( $gameId ) {
		$allLevels = $this->getOtherLevels( $gameId );		
		$nextLevel = $this->get_next( $allLevels, $gameId );
		if ( !$nextLevel ) {
			// https://stackoverflow.com/questions/1921421/get-the-first-element-of-an-array
			return array_shift(array_values($allLevels));
			// var_dump(first($allLevels));
			// return $allLevels[0];
		} 
		else {
			return $nextLevel;
		}
	}
}