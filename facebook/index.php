<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />	
	<title>Facebook friend widget</title>
	<link rel="stylesheet" href="tabs.css" type="text/css" media="screen, projection"/>
	<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.7.custom.min.js"></script>
    <script type="text/javascript">
		$(function() {
			
			var $tabs = $('#tabs').tabs();
	
			$(".ui-tabs-panel").each(function(i){
				
			  var totalSize = $(".ui-tabs-panel").size();
				
			  if (i != totalSize) {
			      next = i + 1;				  
		   		  $(this).append("<a href='#' class='next-tab mover' rel='" + next + "'>Next &#187;</a>");
			  }
	  
			  if (i != 0) {
			      prev = i-1;
		   		  $(this).append("<a href='#' class='prev-tab mover' rel='" + prev + "'>&#171; Prev</a>");
			  }
   		
			});
	
			$('.next-tab, .prev-tab').click(function() { 
		           $tabs.tabs('select', $(this).attr("rel"));
		           return false;
		       });
       

		});
    </script>
</head>
<?php 
//Include facebook PHP SDK
require 'src/facebook.php';

// Create our Application instance 
$facebook = new Facebook(array(
  'appId'  => '3226699594032662',
  'secret' => '48019e8798747b6fa66da39286fb7fc2d',
  'cookie' => true
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
  $logoutUrl = $facebook->getLogoutUrl(array('next' => 'http://localhost/facebook/logout.php'));
} else {  
  $params['scope'] = 'friends_hometown,friends_location';	
  $loginUrl = $facebook->getLoginUrl($params);
  
}
?>
<body>
	<div id="page-wrap">
		
		<div id="tabs">
		
	<?php if (isset($user_profile)): ?>
       <p>[<a href="<?php echo $logoutUrl; ?>">Logout</a>]</p>
	   <?php
		try{
			if (!isset($_SESSION['friend_list']))
			{
				$fql    =   "select name,username,pic_square,uid,hometown_location,current_location from user where uid in (select uid1 from friend where uid2=me())";// . $user.")";
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
            echo $o->getMessage();
        }
		
		if(isset($_SESSION['friend_list'])) {
		$friendList = $_SESSION['friend_list'];		
		?>	
		<ul>
		<?php		
		for($i = 0; $i < count($friendList); $i++){
		?>		
        	<li><a href="#fragment-<?php echo $i; ?>"></a></li>
		<?php } ?>		
		</ul>
		<?php		
		for($i = 0; $i < count($friendList); $i++){
		$j = $i+1;			
		?>		
        <div id="fragment-<?php echo $i; ?>" <?php if($i == 0) {echo "class=\"ui-tabs-panel\"";} else { echo "class=\"ui-tabs-panel ui-tabs-hide\"";}?>>
        	 <div id="inner">
				<a href="https://www.facebook.com/profile.php?id=<?php echo $friendList[$i]['uid']; ?>" target="_blank">
					<img src="<?php echo $friendList[$i]['pic_square']; ?>" title="<?php echo $friendList[$i]['name']; ?>">
				</a>				
			</div>	    
			<div id="bottom">
			<p><?php echo $friendList[$i]['name'];
					if (isset($friendList[$i]['hometown_location']['city']))
					  echo '<br>Hometown:'.$friendList[$i]['hometown_location']['city'];
					if (isset($friendList[$i]['current_location']['city']))  
					  echo '<br>Current city:'.$friendList[$i]['current_location']['city'];?>
				</p>
			<div id="bottomcenter">
				<?php echo $j.'/'.count($friendList);?>
			</div>
			</div>
        </div>	
		<?php } ?>
        <?php //}
		} ?>	      	     	
        </div>
		
	</div>
	<?php else: ?>
    <div>
        <a class="fb_button fb_button_medium" href="<?php echo $loginUrl; ?>"><span class="fb_button_text">Log In</span></a>
    </div>
    <?php endif; ?>
</body>
</html>