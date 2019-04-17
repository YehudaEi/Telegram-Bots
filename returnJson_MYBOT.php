<?php
header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set('Asia/Jerusalem');

$update = file_get_contents('php://input');   
$update = json_decode($update, TRUE);

if($update == NULL){
    http_response_code(403);
    include '403.html';
    die();
}

define('TOKEN', '');

$message = $update["message"]["text"]?$update["message"]["text"]:$update["edited_message"]["text"];
$chatId = $update["message"]["chat"]["id"]?$update["message"]["chat"]["id"]:$update["callback_query"]["from"]["id"];
$chatId = $chatId?$chatId:$update["inline_query"]["from"]["id"];
$chatId = $chatId?$chatId:$update["edited_message"]["from"]["id"];
$messageId = $update['message']['message_id']?$update['message']['message_id']:$update['edited_message']['message_id'];


$keyboard = json_encode(array(
  'inline_keyboard' => array(array(array('text' => 'callback_data is your id', 'callback_data' => $chatId))),
  'one_time_keyboard' => true,'resize_keyboard' => true
));

function curlPost($method,$datas=[]==NULL){
    $url = "https://api.telegram.org/bot".TOKEN."/".$method;
	
    $ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
   
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
		curl_close($ch);
    }else{
		curl_close($ch);
        return json_decode($res,TRUE);
      
    }
}
function sendMessage($id, $message, $rp, $rmi){
	$PostData = array(
		'chat_id' => $id,
		'text' => $message,
		'parse_mode' => "Markdown", 
		'reply_markup' => $rp,
		'disable_web_page_preview' => true,
		'reply_to_message_id' => $rmi
	);
	return curlPost('sendMessage',$PostData);
}
if($message == "callback"){
    sendMessage($chatId, "callback", $keyboard, $messageId);
}
elseif($message == "/start"){
    sendMessage($chatId,"hello,\n Send me the text \"`callback`\" to get a message with a button.\n\nCreated by @YehudaEisenberg.", NULL, $messageId);
}
else
	sendMessage($chatId,"```{\n".json_encode(json_decode(file_get_contents('php://input'), TRUE), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)."```", NULL,$messageId);
?>