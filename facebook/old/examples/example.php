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
$naitik = $facebook->api('/naitik');

?>
<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
    <title>Facebook-Xola Demo</title>
    <style>
      body {
        font-family: 'Lucida Grande', Verdana, Arial, sans-serif;
      }
      h1 a {
        text-decoration: none;
        color: #3b5998;
      }
      h1 a:hover {
        text-decoration: underline;
      }
    </style>
  </head>
  <body>
    <h1>php-sdk</h1>

    <?php if ($user): ?>
      <a href="<?php echo $logoutUrl; ?>">Logout</a>
    <?php else: ?>
      <div>
        Login using OAuth 2.0 handled by the PHP SDK:
        <a href="<?php echo $loginUrl; ?>">Login with Facebook</a>
      </div>
    <?php endif ?>

    <h3>PHP Session</h3>
    <pre><?php print_r($_SESSION); ?></pre>

    <?php if ($user): ?>
      <h3>You</h3>
      <img src="https://graph.facebook.com/<?php echo $user; ?>/picture">

      <h3>Your User Object (/me)</h3>
      <pre><?php print_r($user_profile); ?></pre>
    <?php else: ?>
      <strong><em>You are not Connected.</em></strong>
    <?php endif ?>

    <h3>Public profile of Naitik</h3>
    <img src="https://graph.facebook.com/naitik/picture">
    <?php echo $naitik['name']; ?>
	
	<?php if ($user_profile): ?>
       You have been successfully logged in. [<a href="<?php echo $logoutUrl; ?>">Logout</a>]
	   <h1>User Details</h1>
		<table>
			<tr>
				<td valign="top">
					<img src="https://graph.facebook.com/<?php echo $user_profile['username']; ?>/picture">
				</td>
				<td valign="top">
					<b>Name:</b> <?php echo $user_profile['first_name']." ".$user_profile['last_name']; ?><br/>
					<b>Email:</b> <?php echo $user_profileme['email']; ?>
				</td>
			</tr>
		</table>
		<h1>Friends</h1>
		<div style="width:100%;height:auto">
		<ul style="padding:0px">
		<?php
		try{
            $fql    =   "select name,username,pic_square,uid from user where uid in (select uid1 from friend where uid2=" . $user.")";
            $param  =   array(
                'method'    => 'fql.query',
                'query'     => $fql,
                'callback'  => ''
            );
            $friend_list   =   $facebook->api($param);
        }
        catch(Exception $o){
            d($o);
        }
		$i=1;
		foreach($friend_list as $rec){
			?>
			<li style="float:left;list_style:none;margin:5px">
				<a href="https://www.facebook.com/profile.php?id=<?php echo $rec['uid']; ?>" target="_blank">
					<img src="<?php echo $rec['pic_square']; ?>" title="<?php echo $rec['name']; ?>">
				</a>
			</li>
			<?php
			$i++;
		}
		?>
		</ul><br style="clear:both" />
	<?php else: ?>
    <div>
      You are currently not logged in. Click <a href="<?php echo $loginUrl; ?>">here</a> to login using facebook connect.
    </div>
    <?php endif; ?>
  </body>
</html>
