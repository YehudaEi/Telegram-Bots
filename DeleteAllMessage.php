<?php
$update = file_get_contents('php://input');
$update = json_decode($update, true); 

if($update == NULL){
    http_response_code(403);
    include '403.html';
}
else{
    header('Content-Type: text/html; charset=utf-8');
    date_default_timezone_set('Asia/Jerusalem');
    
    define('TOKEN', '');
    define('BOT_ID', '');
    
    $message = $update["message"]["text"];
    $chatId = $update["message"]["chat"]["id"];
    $chatType = $update["message"]["chat"]["type"];
    $ncpId = $update["message"]["new_chat_participant"]["id"];
    $mesId = $update["message"]["message_id"];
    
    
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
    function sendMessage($id, $message, $rp = null){
        $PostData = array(
            'chat_id' => $id,
            'text' => $message,
            'parse_mode' => "Markdown", 
            'reply_markup' => $rp,
            'disable_web_page_preview' => true
        );
        return curlPost('sendMessage',$PostData);
    }
    
    if($chatType == "supergroup" && $ncpId == BOT_ID){
        sendMessage($chatId, "שלום לכולם 👋🏼 \nמעכשיו, כל הודעה שתשלח בקבוצה זו תמחק.");
        sendMessage($chatId, "*חובה להגדיר אותי כמנהל כדי שאני אוכל לעבוד*");
    }
    elseif($chatType == "supergroup")
    	curlPost('deleteMessage',array('chat_id' => $chatId, 'message_id' => $mesId));
    elseif($chatType == "private"){
    	sendMessage($chatId, "היי 👋🏼\nאני מוחק כל הודעה שנשלחת בקבוצה! כולל הכל!\n➕ להוספת הרובוט לקבוצה [לחץ כאן](http://t.me/DeleteAllMessage_ILBOT?startgroup=true). \n📣 לערוץ 'ממלכת הרובוטים הישראלית' [לחץ כאן](t.me/IL_BOTS). ");
        sendMessage($chatId, "*נ.ב.\nחובה להגדיר אותי כמנהל כדי שאני אוכל לעבוד*");
    }
}
?>
    