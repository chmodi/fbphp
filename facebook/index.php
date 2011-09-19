<?php //set_include_path($_SERVER['DOCUMENT_ROOT']);
require 'src/facebook.php';

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
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	
	<title>Xola facebook Demo</title>

	<link rel="stylesheet" href="tabs.css" type="text/css" media="screen, projection"/>

	<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.7.custom.min.js"></script>
    <script type="text/javascript">
		$(function() {
			
			var $tabs = $('#tabs').tabs();
	
			$(".ui-tabs-panel").each(function(i){
	
			  var totalSize = $(".ui-tabs-panel").size() - 1;
				
			  if (i != totalSize) {
			      next = i + 2;
				  //alert("<a href='#' class='next-tab mover' rel='" + next + "'>Next Page &#187;</a>");
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
	<style type="text/css">

#left {
  position: absolute;
  left: 5px;
  padding: 0px;
  width: 150px;
}

#right{

  margin-left: 200px;
  padding: 0px;
  margin-right: 15px;
}

#inner {
    width: 50%;
    margin: auto;
	text-align: center;
	align: center
}
</style>

</head>

<body>

	<div id="page-wrap">
		
		<div id="tabs">
		
	<?php if (isset($user_profile)): ?>
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
		$friendList = $_SESSION['friend_list'];

		?>	
		<ul>
		<?php		
		for($i = 1; $i < count($friendList); $i++){
		?>		
        	<li><a href="#fragment-<?php echo $i; ?>"></a></li>
		<?php } ?>		
		</ul>
		<?php		
		for($i = 1; $i < count($friendList); $i++){
		?>
		<?php if($i==1) { ?>	
		<div id="fragment-1" class="ui-tabs-panel">
			<div id="inner">
				<a href="https://www.facebook.com/profile.php?id=<?php echo $friendList[$i]['uid']; ?>" target="_blank">
					<img src="<?php echo $friendList[$i]['pic_square']; ?>" title="<?php echo $friendList[$i]['name']; ?>">
				</a>		
				<?php echo $friendList[$i]['name'];?>  
			</div>		
        </div>
		<?php }else { ?>		
        <div id="fragment-<?php echo $i; ?>" class="ui-tabs-panel ui-tabs-hide">
        	    <div id="inner">
				<a href="https://www.facebook.com/profile.php?id=<?php echo $friendList[$i]['uid']; ?>" target="_blank">
					<img src="<?php echo $friendList[$i]['pic_square']; ?>" title="<?php echo $friendList[$i]['name']; ?>">
				</a>		
				<?php echo $friendList[$i]['name'];?>  
			</div>	    
        </div>	
		<?php } ?>
        <?php }
		} ?>	      	     	
        </div>
		
	</div>
	<?php else: ?>
    <div>
        <a href="<?php echo $loginUrl; ?>">Login with Facebook</a>
      </div>
    <?php endif; ?>
</body>

</html>
