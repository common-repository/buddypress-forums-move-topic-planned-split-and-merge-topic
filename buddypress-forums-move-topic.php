<?php

function bp_fmt_update_database() {
	global $wpdb; global $bp; global $topic_template;
	$topic_mover = bp_core_get_userlink( bp_loggedin_user_id() );
	$new_forum_id = htmlentities($_POST['move-topic']);
	$new_forum_row = $wpdb->get_row($wpdb->prepare("SELECT forum_slug, forum_name FROM wp_bb_forums WHERE forum_id = %d LIMIT 1", $new_forum_id));
	$old_forum_row = $wpdb->get_row($wpdb->prepare("SELECT forum_slug, forum_name FROM wp_bb_forums WHERE forum_id = %d LIMIT 1", $topic_template->posts[0]->forum_id));
	$topic_row = $wpdb->get_row($wpdb->prepare("SELECT topic_slug, topic_title FROM wp_bb_topics WHERE topic_id = %d LIMIT 1", $topic_template->posts[0]->topic_id));
	//NEED TO replace topic slug in server_uri
	echo '<br><br>Hi, ' . $topic_mover . '! Got your request to move <strong>"' . $topic_row->topic_title . '"</strong> from forum ' . $old_forum_row->forum_name . ' to forum ' . $new_forum_row->forum_name . '.<br>';
	$server_uri = 'http://'; $server_uri .= $_SERVER['SERVER_NAME']; $server_uri .= $_SERVER['REQUEST_URI'];
	//A bit of hard-coding below. 
	//This MAY NOT WORK FOR YOU if you are using a nonstandard URL structure or secure https (a better coder can fix this):
	$new_uri = str_replace('/groups/' . $old_forum_row->forum_slug . '/', '/groups/' . $new_forum_row->forum_slug . '/', $server_uri);

	if ($old_forum_row->forum_name != $new_forum_row->forum_name) {
		//UPDATE wp_bb_topics, table name is forum_id, new value is $_GET['move-topic']
	$wpdb->update( 'wp_bb_topics', 
				array( 'forum_id' => $new_forum_id ), 
				array( 'topic_id' => $topic_template->posts[0]->topic_id ), 
				array( '%d' ), array( '%d' ) );
	//UPDATE ww_bb_posts, same as above.
	$wpdb->update( 'wp_bb_posts', 
				array( 'forum_id' => $new_forum_id ), 
				array( 'topic_id' => $topic_template->posts[0]->topic_id ), 
				array( '%d' ), array( '%d' ) );
	
	//UPDATE FORUM TOPIC COUNT IN wp_bb_forums, MINUS one for old forum, and SUBTRACT number of moved posts
	$old_forum_topic_and_post_count = $wpdb->get_row($wpdb->prepare("SELECT topics, posts FROM wp_bb_forums WHERE forum_id = %d LIMIT 1", $topic_template->posts[0]->forum_id));
	//echo '<br>got old forum count of topics: ' . $old_forum_topic_and_post_count->topics . ' and posts: ' . $old_forum_topic_and_post_count->posts;
	//echo '<br>number of posts in this thread: ' . 
	$post_count = count($topic_template->posts);	
	//echo '<br>updated post count for old forum (subtract): ' . 
	$old_forum_updated_post_count = $old_forum_topic_and_post_count->posts - $post_count; 
	(int)$old_topic_count = (int)$old_forum_topic_and_post_count->topics; 
	$old_topic_count--;
	//echo '<br>updated topic count for old forum (subtract): ' . $old_topic_count;
	
	$wpdb->update( 'wp_bb_forums', 
				array( 'topics' => $old_topic_count, 'posts' => $old_forum_updated_post_count ), 
				array( 'forum_id' => $topic_template->posts[0]->forum_id ), 
				array( '%d', '%d' ), array( '%d' ) );

	//UPDATE FORUM TOPIC COUNT IN wp_bb_forums, PLUS one for new forum, and ADD number of moved posts
	$new_forum_topic_and_post_count = $wpdb->get_row($wpdb->prepare("SELECT topics, posts FROM wp_bb_forums WHERE forum_id = %d LIMIT 1", $new_forum_id));
	//echo '<br>got new forum count: '; var_dump($new_forum_topic_and_post_count);
	//echo '<br>got new forum count of topics: ' . $new_forum_topic_and_post_count->topics . ' and posts: ' . $new_forum_topic_and_post_count->posts;
	(int)$new_topic_count = $new_forum_topic_and_post_count->topics;
	$new_topic_count++;
	//echo '<br>updated post count for new forum: ' . 
	$new_forum_updated_post_count = $new_forum_topic_and_post_count->posts + $post_count; 
		$wpdb->update( 'wp_bb_forums', 
				array( 'topics' => $new_topic_count, 'posts' => $new_forum_updated_post_count ), 
				array( 'forum_id' => $new_forum_id ), 
				array( '%d', '%d' ), array( '%d' ) );
	//UPDATE wp_bb_posts to change forum_id for EACH post in thread! add post to thread to see what happens
	foreach($topic_template->posts as $posts) { 
		$wpdb->update( 'wp_bb_posts', 
				array( 'forum_id' => $new_forum_id ), 
				array( 'post_id' => $posts->post_id ), 
				array( '%d' ), array( '%d' ) );
		//update $posts->forum_id to $new_forum;
		//echo 'forum id: ' . $posts->forum_id . '<br>';
		}

	//let's notify the user so they are not confused.
	echo 'So there\'s no confusion, we\'ll let the topic author know via email. Sending...<br>'; 
	$to = $topic_template->posts[0]->poster_email;
	$subject = '[' . get_option('blogname') .'] Your topic was moved';
	$message = 'Hi! Your topic "' . $topic_row->topic_title . '" was moved by ' . $topic_mover . ' to the Forum: ' . $new_forum_row->forum_name . ' and is now located here: ' . $new_uri . '. See you there!';
	//NEED TO IMPLEMENT: if forum post is being "caged" for bad behavior, show a different message.
	$admin_email = get_option('admin_email');
	$headers = 'From: ' . $admin_email . "\r\n";
	$headers .= "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
	mail ($to, $subject, $message, $headers);
	echo 'OK. Topic successfully moved to ' . $new_forum_row->forum_name . '. <a href="' . $new_uri . '">Click here to return to the topic.</a>';
	} else {
			echo 'hmm... the topic is already in ' . $old_forum_row->forum_name. '. Try again?<br>';
			echo '<a href="'. $server_uri .'">Return to the topic</a>';
		}
}
	
