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

		global $post;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name."public.css", plugin_dir_url( __FILE__ ) . 'css/play-it-game-public.css', array(), $this->version, 'all' );

		wp_enqueue_style( $this->plugin_name."bootstrap.grid", plugin_dir_url( __FILE__ ) . 'css/bootstrap.grid.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		global $post;

		wp_enqueue_script('bootstrap-min', plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js',
			array( 'jquery' ), $this->version, false );

		wp_enqueue_script('jquery-validate-min', plugin_dir_url( __FILE__ ) . 'js/jquery.validate.min.js',
			array( 'jquery' ), $this->version, false );

		wp_enqueue_script( $this->plugin_name.'play_it_js', plugin_dir_url( __FILE__ ) . 'js/play-it-game-public.js', array( 'jquery' ), $this->version, false );

		wp_localize_script( $this->plugin_name.'play_it_js', 'playit_env', array(
			"ajax_url" => admin_url('admin-ajax.php')
		));

		if ( $post && $post->ID ) {
			$isTeamGame = get_post_meta( $post->ID, 'is_team_game', true );
			if ( $isTeamGame && $isTeamGame == "true" && !empty($_GET['currentTeamId']) ) {
				wp_localize_script( $this->plugin_name.'play_it_js', 'current_env', array(
					"id" => get_current_user_id().'_'.$post->ID,
					"is_team_game" => $isTeamGame,
					"current_user_id" => get_current_user_id(),
					"current_team_id" => $_GET['currentTeamId'],
					"current_level_id" => $post->ID,
					"current_game_id" => $post->post_parent,
					"ajax_url" => admin_url('admin-ajax.php')
				));
			}
			else {
				wp_localize_script( $this->plugin_name.'play_it_js', 'current_env', array(
					"id" => get_current_user_id().'_'.$post->ID,
					"is_team_game" => $isTeamGame,
					"current_user_id" => get_current_user_id(),
					"current_team_id" => 0,
					"current_level_id" => $post->ID,
					"current_game_id" => $post->post_parent,
					"ajax_url" => admin_url('admin-ajax.php')
				));
			}

		}

	}

	public function page_load_actions() {
		global $post;
		global $table_prefix, $wpdb;
		if ( is_user_logged_in() && 'page' === get_post_type() AND is_singular() ) {
			$allLevels = $this->getOtherLevels( $post->ID );
			if ( is_array($allLevels) && count($allLevels) ) {
				foreach ($allLevels as $id => $url) {
					if ( $id !== $post->ID ) {
						$cookieName = "_current_env_id_".get_current_user_id().'_'.$id;
						if ( isset($_COOKIE[$cookieName]) ) {
							unset($_COOKIE[$cookieName]);
							setcookie( $cookieName, null,
								array(
									'expires' => date('Y-m-d', strtotime('-8 days')),
									'path' => '/',
								)
							);
						}
					}
				}
			}
		}
	}

	public function getTeamPosition( $gameId, $teamId ) {
		global $post;
		/**
		* #1: Getting all the levels of the current game (i.e Page)
		**/
		$gameLevels = get_pages(array(
			'child_of' => $post->ID
		));
		// getLevelInfo( $teamId, $levelId );
	}

	public function game_home_page_cb( $attributes ) {
		global $table_prefix;
		global $wpdb;
		global $wp;
		global $post;

		$atts = shortcode_atts( array(
			'table_label' => 'Choose Team:',
			'sno_label' => 'S.No',
			'teamname_label' => 'Team Name',
			'timetaken_label' => 'Time Taken (In Sec)',
			'clues_label' => 'Clues',
			'score_label' => 'Score',
			'levels_label' => 'Levels',
			'members_label' => 'Member Emails',
			'actions_label' => 'Action',
			'css_classes' => '',
		), $attributes );

		/**
		* #1: Getting all the levels of the current game (i.e Page)
		**/
		$gameLevels = get_pages(array(
			'child_of' => $post->ID
		));

		/**
		* #2: Getting the multipler
		**/
		$score_multipler = get_post_meta( $post->ID, 'score_multipler', true);
		if (!$score_multipler) {
			$scoreMultipler = 10;
		} else {
			$scoreMultipler = (int)$score_multipler;
		}

		if( !is_user_logged_in() ) {
			// /**
			// * #2: Show login/signup form if user is not loged-in
			// * https://developer.wordpress.org/reference/functions/wp_login_form/
			// **/
			// $loginMessage = '<h2><center>Please login to play this game</center></h2>';
			// $loginMessage .= wp_login_form(array(
			// 	'echo' => false,
			// 	// 'redirect' => home_url( $wp->request )
			// 	// 'redirect' => home_url( 'all-games' )
			// ));
			// $loginMessage .= '<a href="'.home_url("/wp-login.php?action=register").'">Sign Up</a>';
			// return $loginMessage;
			return "<div class='".$atts['css_classes']."'><h2><center>Please login to play this game</center></h2></div>";
		}
		else {
			/**
			* #3: Show all games link
			**/
			$html = "";
			$all_games_page_id = get_option('all_games_page');
	    if ($all_games_page_id ) {
				$allGamePage = get_post($all_games_page_id);
				$html .= "<div style='text-align:right'><a style='color: #48bb48;' href='".$allGamePage->guid."'>View All Games</a></div>";
	    }

			if ( !empty($_GET['already_emails']) ) {
				$html .= "<div style='color: #f52f2f;font-style: italic;'>".$_GET['already_emails']." already associated with other teams</div>";
			}

			/**
			* #4: Getting all the teams of the current user
			**/
			$userTeamIds = $this->getUserTeamId( get_current_user_id() );

			/**
			* #5: Listing all teams of the current game
			**/
			$otherLevels = $this->getOtherLevels($post->ID);

			/**
			* #6: Checking if current game is team game or not
			**/
			$isTeamGame = get_post_meta( $post->ID, 'is_team_game', true );
			if ( $isTeamGame && $isTeamGame == "true" ) {
				$isTeamGame = true;
			}
			else {
				$isTeamGame = false;
			}

			$teams = $this->getAllTeams( $post->ID );
			if ( is_array($teams) && count($teams) > 0 ) {
				$html .= "<h3>".$atts['table_label']."</h3>";
				$html .= "<table id='teams'>";
				$html .= "<thead>";
				$html .= "<th>".$atts['sno_label']."</th>";
				if ($isTeamGame) {
					$html .= "<th>".$atts['teamname_label']."</th>";
				}
				$html .= "<th width='100'>".$atts['timetaken_label']."</th>";
				$html .= "<th width='100'>".$atts['clues_label']."</th>";
				$html .= "<th width='100'>".$atts['score_label']."</th>";
				$html .= "<th width='20'>".$atts['levels_label']."</th>";
				$html .= "<th>".$atts['members_label']."</th>";
				$html .= "<th>".$atts['actions_label']."</th>";
				$html .= "</thead>";

				foreach ($teams as $i => $team) {
					$memberUserNames = array();
					if ($team['members_email']) {
						$memberEmails = explode(",", $team['members_email']);
						foreach ($memberEmails as $j => $email) {
							$memberInfo = get_user_by('email', $email);
							if ( !empty($memberInfo->data->user_login) ) {
								$memberUserNames[] = $memberInfo->data->user_login;
							} else {
								$memberUserNames[] = $email;
							}
						}
					}

					/**
					* #6: Showing team info in table row and showing the buttons
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
						$buttons = '<a href="'.add_query_arg('currentTeamId', $team['id'], $nextLevelLink ).'" class="button button-black">Start Playing</a>';
					} else {
						$buttons = '-';
					}

					$html .= '<tr>';
					$html .= '<td>'.($i+1).'</td>';
					if ($isTeamGame) {
						$html .= '<td>'.$team['team_name'].'</td>';
					}
					$html .= '<td>'.$team['total_time_taken'].'</td>';
					$html .= '<td>'.$team['clue_seconds'].'</td>';
					$html .= '<td>'.round($team['total_score']/$scoreMultipler, 2).'</td>';
					$html .= '<td>'.$team['cleared_levels'].'/'.$team['total_levels'].'</td>';
					$html .= '<td>'.implode(", ", $memberUserNames).'</td>';
					$html .= '<td>'.$buttons.'</td>';
					$html .= '</tr>';
				}
				$html .= "</table><br/>";
			}
			else if($isTeamGame) {
				$html .= "<p><i>No teams for this game</i></p>";
			}

			return "<div class='".$atts['css_classes']."'>".$html."</div>";
		}
	}

	public function create_team_cb( $atts ) {
		global $post;

		/**
		* #1: Getting shortcode attributes
		**/
		$attributes = shortcode_atts( array(
			'form_heading' => 'Create Team:',
			'css_classes' => '',
			'email_required' => "no",
		), $atts );

		$str  = '<div class="form-container '.$attributes['css_classes'].'">';
		$str .= '<h2>'.$attributes['form_heading'].'</h2>';

		/**
		* #2: Check if current game is team game or not
		**/
		$isTeamGame = get_post_meta( $post->ID, 'is_team_game', true );
		if ( $isTeamGame && $isTeamGame !== "false" ) {
			/**
			* #3: Showing the create team form
			**/
			$str .= '<form id="emailFrm" method="post">';
			$str .= '<p>
				<label for="playit_team_name">Team Name</label>
				<input type="text" id="playit_team_name" name="playit_team_name" placeholder="Your name..">
			</p>';
			if($attributes['email_required'] == "yes") {
				$str .= '<p>
					<label>Member Emails:</label>
					<input type="text" id="example_emailBS" name="playit_member_emails">
				</p>';
			}
			$str .= '<p>
				<input type="hidden" name="playit_current_page_id" value="'.$post->ID.'">
				<input type="submit" value="Submit">
			</p>';
			$str .= '</form>';
		}
		else {
			/**
			* #3: Showing the start playing button
			**/
			$nextLevelLink = $this->getGameNextLevelLink( $post->ID );
			if ( !$nextLevelLink ) {
				$nextLevelLink = $post->guid;
			}
			$str .= '<a href="'.add_query_arg('currentTeamId', 0, $nextLevelLink ).'" class="button button-black">Start Playing</a>';
		}
		$str .= '</div>';
		return $str;
	}

	public function init_actions() {
		global $post;

		// #1: Getting the current user info
		$playit_team_created_by = get_current_user_id();
		$currentUser = get_userdata($playit_team_created_by);

		// #2: Inserting/Updating the team and team members according to request data
		if( isset($_REQUEST['playit_team_name']) ) {
			$playit_team_name 		= $_REQUEST['playit_team_name'];
			$playit_current_page_id = $_REQUEST['playit_current_page_id'];

			// Checking if member emails is associated in team or not
			$emailsToInsert = array();
			$alreadyEmails 	= array();

			if ( isset($_REQUEST['playit_member_emails']) ) {
				$playit_member_emails 	= explode(",", $_REQUEST['playit_member_emails']);
				if ($playit_member_emails && count($playit_member_emails)) {
					foreach ($playit_member_emails as $i => $email) {
						$teamForThisEmail = $this->getUserTeamsInGameByEmail( $playit_current_page_id, $email );
						if (is_array($teamForThisEmail) && count($teamForThisEmail) > 0) {
							$alreadyEmails[] = $email;
						}
						else {
							$emailsToInsert[] = $email;
						}
					}
				}
			}

			// If error is present then show error message
			$manageTeamResponse = false;

			// Pushing current user email in array also
			if ( !empty($currentUser->data->user_email) ) {
				$emailsToInsert[] 	= $currentUser->data->user_email;
			}

			if (is_array($emailsToInsert) && count($emailsToInsert) > 0) {
				// Creating a team
				$manageTeamResponse = $this->manageTeam($playit_team_name, implode(",", array_unique($emailsToInsert)), $playit_current_page_id, $playit_team_created_by);
			}

			// If error is present then show error message
			if (is_array($alreadyEmails) && count($alreadyEmails) > 0) {
				$redirectWithQueryString = $_SERVER['HTTP_REFERER']."?error_msg=true&already_emails=".implode(",", $alreadyEmails);
				wp_redirect( $redirectWithQueryString );
				exit;
			}

			if ($manageTeamResponse) {
				wp_redirect( $_SERVER['HTTP_REFERER'] );
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

		#3: If current is page is game level then only perform db operations

		/**
		* Handling the event after answer has been posted via shortcode
		**/
		if ( 	isset($_POST['_current_answer']) &&
					isset($_POST['_current_team_id']) &&
					isset($_POST['_next_step_answer']) &&
					!empty($_POST['_current_game_id']) &&
					!empty($_POST['_current_level_id'])
				) {
	    if ( strtolower($_POST['_next_step_answer']) == strtolower($_POST['_current_answer']) ) {
	      // Inserting the time taken by the team in db also
	      $timeTaken = null;
	      if ( isset($_POST['_time_taken']) ) {
	        $timeTaken = $_POST['_time_taken'];
	      }

	      $gameLevelRes = $this->manageGameLevel(
					$_POST['_current_team_id'],
					$_POST['_current_game_id'],
					$playit_team_created_by,
					$_POST['_current_level_id'],
					$timeTaken,
					1
				);

	      if ($gameLevelRes) {
	        $nextLevel = add_query_arg( 'currentTeamId', $_POST['_current_team_id'], $this->getGameNextLevelLink($_POST['_current_level_id']) );
	    		if ( !$nextLevel ) {
	    			$nextLevel = $gameHomePage->guid;
	    		}
	        wp_redirect($nextLevel);
	        exit;
	      }
	    }
	    else {
				// if (!empty($_GET['currentTeamId'])) {
				// 	$nextLevel = add_query_arg( 'currentTeamId', $_GET['currentTeamId'], $this->getGameNextLevelLink($_POST['_current_level_id']) );
				// }
				// else {
				// 	$nextLevel = add_query_arg( 'currentTeamId', 0, $this->getGameNextLevelLink($_POST['_current_level_id']) );
				// }
				$nextLevel = add_query_arg( NULL, NULL );
				$urlWithErrMsg = add_query_arg( 'errmsg', "Answer Not Matched", $nextLevel );
				wp_redirect( $urlWithErrMsg );
				exit;
	    }
	  }

	}

	public function manageTeam($teamName, $memberEmails, $gameId, $createdBy) {
		global $table_prefix, $wpdb;
		$tblname = $table_prefix . 'gm_teams';

		// Checking the attribute value in db
		$row = $wpdb->get_row( "SELECT * FROM $tblname WHERE game_id = $gameId AND created_by=$createdBy", ARRAY_A );

		/*if ( is_array($row) && count($row) > 0 ) {
	    	$sql = "UPDATE $tblname SET team_name='$teamName', members_email='$memberEmails' WHERE game_id = $gameId AND created_by=$createdBy";
		}
		else {*/
			$sql = "INSERT INTO $tblname (team_name, members_email, game_id, created_by) VALUES ('$teamName', '$memberEmails', $gameId, $createdBy)";
		// }
		return $wpdb->query($sql);
	}

	/**
	* Returning the games associated with the current logged in user
	**/
	public function list_user_games_cb( $atts ) {
		// $attributes = shortcode_atts( array(
		// 	'productid' => 'current'
		// ), $atts );

		// if( !is_user_logged_in() ) {
		// 	return;
		// }
		// else {
		// 	$html = "";
		// 	$html .= "<p>Your Games</p>";
		// 	$currentUserId = get_current_user_id();
		// 	$userGames = $this->getUserTeamsById( $currentUserId );
		// 	if ( is_array($userGames) && count($userGames) > 0 ) {
		// 		$html .= '<table>
		// 		<tr>
		// 			<th width="30">S.No</th>
		// 			<th>Name</th>
		// 			<th>Associated Team</th>
		// 		</tr>';
		// 		foreach ($userGames as $i => $gameInfo) {
		// 			if (!empty($gameInfo['game_id'])) {
		// 				$pageData = get_post( $gameInfo['game_id'] );
		// 				$html .= '<tr>
		// 					<th>'.($i+1).'</th>
		// 					<th><a href="'.$pageData->guid.'">'.$pageData->post_title.'</a></th>
		// 					<th>'.$gameInfo['team_name'].'</th>
		// 				</tr>';

		// 				$html .= '<p></p>';
		// 			}
		// 		}
		// 		$html .= '</table>';
		// 	}

		// 	return $html;
		// }
	}

	/**
	* Formula for score
	*
	* maxscore = 5/(5+0)  levels per second
	* LevelsCleared*3600 - (totalTimeTaken + totalCluesTaken )
	*
	* score = (Number Of Levels/(Total Time Taken + Total Clues))*100
	**/
	public function getAllTeams( $gamesId ) {
		global $wpdb;
		$gamesTable = $wpdb->prefix . 'gm_games';
		$teamsTable = $wpdb->prefix . 'gm_teams';
		$postsTable = $wpdb->prefix . 'posts';
		$sql = "SELECT *,
		(SELECT SUM(time_taken) FROM $gamesTable WHERE game_id = t.game_id AND team_id = t.id AND is_cleared = 1) as total_time_taken,
		(SELECT SUM(clue_seconds) FROM $gamesTable WHERE game_id = t.game_id AND team_id = t.id AND is_cleared = 1) as clue_seconds,
		(
			((SELECT COUNT(ID) FROM $postsTable WHERE post_parent = t.game_id AND post_type = 'page' AND post_status = 'publish')/((SELECT SUM(time_taken) FROM $gamesTable WHERE game_id = t.game_id AND team_id = t.id AND is_cleared = 1) + (SELECT SUM(clue_seconds) FROM $gamesTable WHERE game_id = t.game_id AND team_id = t.id AND is_cleared = 1)))
		) as old_total_score,
		(
			(
				((SELECT COUNT(level_id) FROM $gamesTable WHERE game_id = t.game_id AND team_id = t.id AND is_cleared = 1)*3600) - ((SELECT SUM(time_taken) FROM $gamesTable WHERE game_id = t.game_id AND team_id = t.id AND is_cleared = 1) + (SELECT SUM(clue_seconds) FROM $gamesTable WHERE game_id = t.game_id AND team_id = t.id AND is_cleared = 1))
			)
		) as total_score,
		(SELECT COUNT(level_id) FROM $gamesTable WHERE game_id = t.game_id AND team_id = t.id AND is_cleared = 1) as cleared_levels,
		(SELECT COUNT(ID) FROM $postsTable WHERE post_parent = t.game_id AND post_type = 'page' AND post_status = 'publish') as total_levels,
		(SELECT COUNT(level_id) FROM $gamesTable WHERE game_id = t.game_id AND team_id = t.id) as total_levels_old
		FROM $teamsTable as t WHERE game_id = $gamesId ORDER BY cleared_levels DESC, total_time_taken ASC";
		// $sql = "SELECT * FROM $tblname WHERE game_id = $gamesId";
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
		$gamesId = array();
		$currentUser = get_userdata($userId);
		$userEmail = $currentUser->data->user_email;
		return $this->getUserTeamsByEmail($userEmail);
	}

	public function getUserTeamsByEmail( $userEmail ) {
		global $wpdb;
		$tblname = $wpdb->prefix . 'gm_teams';
		$sql = "SELECT * FROM $tblname WHERE members_email LIKE '%$userEmail%'";
		return $wpdb->get_results($sql, ARRAY_A);
	}

	public function getUserTeamsInGameByEmail( $gameId, $userEmail ) {
		global $wpdb;
		$tblname = $wpdb->prefix . 'gm_teams';
		$sql = "SELECT * FROM $tblname WHERE game_id=$gameId AND members_email LIKE '%$userEmail%'";
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

	public function getUserGameLevel( $userId, $teamId, $levelId ) {
		global $wpdb;
		$tblname = $wpdb->prefix . 'gm_games';
		$sql = "SELECT * FROM $tblname WHERE level_id=$levelId AND user_id=$userId AND team_id=$teamId";
		return $wpdb->get_row($sql, ARRAY_A);
	}

	public function getLevelInfo( $teamId, $levelId ) {
		global $wpdb;
		$tblname = $wpdb->prefix . 'gm_games';
		$sql = "SELECT * FROM $tblname WHERE level_id=$levelId AND team_id=$teamId";
		return $wpdb->get_row($sql, ARRAY_A);
	}

	public function manageGameLevel($team_id, $game_id, $user_id, $level_id, $time_taken=null, $is_cleared=0) {
		global $table_prefix, $wpdb;
		// $time_taken = time();
		$tblname = $table_prefix . 'gm_games';

		// Checking the attribute value in db
		// $row = $this->getUserGameLevel($user_id, $level_id);
		$row = $this->getLevelInfo( $team_id, $level_id );

		if ( is_array($row) && count($row) > 0 ) {
	    	$sql = "UPDATE $tblname SET team_id=$team_id, game_id=$game_id, level_id=$level_id, user_id=$user_id, time_taken='$time_taken', is_cleared=$is_cleared WHERE level_id=$level_id AND user_id=$user_id AND team_id=$team_id";
		}
		else {
			/**
			* Generating the query to insert game with all its levels
			**/
			$vals = "";
			$otherLevels = $this->getOtherLevels($game_id);
			if ( is_array($otherLevels) && count($otherLevels) > 0 ) {
				$counter = 0;
				foreach ($otherLevels as $lId => $otherLevel) {
					// Appending the commas
					if ( $counter !== 0 )
						$vals .= ",";

					if ( $lId == $level_id ) {
						$vals .= "($team_id, $game_id, $lId, $user_id, $is_cleared, '$time_taken')";
					} else {
						$vals .= "($team_id, $game_id, $lId, $user_id, 0, 0)";
					}
					$counter++;
				}
			}

			$sql = "INSERT INTO $tblname (team_id, game_id, level_id, user_id, is_cleared, time_taken) VALUES ".$vals;
		}

		return $wpdb->query($sql);
	}

	public function next_step_form_cb( $atts ) {
		global $post;

		$attributes = shortcode_atts( array(
			'answer' => '',
			'css_classes' => ''
		), $atts );

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
		* #3: If current game is team game or not and user doesn't belongs to any team
		* then redirect it to game page
		**/
		$isTeamGame = get_post_meta( $gameHomePage->ID, 'is_team_game', true );
		$currentTeamId = 0;
		if ( $isTeamGame && $isTeamGame !== "false" ) {
			if (!empty($_GET['currentTeamId'])) {
				$currentTeamId = $_GET['currentTeamId'];
				$teamInfo = $this->getTeamById( $currentTeamId );
				if ( empty($teamInfo) ) {
					return '<div class="teamnotexists '.$attributes['css_classes'].'">The team you selected doesn\'t exists <a href="'.$gameHomePage->guid.'">click here</a> to go to game page</div>';
				}
			}
			else {
				// wp_redirect($gameHomePage->guid);
				// exit;
				return '<div class="chooseteam '.$attributes['css_classes'].'">Please choose a team</div>';
			}
		}

		// // #3: Getting All Levels
		$currentUserLevelInfo = $this->getUserGameLevel($currentUserId, $currentTeamId, $post->ID);

		// $allLevels = $this->getOtherLevels( $post->ID );

		// // #4: Getting Next Level Link
		// $nextLevel = $this->get_next($allLevels, $post->ID);

		$nextLevel = add_query_arg( 'currentTeamId', $currentTeamId, $this->getGameNextLevelLink( $post->ID ));

		if ( !$nextLevel ) {
			$nextLevel = $gameHomePage->guid;
		}

		/**
		* #5: Process to save data in db after user answer a level, here we are also
		* checking if user is on page and so that it will not affect other pages.
		**/
		if ( 'page' === get_post_type() AND is_singular() ) {
			/**
			* #7: Checking if current user has already cleared the level or not
			**/
			$isCurrentLevelCleared = false;
			// $userLevelInfo = $this->getUserGameLevel($currentUserId, $post->ID);
			$userLevelInfo = $this->getLevelInfo( $currentTeamId, $post->ID );
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
			return '<div class="cleared '.$attributes['css_classes'].'">This level has been solved <a href="'.$nextLevel.'">click here</a> to go next level</div>';
		}
		else {

			if ( !empty($_GET['errmsg']) ) {
				$errorMessage = "<p style='color:red'>".$_GET['errmsg']."</p>";
			}

			return '<div class="'.$attributes['css_classes'].'">
				<form method="post" action="">
					<input type="hidden" value="0" name="_time_taken" />
					<input type="hidden" value="'.$post->ID.'" name="_current_level_id" />
					<input type="hidden" value="'.$post->post_parent.'" name="_current_game_id" />
					<input type="hidden" value="'.$currentTeamId.'" name="_current_team_id" />
					<input type="hidden" value="'.$attributes['answer'].'" name="_current_answer" />
					<input type="text" name="_next_step_answer" />
					<input type="submit" value="Submit" />
				</form>'.$errorMessage.'
			</div>';
		}
	}

	public function show_timer_cb( $atts ) {
		$attributes = shortcode_atts( array(
			'hours_label' => 'Hours',
			'minutes_label' => 'Minutes',
			'seconds_label' => 'Seconds',
			'hours_background_color' => '#cccccc9e',
			'minutes_background_color' => '#cccccc9e',
			'seconds_background_color' => '#cccccc9e',
			'hours_font_color' => '#fff',
			'minutes_font_color' => '#fff',
			'seconds_font_color' => '#fff',
			'css_classes' => '',
		), $atts );

		return '<div class="timer '.$attributes['css_classes'].'"><div class="timerwrapper">
				<div class="container">
					<div class="row">
						<div class="col-md-4 col-sm-4 col-xs-4">
							<div style="background-color:'.$attributes['hours_background_color'].';color:'.$attributes['hours_font_color'].'"
							 class="h"><h1>00</h1><p>'.$attributes['hours_label'].'</p></div>
						</div>
						<div class="col-md-4 col-sm-4 col-xs-4">
							<div style="background-color:'.$attributes['minutes_background_color'].';color:'.$attributes['minutes_font_color'].'"
							 class="m"><h1>00</h1><p>'.$attributes['minutes_label'].'</p></div>
						</div>
						<div class="col-md-4 col-sm-4 col-xs-4">
							<div style="background-color:'.$attributes['seconds_background_color'].';color:'.$attributes['seconds_font_color'].'"
							 class="s"><h1>00</h1><p>'.$attributes['seconds_label'].'</p></div>
						</div>
					</div>
				</div>
			</div>
		</div>';
	}

	/**
	* Checking if $pageId has parent or not if it has parent then getting its siblings else returning all child pages
	*
	* $id can be gameid or level id
	**/
	public function getOtherLevels( $pageId ) {
		$levelsWithUrl = array();
		$levelInfo = get_post( $pageId );

		// Implies that it is level
		if ( isset($levelInfo->post_parent) && $levelInfo->post_parent != 0 ) {
			$gameLevels = get_pages(array(
				'child_of' => $levelInfo->post_parent,
				'sort_column' => 'menu_order'
			));
		}
		else {
			$gameLevels = get_pages(array(
				'child_of' => $pageId,
				'sort_column' => 'menu_order'
			));
		}

		foreach ($gameLevels as $id => $level) {
			$levelsWithUrl[$level->ID] = get_the_permalink($level->ID);
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
			'meta_value' => 'true'
		));

		$html = '';
		if ( is_array($allPages) && count($allPages) > 0 ) {
			$html .= '<div class="container">';
			$html .= '<div class="row">';
			foreach ($allPages as $key => $page) {
				$thumbnailUrl = get_the_post_thumbnail_url($page->ID, 'post-thumbnail');
				if ( !$thumbnailUrl ) {
					$thumbnailUrl = get_option('default_game_images');
				}
				$html .= '<div class="col-md-4">
					<a href="'.$page->guid.'">
						<div><img src="'.$thumbnailUrl.'" /></div>
						<div>'.$page->post_title.'</div>
					</a>
				</div>';
			}
			$html .= '</div>';
			$html .= '</div>';
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

	public function init_template_redirect() {
		// global $post;

		// $currentUserLevelInfo = $this->getUserGameLevel( get_current_user_id(), $_GET['currentTeamId'], $post->ID);

		// if ( is_page() ) {
		// 	// $initialCookieValue = null;

		// 	// if ( is_null($currentUserLevelInfo) ) {
		// 	// 	// $initialCookieValue = htmlentities("00:00:00");
		// 	// 	setcookie("_timepassed", htmlentities("00:00:00"), strtotime( '+7 days' ));
		// 	// }
		// 	// else {
		// 	// 	// $initialCookieValue = htmlentities($currentUserLevelInfo['user_spent_time']);
		// 	// 	setcookie("_timepassed", htmlentities($currentUserLevelInfo['user_spent_time']), strtotime( '+7 days' ));
		// 	// }
		// }

	}

	public function show_clue_cb( $atts ) {
		$attributes = shortcode_atts( array(
			'seconds_to_add' => 0,
			'image_url' => null,
			'label' => "Clue",
			'text' => '',
			'css_classes' => '',
		), $atts );

		$secondsToAdd = $attributes['seconds_to_add'];
		if ($secondsToAdd && $secondsToAdd > 0) {
			$str = "<div class='cluewrapper ".$attributes['css_classes']."'>";
				$str .= "<a data-secondsToAdd='".$secondsToAdd."' href='javascript:void(0)' onclick='showclue(this)' class=''>".$attributes['label']."</a>";
				$str .= "<div class='clue' style='display:none'>";
					if (!empty($attributes['image_url'])) {
						$str .= "<div class='imagewrapper'>
							<img src='".$attributes['image_url']."'/>
						</div>";
					}
					if (!empty($attributes['text'])) {
						$str .= "<div class='textwrapper'>".$attributes['text']."</div>";
					}
				$str .= "</div>";
			$str .= "</div>";
			return $str;
		}
		else {
			return;
		}
	}

	public function join_game_by_code_cb( $atts ) {
		$attributes = shortcode_atts( array(
			'game_id' => null,
			'label' => "Add Code",
			'submit_button_label' => "Submit",
			'close_button_label' => "Close",
			'modal_title' => "Join Team By Code",
			'redirect_url' => site_url()
		), $atts );

		return '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#join_game_by_code_modal">'.$attributes['label'].'</button>
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
	}

}
