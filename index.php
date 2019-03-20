<?php
/* =====================================
// POST/GET Vars
===================================== */
// get the guild
if( isset($_POST['gid']) ){
	$gid = $_POST['gid'];
	$gid = htmlspecialchars( $gid );
}elseif( isset($_GET['gid']) ){
	$gid = $_GET['gid'];
	$gid = htmlspecialchars( $gid );
}else{
	$gid = null;
}

// get the channel
if( isset($_POST['ch']) ){
	$ch = $_POST['ch'];
	$ch = htmlspecialchars( $ch );
	settype($ch, "integer");
}elseif( isset($_GET['ch']) ){
	$ch = $_GET['ch'];
	$ch = htmlspecialchars( $ch );
	settype($ch, "integer");
}else{
	$ch = null;
}

// Message for discord
if( isset($_POST['tx']) ){
	$tx = $_POST['tx'];
}else{
	$tx = null;
}
// Message id to be edited
if( isset($_POST['mid']) ){
	$mid = $_POST['mid'];
	settype($mid, "integer");
}else{
	$mid = null;
}

/* =====================================
// Loading and Initializing Vars
===================================== */
include __DIR__.'/vendor/autoload.php';
include "./functions.php";

$ini_array = parse_ini_file("discord.ini",false,INI_SCANNER_TYPED);

$bot_token = $ini_array['token'];
$timezone = $ini_array['timezone'];
date_default_timezone_set($timezone);
$dateformat = $ini_array['datetime'];

use RestCord\DiscordClient;
$discord = new DiscordClient(['token' => $bot_token]);
$client = $discord->user->getCurrentUser();
$username = $client->username;
$client_id = $client->id;
$client_avatar = $client->avatar;
if(isset($client_avatar)){
	$avatar_url = "https://cdn.discordapp.com/avatars/".$client_id."/".$client_avatar.".png";
}else{
	$avatar_url = "";
}

$guilds = $discord->user->getCurrentUserGuilds();
if(count($guilds) == 1){ // if there is only one guild, set it as The Guild
	$gid = $guilds[0]->id;
}

foreach($guilds as $guild){
	$guild_list[$guild->id] = array($guild->name,$guild->icon);
}
// if gid doesn't actually exist, unset it
if(!isset($guild_list[$gid])){unset($gid);}

foreach($guild_list as $gid => $array){
	$channels = $discord->guild->getGuildChannels(['guild.id' => $gid]);
	foreach($channels as $channel){
		if($channel->type  == 0 or $channel->type == 1 or $channel->type == 3){ // text channels, dms, group dms
			$channel_list[$channel->id] = array($gid,$channel->name,$channel->topic);
		}
	}
}

// if ch actually doesn't exist, unset it
if(!isset($channel_list[$ch])){unset($ch);}

// determine whether the guild and channel are set
if(isset($ch)){ // the channel is set, load messages
	$mode = "messages";
	$chid = $ch;
	$page_uri = "./?ch=".$chid;
}elseif(isset($gid)){ // the guild is set, load channel list
	$mode = "channels";
	$page_uri = "./?gid=".$gid;
}else{ // everything is not set, load guild list
	$mode = "guilds";
	$page_uri = "./";
}
/* =====================================
// Post to Discord
===================================== */
if($mid and $tx){ // both means editing
	$discord->channel->editMessage(['channel.id' => $chid, 'message.id' => $mid, 'content' => $tx]);
	header('Location: '.$page_uri);
	exit;
}elseif($tx){ // just txt means post
	$discord->channel->createMessage(['channel.id' => $chid, 'content' => $tx]);
	header('Location: '.$page_uri);
	exit;
}elseif($mid){ // just mid means delete
	$discord->channel->deleteMessage(['channel.id' => $chid, 'message.id' => $mid]);
	header('Location: '.$page_uri);
	exit;
}
/* =====================================
// Load messages
===================================== */
if($mode == "messages"){
	$messages = $discord->channel->getChannelMessages(['channel.id' => $chid]);
}

/* =====================================
// Print HTML
===================================== */
?><!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Content-Script-Type" content="text/javascript" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<meta name="googlebot" content="noindex,nofollow,noarchive" /> 
	<meta name="robots" content="noindex,nofollow,noarchive" /> 
	<meta name="viewport" content="width = device-width, initial-scale = 1" />
	<meta name="theme-color" content="#ccddff" />
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="./style.css" />
	<style><!--
	h2::before{background-image: url('<?php print $avatar_url;?>');}
	--></style>
	<title><?php print $username;?> discord client</title>
</head>
<body>
<div id="wrap"><div id="head">

<nav class="navbar navbar-expand-sm navbar-dark bg-dark fixed-top">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#chlist" aria-controls="chlist" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="chlist">
    <ul class="navbar-nav mr-auto">
    <li class="nav-item"><a class="nav-link" href="./">Index</a></li>
<li class="nav-item dropdown">
  <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Channels</a>
  <ul class="dropdown-menu bg-dark" aria-labelledby="navbarDropdown"><?php
foreach($channel_list as $key => $items){
	print '<li class="dropdown-item"><a href="./?ch='.$key.'">#'.$items[1].'</a></li>';
}
?>
  </ul>
</li>
  </ul>
  </div>
<button type="button" class="btn  btn-dark" data-toggle="modal" data-target="#Help">?</button>
</nav>
</div><div id="main"><?php
/* === Message view ================= */
if($mode == "messages"){ /* Posting form */ ?>
<h2>#<?php print $channel_list[$ch][1];
if(isset($channel_list[$ch][2])){ // print the topic if it exists
	print "<small> - ".$channel_list[$ch][2]."</small>";
}
?></h2>
<div class="text">
<form method="POST" action="<?php print $page_uri;?>">
<div>
<textarea id="tx" name="tx" autofocus="autofocus"></textarea>
<input type="number" id="mid" name="mid" placeholder="Message ID" />
</div>
<input type="hidden" name="ch" id="ch" value="<?php print $chid;?>" />
<input type="submit" value="Post to #<?php print $channel_list[$chid][1];?>">
</form>
</div>
<?php
} /* Posting form end*/
if($messages){ // Print Messages
	print '<dl class="loglist">';
	foreach($messages as $message){
		print parseMessage($message, $client_id, $dateformat);
	}
	print "</dl>";
}
/* === Channel view ================= */
if($mode == "channels"){ // Print Channel List
	print "<h2>Channels</h2><ul>";
	foreach($channel_list as $key => $items){
	print '<li><a href="./?ch='.$key.'">#'.$items[1].'</a></li>';
	}
	print "</ul>";
}
/* === Guild view ================= */
if($mode == "guilds"){
	print "<h2>Servers</h2><ul>";
	foreach($guild_list as $key => $items){
	print '<li><a href="./?gid='.$key.'">#'.$items[0].'</a></li>';
	}
	print "</ul>";
}
?>
</div>
</div>
<!-- Help Modal -->
<div class="modal fade" id="Help" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Usage</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
<?php helpModal(); ?>
      </div>
    </div>
  </div>
</div>
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	<script>
$(function() {
	$("button.editor").on("click", function() {
		$("textarea#tx").val($(this).parent().prev().data("origtxt"));
		$("input#mid").val($(this).parent().prev().data("mid"));
	});
	$("button.deleter").on("click", function() {
		$("textarea#tx").val("");
		$("input#mid").val($(this).parent().prev().data("mid"));
	});
});
	</script>
</body>
</html>