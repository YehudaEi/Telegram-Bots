<?php
date_default_timezone_set('Asia/Jerusalem'); 
header('Content-Type: text/html; charset=utf-8');

$update = file_get_contents('php://input');   
$update = json_decode($update, TRUE);

define('TOKEN', "");
define('BOT_ID', "");

function saveId($chatId) {
	$filename = "ids.txt";
	if(!file_exists($filename)) file_put_contents($filename," ");
	$handle = fopen($filename, "r");
	$allIds = fread($handle, filesize($filename));
	fclose($handle);
	if (strpos($allIds, "$chatId") === false)
	{
		$allIds .= " ".$chatId;
		$handle = fopen($filename, "w");
		fwrite($handle, $allIds);
		fclose($handle);
	}
}
function checkId($chatId) {
	$filename = "ids.txt";
	if(!file_exists($filename)) file_put_contents($filename," ");
	$handle = fopen($filename, "r");
	$allIds = fread($handle, filesize($filename));
	fclose($handle);
	if (strpos($allIds, "$chatId") === false)
		return true;
	else
		return false;
}
function isShabat(){
    if((date("l") == "Friday" && time() > date_sunset(time(), SUNFUNCS_RET_TIMESTAMP)))
        return true;
    if((date("l") == "Saturday" && time() < date_sunset(time(), SUNFUNCS_RET_TIMESTAMP)))
        return true;
    else
        return false;
}
function isArabic($message = NULL){
    if(mb_ereg('[\x{0600}-\x{06FF}]', $message))
        return true;
    else
        return false;
}

function curlPost($method,$datas=[]==NULL){    
    $urll = "https://api.telegram.org/bot".TOKEN."/".$method;
	
    $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $urll);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
   
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
		curl_close($ch);
    }else{
		curl_close($ch);
        return json_decode($res,true);
    }
}
function sendMessage($id, $message, $reply_markup = NULL, $parse_mode = "markdown"){
    $PostData = array(
        'chat_id' => $id,
        'text' => $message,
        'parse_mode' => $parse_mode, 
        'reply_markup' => $reply_markup
        );
	return curlPost('sendMessage',$PostData,$id);
}


$message = $update["message"]["text"]?$update["message"]["text"]:$update["edited_channel_post"]["text"];
$message = $message?$message:$update['message']['caption'];
$name = $update["message"]["from"]["first_name"].$update["message"]["from"]["last_name"];
$fwdname = $update["message"]["forward_from"]["first_name"].$update["message"]["forward_from"]["last_name"];
$chatId = $update["message"]["chat"]["id"]?$update["message"]["chat"]["id"]:$update["edited_channel_post"]["from"]["id"];
$mesId = $update['message']['message_id']?$update['message']['message_id']:$update['edited_channel_post']['message_id'];
$fromId = $update["message"]["from"]["id"]?$update["message"]["from"]["id"]:$update["edited_channel_post"]["from"]["id"];
$chatType = $update["message"]["chat"]["type"]?$update["message"]["chat"]["type"]:$update["edited_channel_post"]["chat"]["type"];
$ncpId = $update["message"]["new_chat_member"]["id"]?$update["message"]["new_chat_member"]["id"]:0;
$ncpName = $update["message"]["new_chat_member"]["first_name"]?$update["message"]["new_chat_member"]["first_name"].$update["message"]["new_chat_member"]["last_name"]:0;
$text = "היי👋,  
אני רובוט 'הרובוט היהודי'.  
תוכלו להוסיף אותי לקבוצתכם ולהעניק לי ניהול למחיקת הודעות, אני אמחק כל הודעה שתשלח בקבוצה במהלך השבת. 
בנוסף, אני מוחק הודעות בערבית. 
 
לדיווח על שגיאות @YehudaEisenberg. 
תודה ל @Avi_av על העזרה. 
 
➕ להוספת הרובוט לקבוצה [לחץ כאן](http://t.me/GardShabatBot?startgroup=true). 
📣 לערוץ העדכונים: @GardShabatBot.
";



if(checkId($chatId))
    saveId($chatId);
if($chatType == "supergroup" && isShabat())
	curlPost('deleteMessage', array('chat_id' => $chatId, 'message_id' => $mesId));
elseif($chatType == "supergroup" && (isArabic($message) || isArabic($name) || isArabic($fwdname))){
    curlPost('deleteMessage', array('chat_id' => $chatId, 'message_id' => $mesId));
    $res = curlPost('kickChatMember', array('chat_id' => $chatId, 'user_id' => $fromId));
    curlPost('deleteMessage', array('chat_id' => $chatId, 'message_id' => $res['result']['message_id']));
}
elseif($chatType == "supergroup" && ($fromId == 343637776 || $fromId == 360299327) && $message == "המשתמש הועף בהצלחה"){
    curlPost('deleteMessage', array('chat_id' => $chatId, 'message_id' => $mesId));
}
elseif($chatType == "private"){
	sendMessage($chatId, $text);
	sendMessage($chatId, "*נ.ב.\nחובה להגדיר אותי כמנהל כדי שאני אוכל לעבוד*");
}
if(isset($ncpId)){
    if($ncpId == BOT_ID){
        if(isArabic($update['message']['chat']['title'])){
            curlPost('leaveChat', array("chat_id" => $chatId));
        }
        sendMessage($chatId, "שלום לכולם 👋🏼 \nמעכשיו, כל הודעה שתשלח בקבוצה זו במהלך השבת תמחק.\nבנוסף, גם הודעות בערבית ימחקו");
        sendMessage($chatId, "*נ.ב.\nחובה להגדיר אותי כמנהל כדי שאני אוכל לעבוד*");
    }
    elseif(isArabic($ncpName)){
        $res = curlPost('kickChatMember', array('chat_id' => $chatId, 'user_id' => $ncpId));
        curlPost('deleteMessage', array('chat_id' => $chatId, 'message_id' => $res['result']['message_id']));
    }
}