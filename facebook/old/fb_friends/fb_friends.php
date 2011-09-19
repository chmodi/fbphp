<?php
require 'src/facebook.php';
$facebook = new Facebook(array('appId'  => 'YOUR_APP_ID','secret' => 'YOUR_SECRET_KEY','cookie' => true,));
$session = $facebook->getSession();
$me = null;
if ($session) {
  try {
    $uid = $facebook->getUser();
    $me = $facebook->api('/me');
  } catch (FacebookApiException $e) {
    error_log($e);
  }
}

if ($me) {
  $logoutUrl = $facebook->getLogoutUrl();
} else {
	$loginUrl = $facebook->getLoginUrl();

}
?>
<html>
  <head>
    <title>PHP Drops :: Facebook Connect (Demo)</title>
    <style type="text/css">
      body {font-family: Trebuchet MS; font-size:12px }
      </style>
  </head>
  <body>
    <?php if ($me): ?>
       You have been successfully logged in. [<a href="<?php echo $logoutUrl; ?>">Logout</a>]
	   <h1>User Details</h1>
		<table>
			<tr>
				<td valign="top">
					<img src="https://graph.facebook.com/<?php echo $me['username']; ?>/picture">
				</td>
				<td valign="top">
					<b>Name:</b> <?php echo $me['first_name']." ".$me['last_name']; ?><br/>
					<b>Email:</b> <?php echo $me['email']; ?>
				</td>
			</tr>
		</table>
		<h1>Friends</h1>
		<div style="width:100%;height:auto">
		<ul style="padding:0px">
		<?php
		try{
            $fql    =   "select name,username,pic_square,uid from user where uid in (select uid1 from friend where uid2=" . $uid.")";
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
