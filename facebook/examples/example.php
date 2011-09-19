<?php
/**
 * Copyright 2011 Facebook, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

require '../src/facebook.php';

// Create our Application instance (replace this with your appId and secret).
$facebook = new Facebook(array(
  'appId'  => '226699594032662',
  'secret' => '8019e8798747b6fa66da39286fb7fc2d',
));

// Get User ID
$user = $facebook->getUser();

// We may or may not have this data based on whether the user is logged in.
//
// If we have a $user id here, it means we know the user is logged into
// Facebook, but we don't know if the access token is valid. An access
// token is invalid if the user logged out of Facebook.

if ($user) {
  try {
    // Proceed knowing you have a logged in user who's authenticated.
    $user_profile = $facebook->api('/me');
  } catch (FacebookApiException $e) {
    error_log($e);
    $user = null;
  }
}

// Login or logout url will be needed depending on current user state.
if ($user) {
  $logoutUrl = $facebook->getLogoutUrl();
} else {
  $loginUrl = $facebook->getLoginUrl();
}

// This call will always work since we are fetching public data.
//$naitik = $facebook->api('/naitik');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!--html xmlns:fb="http://www.facebook.com/2008/fbml"-->
  <head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" href="tabs.css" type="text/css" media="screen, projection"/>
    <title>Facebook-Xola Demo</title>    
	<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.7.custom.min.js"></script>
    <script type="text/javascript">
		$(function() {

			var $tabs = $('#tabs').tabs();
	
			$(".ui-tabs-panel").each(function(i){
	
			  var totalSize = $(".ui-tabs-panel").size() - 1;
	
			  if (i != totalSize) {
			      next = i + 2;
		   		  $(this).append("<a href='#' class='next-tab mover' rel='" + next + "'>Next Page &#187;</a>");
			  }
	  
			  if (i != 0) {
			      prev = i;
		   		  $(this).append("<a href='#' class='prev-tab mover' rel='" + prev + "'>&#171; Prev Page</a>");
			  }
   		
			});
	
			$('.next-tab, .prev-tab').click(function() { 
		           $tabs.tabs('select', $(this).attr("rel"));
		           return false;
		       });
       

		});
    </script>

  </head>
  <body>
    <h1>Xola Facebook Demo</h1>

    <!--h3>PHP Session</h3-->
    <pre><?php //print_r($_SESSION); ?></pre>

    <?php if ($user): ?>
      <h3>You</h3>
      <img src="https://graph.facebook.com/<?php echo $user; ?>/picture">

      <!--h3>Your User Object (/me)</h3-->
      <pre><?php //print_r($user_profile); ?></pre>
    <?php else: ?>
      <strong><em>You are not Connected.</em></strong>
    <?php endif ?>

	
	<?php if ($user_profile): ?>
       You have been successfully logged in. [<a href="<?php echo $logoutUrl; ?>">Logout</a>]
	   <h1>Friends</h1>
		

		<?php
		try{
			if (!isset($_SESSION['friend_list']))
			{
				$fql    =   "select name,username,pic_square,uid from user where uid in (select uid1 from friend where uid2=" . $user.")";
				$param  =   array(
					'method'    => 'fql.query',
					'query'     => $fql,
					'callback'  => ''
				);
				$friend_list   =   $facebook->api($param);
				$_SESSION['friend_list'] = $friend_list;
			}	
        }
        catch(Exception $o){
            d($o);
        }
		$i=1;		
		if(isset($_SESSION['friend_list'])) {
		foreach($_SESSION['friend_list'] as $rec){
			?>
			<!--li style="float:left;list_style:none;margin:5px">
				<a href="https://www.facebook.com/profile.php?id=<?php //echo $rec['uid']; ?>" target="_blank">
					<img src="<?php //echo $rec['pic_square']; ?>" title="<?php //echo $rec['name']; ?>">
				</a>
			</li-->
			<?php
			$i++;
		}}
		?>
		<!--/ul><br style="clear:both" /-->
		</div>
	<?php else: ?>
    <div>
        <a href="<?php echo $loginUrl; ?>">Login with Facebook</a>
      </div>
    <?php endif; ?>
  </body>
</html>