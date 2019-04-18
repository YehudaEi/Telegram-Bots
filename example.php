<?php
header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set('Asia/Jerusalem');

$update = file_get_contents('php://input');   
$update = json_decode($update, TRUE);

$message = $update["message"]["text"];
$id = $update["message"]["chat"]["id"];
$FirstName = $update["message"]["from"]["first_name"];
$LastName = $update["message"]["from"]["last_name"];
$inlineQ = $update["inline_query"]["query"];
$InlineQId = $update["inline_query"]["id"];
$InlineMsId = $update["callback_query"]["inline_message_id"];
$callId = $update["callback_query"]["id"];
$callData = $update["callback_query"]["data"];
$callFromId = $update["callback_query"]["from"]["id"];
$callMessageId = $update["callback_query"]["message"]["message_id"];

$rp1 = json_encode(array(
  'inline_keyboard' => array(array(array('text' => 'מעבר לתפריט 2', 'callback_data' => "menu2")),array(array('text' => 'מעבר לתמיכה', 'url' => "t.me/YehudaEisenberg"))),
  'one_time_keyboard' => true,'resize_keyboard' => true
));
$rp2 = json_encode(array(
  'inline_keyboard' => array(array(array('text' => 'מעבר לתפריט 1', 'callback_data' => "תפריט1")),array(array('text' => 'מעבר לתמיכה', 'url' => "t.me/YehudaEisenberg"))),
  'one_time_keyboard' => true,'resize_keyboard' => true
));

function curlPost($method,$datas=[]==NULL){
    $token = "<YOUR_TOKEN>";
    $url = "https://api.telegram.org/bot".$token."/".$method;
	
    $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
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
function editMessageText($id, $messageId, $text, $reply_markup = null, $parse_mode = "Markdown"){
    $PostData = array(
        'chat_id' => $id,
        'message_id' => $messageId,
        'text' => $text,
        'parse_mode' => $parse_mode,
        'disable_web_page_preview' => true,
        'reply_markup' => $reply_markup
    );
    return curlPost('editMessageText',$PostData,$id);
}
function answerInline($id, $data=[]){
	$PostData = array(
		'inline_query_id' => $id,
		'cache_time' => 30,
		'results' => $data
	);
	return curlPost('answerInlineQuery',$PostData);
}
function answerCallback($callId, $text, $alert = true){
    $PostData = array(
        "callback_query_id" => $callId, 
        "text" => $text,
    	"show_alert" => $alert
    );
    return curlPost("answerCallbackQuery", $PostData);
}

if(isset($message)){
    sendMessage($id,"הודעה וכו' וכו' וכו'",$rp1);
}
elseif(isset($callData)){
    if($callData == "תפריט1"){
        editMessageText($callFromId, $callMessageId, "הודעה ראשונה...", $rp1);
        answerCallback($callId, "היי, קפץ לך חלון ;)", true);
    }
    elseif($callData == "menu2"){
        editMessageText($callFromId, $callMessageId, "הודעה שניה...", $rp2);
        answerCallback($callId, "התראה שקטה..", false); 
    }
}
elseif(isset($inlineQ)){
    $markup = array('inline_keyboard' => array(array(array('text' => 'Link', 'url' => "t.me/YehudaEisenberg"))));
    $mResult = array(array(
		"type" => "article",
        "id" => "1",//מיקום ברשימה
        "title" => "כותרת",
		"description" => "הסבר",
        "message_text" => "תוכן ההודעה",
        "reply_markup" => $markup,
        "parse_mode" => "Markdown",
	));
    answerInline($InlineQId,json_encode($mResult));
}
?>