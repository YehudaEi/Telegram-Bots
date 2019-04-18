<?php
header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set('Asia/Jerusalem');

$update = file_get_contents('php://input');   
$update = json_decode($update, TRUE);

define('TOKEN', '');

$message = $update["message"]["text"];
$chatId = $update["message"]["chat"]["id"];
$FirstName = $update["message"]["from"]["first_name"];
$LastName = $update["message"]["from"]["last_name"];
$callData = $update["callback_query"]["data"];
$callId = $update["callback_query"]["id"];
$reply_markup_defult = json_encode(array(
    'inline_keyboard' => array(array(array('text' => 'אודות','callback_data' => "אודות"))),
    'one_time_keyboard' => true,
    'resize_keyboard' => true
));

function curlPost($method,$datas=[]){
    $url = "https://api.telegram.org/bot".TOKEN."/".$method;
	
    $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
   
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
		curl_close($ch);
    }
    else{
		curl_close($ch);
        return json_decode($res,TRUE);
    }
}
function sendMessage($id, $message, $reply_markup = NULL, $a ="html"){
    $PostData = array(
        'chat_id' => $id,
        'text' => $message,
        'parse_mode' => $a, 
        'reply_markup' => $reply_markup,
        'disable_web_page_preview' => true
    );
    return curlPost('sendMessage',$PostData);
}
function rashitevot($str, $reply_markup_defult, $chatId){
    for($i = 0; $i < strlen($str);$i++)
        if($str[$i] == "\"")
        {
             sendMessage($chatId, "שגיאה! הטקסט המבוקש מכיל ראשי תבות" , $reply_markup_defult);
             return true;
        }
}


if($callData == "אודות"){
    $PostData = array(
        "callback_query_id" => $callId, 
        "text" => "רובוט מורפיקס!
    גרסא 0.0.1
    נוצר על ידי יהודה אייזנברג.",
        "show_alert" => true
    );
    curlPost("answerCallbackQuery", $PostData);
}
if($message == "/start")
    sendMessage($chatId, "ברוך הבא ".$FirstName." ".$LastName." !", $reply_markup_defult, null);
elseif(strlen($message) > 120)
     sendMessage($chatId, "ניתן להקליד עד 120 תוים", $reply_markup_defult);
elseif(rashitevot($message, $reply_markup_defult, $chatId))
    ;
else
    sendMessage($chatId, "<a href=\"http://www.morfix.co.il/".$message."\">".$message."</a>" ,$reply_markup_defult);
?>