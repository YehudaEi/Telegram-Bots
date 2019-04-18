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
    
    function curl($method,$datas=[]==NULL){
        $url = "https://api.telegram.org/bot".TOKEN."/".$method;
        
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
            return json_decode($res,TRUE);
        }
    }
    function sendMessage($id, $mes, $rp = null, $pm = "", $rtmi = null){
        $data = array(
            'chat_id' => $id,
            'text' => $mes,
            'parse_mode' => $pm, 
            'reply_to_message_id' => $rtmi,
            'reply_markup' => $rp,
            'disable_web_page_preview' => true
        );
        return curl('sendMessage',$data);
    }
    
    $mes = $update["message"]["text"];
    $namef = $update["message"]["from"]["first_name"];
    $id = $update["message"]["chat"]["id"];
    if(isset($update['message']['photo'])){
        $tmp_p = $update['message']['photo'];
        $phid = $update['message']['photo'][count($tmp_p)-1]['file_id'];
    }
    if(isset($update['message']['reply_to_message'])){
        $tmp_p_r = $update['message']['reply_to_message']['photo'];
        $phid_r = $update['message']['reply_to_message']['photo'][count($tmp_p_r)-1]['file_id'];
    }
    
    if(isset($mes)){
        if($mes == "/start")
            sendMessage($id, "היי ".$namef." 👋🏼 \nהבוט הזה מדביק תמונה על תמונה.\nהוראות:\nשלח את התמונה שתרצה שעליה תודבק התמונה השניה\nשלח בהשב על התמונה הקודמת את התמונה שתרצה להדביק על התמונה הקודמת\n\nמבוסס על האתר: yehuda-bots.ga/kredit\n\nנוצר ע\"י @YehudaEisenberg");
        else
            sendMessage($id, "הוראות:\n1) שלח את התמונה שתרצה שעליה תודבק התמונה השניה.\n2)של בהשב על התמונה הראשונה את התמונה השניה.\n\nלעזרה: @YehudaEisenberg");
    }
    elseif(isset($phid) && !isset($phid_r)){
        sendMessage($id, "פצצה!\nשלח בהשב לתמונה הזאת את התמונה שתרצה שתודבק מעל.");
    }
    elseif(isset($phid) && isset($phid_r)){
        $tmp = curl('getFile',array('file_id' => $phid_r));
        $im_t = imagecreatefromjpeg("https://api.telegram.org/file/bot".TOKEN."/".$tmp['result']['file_path']);
        $tmp = curl('getFile',array('file_id' => $phid));
        $im = imagecreatefromjpeg("https://api.telegram.org/file/bot".TOKEN."/".$tmp['result']['file_path']);
        
        $sx = imagesx($im_t);
        $sy = imagesy($im_t);
        $ix = imagesx($im);
        $iy = imagesy($im);
        if($ix > $sx && $iy < $sy){
            $temp = $im_t;
            $im_t = $im;
            $im = $temp;
        }
        elseif(($ix > $sx && $iy < $sy) || ($ix < $sx && $iy > $sy)){
            sendMessage($id, "שגיאה!\nגדלי התמונות אינם תואמים!");
            die();
        }
        imagecopy($im, $im_t, 0, imagesy($im) - $sy, 0, 0, imagesx($im_t), imagesy($im_t));
        
        imagejpeg($im, $id."temp.jpg");
        $data = array(
            'chat_id' => $id,
            'photo' =>  new CURLFile(realpath($id."temp.jpg")),
            'caption' => "נוצר ע\"י @Credit_ILBOT",
        );
        curl("sendPhoto",$data);
        
        unlink($id."temp.jpg");
    }
}   
?>