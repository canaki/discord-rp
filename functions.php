<?php
function stripMarkdown($text){
	$Parsedown = new Parsedown();
//	$Parsedown->setMarkupEscaped(true);
	$Parsedown->setBreaksEnabled(true);
	return $Parsedown->text(htmlspecialchars($text));
}

function parseDiscordTime($str,$format){
	if (($timestamp = strtotime($str)) === false) {
		return $str;
	} else {
		return date($format, $timestamp);
	}
}

function parseMessage($message, $client_id, $format){
	unset($return);
	$avatar = 'https://cdn.discordapp.com/avatars/'.$message["author"]["id"].'/'.$message["author"]["avatar"].'.png';
	
	$return = '<dt class="author"><span class="avatar" style="background-image:url('.$avatar.');"></span><span class="username">'.$message["author"]["username"].'</span><span class="timestamp">';
	$return .= parseDiscordTime($message["timestamp"],$format);
	if($message["edited_timestamp"]){
		$return .= ' / Edited: ';
		$return .= parseDiscordTime($message["edited_timestamp"],$format);
	}
	$return .= '</span></dt>';
	$return .= '<dd data-mid="'.$message["id"].'" data-origtxt="'.htmlspecialchars($message["content"]).'">';
	$return .= stripMarkdown($message["content"]);
	$return .= '</dd>';
	if($message["attachments"]){
	foreach($message["attachments"] as $file){
		$return .= '<dd class="attachment">';
		if($file["width"]){
			$return .= '<a href="'.$file["url"].'"><img src="'.$file["url"].'" /></a>';
		}else{
			$ext = substr($file["url"], strrpos($file["url"], '.') + 1);
			if($ext == "mp3"){
				$return .= '<audio controls><source src="'.$file["url"].'" type="audio/mpeg" /></audio>';
			}elseif($ext == "wav"){
				$return .= '<audio controls><source src="'.$file["url"].'" type="audio/wav" /></audio>';
			}else{
				$return .= 'Attachment: <a href="'.$file["url"].'">'.$file["url"].'</a>';
			}
		}
		$return .= '</dd>';
	}
	}
	if($message["author"]["id"] == $client_id){
		$return .= '<dd class="meta"><button type="button" class="editor btn btn-dark  btn-sm">Edit</button> <button type="button" class="deleter btn btn-danger  btn-sm">Delete</button></dd>';
	}
	return $return;
}
function helpModal(){
	print<<<EOE
The all-purpose form works like this:
<ul>
	<li>Posting: text typed, no message id</li>
	<li>Editing: text typed, message id filled</li>
	<li>Deleting: no text, message id filled</li>
</ul>
There are [Edit] [Delete] buttons displayed on posts that are by you, which will do the following when pushed:
<ul>
	<li>Edit: copy the text and message id into the form</li>
	<li>Delete: copy message id into the form</li>
</ul>
Neither will immediately take action. To complete each action, you need to push the post button to confirm.
EOE;
}
?>