//function bp_fmt_display_dropdown() { //obsolete
//		if (bp_group_is_admin() || bp_group_is_mod()) { //don't display if user is not admin/mod
//			$my_forums_file = WP_PLUGIN_DIR; $my_forums_file .= '/buddypress-forums-move-topic/my-forums.php';
//			include($my_forums_file);
//			echo $drop_down;
//		}
//}

//setup forum list
function bp_fmt_setup_forum_list() {
		global $wpdb;
		$forums = $wpdb->get_results($wpdb->prepare("SELECT forum_id, forum_name, forum_order, forum_slug FROM wp_bb_forums WHERE forum_parent = 1"));
		$groups = $wpdb->get_results($wpdb->prepare("SELECT slug FROM wp_bp_groups WHERE enable_forum = 1"));
		foreach ($groups as $group) {
			$group_slug_array[] = $group->slug;
			}
		$my_forums_file = WP_PLUGIN_DIR; $my_forums_file .= '/buddypress-forums-move-topic-planned-split-and-merge-topic/my-forums.php';
		$fh = fopen($my_forums_file, 'w') or die("can't open file");
		$fwrite_string = '<form style="float:right;" action="" method="post">'; fwrite($fh, $fwrite_string);
		$fwrite_string = '<select name="move-topic" style="float:right;" onchange="this.form.submit();">'; fwrite($fh, $fwrite_string);
		$fwrite_string = '<option>Move topic to:</option>'; fwrite($fh, $fwrite_string);
		foreach ($forums as $forum) {
			if (in_array($forum->forum_slug, $group_slug_array)) {
				$fwrite_string = '<option value="'. $forum->forum_id .'">' . $forum->forum_name . '</option>'; fwrite($fh, $fwrite_string);
				}
			}
		$fwrite_string = '<option value="update">-Update Forum List-</option>'; fwrite($fh, $fwrite_string); 
		$fwrite_string = '</select></form>'; fwrite($fh, $fwrite_string);
		fclose($fh);
		$server_uri = 'http://'; $server_uri .= $_SERVER['SERVER_NAME']; $server_uri .= $_SERVER['REQUEST_URI'];
		$server_uni = '<br><a href="'. $server_uri .'">Click to continue.</a>';
}


//setup forum list COPY with debugging info.
function bp_fmt_setup_forum_list_COPY() {
		echo 'attempting to write the following drop-down code to file. If it fails, contact the plugin author, or simply create a file called <b>my-forums.php</b>, copy the text beow into that file, and upload it to /plugins/buddypress-forums-move-topic<br><br>';
		global $wpdb;
		$forums = $wpdb->get_results($wpdb->prepare("SELECT forum_id, forum_name, forum_order FROM wp_bb_forums WHERE forum_parent = 1"));
		$my_forums_file = WP_PLUGIN_DIR; $my_forums_file .= '/buddypress-forums-move-topic-planned-split-and-merge-topic/my-forums.php';
		$fh = fopen($my_forums_file, 'w') or die("can't open file");
		$fwrite_string = '<form style="float:right;" action="" method="post">'; fwrite($fh, $fwrite_string);
		echo htmlspecialchars($fwrite_string) .'<br>';
		$fwrite_string = '<select name="move-topic" style="float:right;" onchange="this.form.submit();">'; fwrite($fh, $fwrite_string);
		echo htmlspecialchars($fwrite_string) .'<br>';
		$fwrite_string = '<option>Move topic to:</option>'; fwrite($fh, $fwrite_string);
		echo htmlspecialchars($fwrite_string) .'<br>';
		foreach ($forums as $forum) {
			$fwrite_string = '<option value="'. $forum->forum_id .'">' . $forum->forum_name . '</option>'; fwrite($fh, $fwrite_string); 
			echo htmlspecialchars($fwrite_string) .'<br>';
			}
		$fwrite_string = '<option value="setup">Update Forum List</option>'; fwrite($fh, $fwrite_string); 
		$fwrite_string = '</select></form>'; fwrite($fh, $fwrite_string);
		echo htmlspecialchars($fwrite_string) .'<br>';
		fclose($fh);
		$server_uri = 'http://'; $server_uri .= $_SERVER['SERVER_NAME']; $server_uri .= $_SERVER['REQUEST_URI'];
		echo '<br><a href="'. $server_uri .'">Click to continue.</a>';
}

//If post is moved, avoid chaos and confusion. force the user to visit the new topic page ...
function bp_fmt_hide_page_content() {
	echo '<style type="text/css">';
	echo 'form#forum-topic-form { display:none }';
	//echo 'div#header, div#container { display:none }';
	echo '</style>';
	}
	
//this is the core function!
function bp_forum_move_topic() {
	if ( bp_has_forum_topic_posts() ) {
		global $bp; global $topic_template;

		//include drop-down only for admins and mods.
		//if no drop down exists, create it.
		if ( ( bp_group_is_admin() || bp_group_is_mod() ) && ( $_POST['move-topic'] == '' ) ) {
			include('my-forums.php');
			if ($drop_down == 'none') {
				bp_fmt_setup_forum_list();
			}
		}

		//react to form input... default to updating database if the POST value is set to a forum id number
		switch ($_POST['move-topic']) {
		    case 'update':
				bp_fmt_setup_forum_list();
				include ('my-forums.php');
	        	echo '<font color=\"red\">forum topic list manually updated! </font>';
	        	echo 'Any issues - please contact the <a href=\"http://buddypress.org/developers/3sixty/profile/\">plugin author.</a><br>';
				break;
			//case 'setup':
				//bp_fmt_setup_forum_list();
				//bp_fmt_display_dropdown();
				//echo "<font color=\"red\">forum topic list should be set up now!<br> Refresh the page to see the new Move Topic dropdown.</font><br> ";
				//$server_uri = 'http://'; $server_uri .= $_SERVER['SERVER_NAME']; $server_uri .= $_SERVER['REQUEST_URI'];
				//echo '<a href="'. $server_uri .'">Click here to return to the topic.</a>';
				//bp_fmt_hide_page_content();		
				//break;
		    case '':
		        //if ($drop_down != 'none') echo $drop_down;
		        break;   
		    default:
				bp_fmt_update_database();
				bp_fmt_hide_page_content();		
		        break;
				}
				
	} //end if (bp_has_forum_topic_posts() )
}

//this action puts the move topic drop down on every forum topic page (if you are an admin or mod).
add_action('bp_before_group_forum_content','bp_forum_move_topic');
//this action updates the topic list every time you start a new forum.
add_action('groups_new_group_forum','bp_fmt_setup_forum_list');
?>