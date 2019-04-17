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
$inlineQ = $update["inline_query"]["query"];
$InlineQId = $update["inline_query"]["id"];
$InlineMsId = $update["callback_query"]["inline_message_id"];
$callData = $update["callback_query"]["data"];
$callFromId = $update["callback_query"]["from"]["id"];
$callMessageId = $update["callback_query"]["message"]["message_id"];
$reply_markup = array('inline_keyboard' =>  array(array(array('text' => 'חיפוש🔍', 'switch_inline_query' => ""))));
$rm_refresh = array('inline_keyboard' => array(array(array('text' => '🔄 רענן 🔄', 'callback_data' => "1"))));

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
    }else{
		curl_close($ch);
        return json_decode($res,TRUE);
    }
}
function editMessageText($chatId, $messageId, $text, $reply_markup = null, $parse_mode = "Markdown"){
	if($chatId)
		$PostData = array(
			'chat_id' => $chatId,
			'message_id' => $messageId,
			'text' => $text,
			'parse_mode' => $parse_mode,
			'disable_web_page_preview' => false,
			'reply_markup' => $reply_markup
		);
	elseif(!$chatId)
		$PostData = array(
			'inline_message_id' => $messageId,
			'text' => $text,
			'parse_mode' => $parse_mode,
			'disable_web_page_preview' => false,
			'reply_markup' => $reply_markup
		);
	return curlPost('editMessageText',$PostData);
}
function sendMessage($id, $message, $reply_markup = NULL){
	$PostData = array(
		'chat_id' => $id,
		'text' => $message,
		'parse_mode' => "Markdown", 
		'reply_markup' => $reply_markup,
		'disable_web_page_preview' => false
	);
	return curlPost('sendMessage',$PostData);
}
function answerInline($id, $data=[]){
	$PostData = array(
		'inline_query_id' => $id,
		'switch_pm_text' => "מעבר לבוט",
		'switch_pm_parameter' => "a",
		'cache_time' => 2,
		'results' => $data
	);
	return curlPost('answerInlineQuery',$PostData);
}
function f_count($bool = false){
    $filename = "count_search.txt";
    $handle = fopen($filename, "r");
    $size = fread($handle, filesize($filename));
    fclose($handle);
    if($bool)
    {
		$size ++;
		$f = fopen('count_search.txt', 'w');
		fwrite($f,$size);
		fclose($f); 
    }
    return $size;
}

{
$google = array(
        "type" => "article",
        "id" => "001",
        "title" => "Google - גוגל",
		"description" => "תוצאות חיפוש על: ".$inlineQ,
        "message_text" => "Google:
		[".$inlineQ."](https://www.google.co.il/search?q=".$inlineQ.")",
        "reply_markup" => $reply_markup,
        "thumb_url" => 'https://t.me/i/userpic/320/Search_ILBOT.jpg',  
        "parse_mode" => "Markdown",
    );
/*$google_a = array(
        "type" => "article",
        "id" => "002",
        "title" => "Google - גוגל - הסבר איך לחפש",
		"description" => "תוצאות חיפוש על: ".$inlineQ,
        "message_text" => "אתה ממש עצלן אם הגעת לפה ;)
		[".$inlineQ."](http://he.lmgtfy.com/?q=".$inlineQ.")",
        "reply_markup" => $reply_markup,
        "thumb_url" => 'https://t.me/i/userpic/320/Search_ILBOT.jpg',  
        "parse_mode" => "Markdown",
    );*/
$duckduckgo = array(
        "type" => "article",
        "id" => "003",
        "title" => "DuckDuckGo - חיפוש בסתר ",
		"description" => "תוצאות חיפוש על: ".$inlineQ,
        "message_text" => "מה כזה חסוי ?!
		[".$inlineQ."](https://duckduckgo.com/?q=".$inlineQ.")",
        "reply_markup" => $reply_markup,
        "thumb_url" => 'https://t.me/i/userpic/320/Search_ILBOT.jpg',  
        "parse_mode" => "Markdown",
    );
/*$duckduckgo_a = array(
        "type" => "article",
        "id" => "004",
        "title" => "DuckDuckGo - חיפוש בסתר - הסבר איך לחפש",
		"description" => "תוצאות חיפוש על: ".$inlineQ,
        "message_text" => "אתה ממש סטלן גם חסוי וגם עצלן!
		[".$inlineQ."](http://he.lmgtfy.com/?s=d&q=".$inlineQ.")",
        "reply_markup" => $reply_markup,
        "thumb_url" => 'https://t.me/i/userpic/320/Search_ILBOT.jpg',  
        "parse_mode" => "Markdown",
    );*/
$bing = array(
        "type" => "article",
        "id" => "005",
        "title" => "Bing - בינג",
		"description" => "תוצאות חיפוש על: ".$inlineQ,
        "message_text" => "למה אתה מחפש בבינג לעזזל ?!
		[".$inlineQ."](https://bing.com/search?q=".$inlineQ.")",
        "reply_markup" => $reply_markup,
        "thumb_url" => 'https://t.me/i/userpic/320/Search_ILBOT.jpg',  
        "parse_mode" => "Markdown",
    );
/*$bing_a = array(
        "type" => "article",
        "id" => "006",
        "title" => "Bing - בינג - הסבר איך לחפש",
		"description" => "תוצאות חיפוש על: ".$inlineQ,
        "message_text" => "טוב נו, עוד מילא לחפש בבינג אבל למה - הסבר איך לחפש ??!
		[".$inlineQ."](http://he.lmgtfy.com/?s=b&q=".$inlineQ.")",
        "reply_markup" => $reply_markup,
        "thumb_url" => 'https://t.me/i/userpic/320/Search_ILBOT.jpg',  
        "parse_mode" => "Markdown",
    );*/
$Yahoo = array(
        "type" => "article",
        "id" => "007",
        "title" => "Yahoo - יהוו",
		"description" => "תוצאות חיפוש על: ".$inlineQ,
        "message_text" => "Yahoo:
		[".$inlineQ."](https://search.yahoo.com/search?p=".$inlineQ.")",
        "reply_markup" => $reply_markup,
        "thumb_url" => 'https://t.me/i/userpic/320/Search_ILBOT.jpg',  
        "parse_mode" => "Markdown",
    );
/*$Yahoo_a = array(
        "type" => "article",
        "id" => "008",
        "title" => "Yahoo - יהוו - הסבר איך לחפש",
		"description" => "תוצאות חיפוש על: ".$inlineQ,
        "message_text" => "Yahoo - הסבר איך לחפש כמוך:
		[".$inlineQ."](http://he.lmgtfy.com/?s=y&q=".$inlineQ.")",
        "reply_markup" => $reply_markup,
        "thumb_url" => 'https://t.me/i/userpic/320/Search_ILBOT.jpg',  
        "parse_mode" => "Markdown",
    );*/
$aol = array(
        "type" => "article",
        "id" => "009",
        "title" => "Aol. - אול",
		"description" => "תוצאות חיפוש על: ".$inlineQ,
        "message_text" => "Aol.
		[".$inlineQ."](https://search.aol.com/aol/search?q=".$inlineQ.")",
        "reply_markup" => $reply_markup,
        "thumb_url" => 'https://t.me/i/userpic/320/Search_ILBOT.jpg',  
        "parse_mode" => "Markdown",
    );
/*$aol_a = array(
        "type" => "article",
        "id" => "010",
        "title" => "Aol. - אול - הסבר איך לחפש",
		"description" => "תוצאות חיפוש על: ".$inlineQ,
        "message_text" => "Aol. - הסבר איך לחפש
		[".$inlineQ."](http://he.lmgtfy.com/?s=a&q=".$inlineQ.")",
        "reply_markup" => $reply_markup,
        "thumb_url" => 'https://t.me/i/userpic/320/Search_ILBOT.jpg',  
        "parse_mode" => "Markdown",
    );*/
$clearch = array(
        "type" => "article",
        "id" => "011",
        "title" => "search.clearch - חיפוש בטוח",
		"description" => "תוצאות חיפוש על: ".$inlineQ,
        "message_text" => "החיפוש הכי מעפן שקיים ! יותר מבינג
		[".$inlineQ."](http://search.clearch.org/?q=".$inlineQ.")",
        "reply_markup" => $reply_markup,
        "thumb_url" => 'https://t.me/i/userpic/320/Search_ILBOT.jpg',  
        "parse_mode" => "Markdown",
    );
$StartPage = array(
        "type" => "article",
        "id" => "012",
        "title" => "StartPage   - חיפוש בסתר",
		"description" => "תוצאות חיפוש על: ".$inlineQ,
        "message_text" => "StartPage:
        החיפוש בסתר לא שומר עליך שום מידע אבל האתרים שתיכנס אליהם כן
		[".$inlineQ."](https://www.startpage.com/do/dsearch?query=".$inlineQ.")",
        "reply_markup" => $reply_markup,
        "thumb_url" => 'https://t.me/i/userpic/320/Search_ILBOT.jpg',  
        "parse_mode" => "Markdown",
    );
$youtube = array(
        "type" => "article",
        "id" => "013",
        "title" => "Youtube - יוטיוב",
		"description" => "תוצאות חיפוש על: ".$inlineQ,
        "message_text" => "Youtube:
		[".$inlineQ."](https://www.youtube.com/results?search_query=".$inlineQ.")",
        "reply_markup" => $reply_markup,
        "thumb_url" => 'https://t.me/i/userpic/320/Search_ILBOT.jpg',  
        "parse_mode" => "Markdown",
    );
$wikipdia = array(
        "type" => "article",
        "id" => "014",
        "title" => "Wikipedia - ויקיפדיה",
		"description" => "תוצאות חיפוש על: ".$inlineQ,
        "message_text" => "Wikipedia:
		[".$inlineQ."](https://he.wikipedia.org/w/index.php?search=".$inlineQ."&title=מיוחד:חיפוש&go=לערך)",
        "reply_markup" => $reply_markup,
        "thumb_url" => 'https://t.me/i/userpic/320/Search_ILBOT.jpg',  
        "parse_mode" => "Markdown",
    );
$ebay = array(
        "type" => "article",
        "id" => "015",
        "title" => "Ebay - איביי",
		"description" => "תוצאות חיפוש על: ".$inlineQ,
        "message_text" => "עדיף אליאקספרס (;
		[".$inlineQ."](https://www.ebay.com/sch/i.html?_odkw=5&_osacat=0&_from=R40&_trksid=m570.l1313&_nkw=".$inlineQ."&_sacat=0)",
        "reply_markup" => $reply_markup,
        "thumb_url" => 'https://t.me/i/userpic/320/Search_ILBOT.jpg',  
        "parse_mode" => "Markdown",
    );
$facebook = array(
        "type" => "article",
        "id" => "016",
        "title" => "Facebook - פייסבוק",
		"description" => "תוצאות חיפוש על: ".$inlineQ,
        "message_text" => "Facebook:
		[".$inlineQ."](https://www.facebook.com/public?query=".$inlineQ."&type=all&init=ffs&nomc=0)",
        "reply_markup" => $reply_markup,
        "thumb_url" => 'https://t.me/i/userpic/320/Search_ILBOT.jpg',  
        "parse_mode" => "Markdown",
    );
$reply_markup1 = json_encode(array(
  'inline_keyboard' =>  array(
    //line 1
   array(array('text' => '🇮🇱ממלכת הרובוטים הישראלית🇮🇱', 'url' => 't.me/il_BOTS')),
    //line 2
   array(array('text' => 'חיפוש🔍', 'switch_inline_query' => ""))
   //line 3
   )));
switch($message)
{
    case "/start":
        sendMessage($chatId, "ברוך הבא!
לשימוש ברובוט יש לכתוב בשורת ההודעה את שם הבוט ואחר מכן את מה שברצונך לחפש.
לחצו על הכפתור בשביל לנסות זאת!
תהנו.\nלהערות / הארות נא לשלוח הודעה ל- [יהודה אייזנברג](tg://user?id=291563178)",$reply_markup1);
        break;
    case "/start a":
        sendMessage($chatId, "ברוך הבא!
לשימוש ברובוט יש לכתוב בשורת ההודעה את שם הבוט ואחר מכן את מה שברצונך לחפש.
לחצו על הכפתור בשביל לנסות זאת!
תהנו.\nלהערות / הארות נא לשלוח הודעה ל- [יהודה אייזנברג](tg://user?id=291563178)",$reply_markup1);
        break;
    case "/help":
        //sendMessage($chatId,"תפריט בבניה, אשמח אם תוכל להסריט מסך להסבר ולשלוח  [לי](tg://user?id=291563178) בפרטי.
        //אם אתה מסתבך איך להשתמש בבוט אתה יכול לפנות [אליי](tg://user?id=291563178) בפרטי ואני אסביר לך.");
       $markup = json_encode(array('inline_keyboard' =>  array(array(array('text' => 'שיתוף', 'switch_inline_query' => "סרטון")))));
        $postData = array(
            'chat_id' => $chatId,
            'video' =>  "BAADBAADxQQAAqTMUVH7vwe5r_FsmQI",
            'caption' => "אם הסרטון עדיין לא ברור לך אתה מוזמן לפנות ל @YehudaEisenberg",
            'reply_markup' => $markup
            );
        curlPost("sendVideo",$postData);
        break;
    case "/about":
        sendMessage($chatId,"קרדיט ענק ל- [יהודה אייזנברג](tg://user?id=291563178) שיצר את הבוט!");
        break;
    case "עוד בוטים":
        sendMessage($chatId,"עוד בוטים.");
        break;
    default:
        sendMessage($chatId, "החיפוש אינו פעיל בתוך הבוט אלא רק באינליין.
        לחצו על הכפתור שמתחת בשביל לחפש את זה.", json_encode(array('inline_keyboard' =>  array(array(array('text' => 'חיפוש🔍', 'switch_inline_query' => $message))))));
/*sendMessage($chatId,"```".file_get_contents('php://input')."```");*/
break;
}
if($callData == "1")
	editMessageText(null, $InlineMsId, "כמות החיפושים בבוט היא: ".f_count(false), json_encode($rm_refresh));
//	$InlineMsId
if($InlineQId) {
    if($inlineQ == "" )
    {
        $start = array(
			"type" => "article",
			"id" => "1",
			"title" => "אודות הבוט",
			"description" => "הקלידו: \"אודות הבוט\"",
			"message_text" => "בשביל פרטים נוספים הקלידו בשורת ההודעה `@Search_ILBOT אודות הבוט ` וקבלו פרטים נוספים על הבוט",
			"thumb_url" => 'https://t.me/i/userpic/320/Search_ILBOT.jpg',  
			"parse_mode" => "Markdown");
	       	$mResult = array($start);
        answerInline($InlineQId,json_encode($mResult));
    }
	if($inlineQ == "אודות הבוט" )
    {
        $credit = array(
			"type" => "article",
			"id" => "1",
			"title" => "אודות הבוט",
			"description" => "קרדיט ל-@YehudaEisenberg",
			"message_text" => "הבוט נוצר ע\"י [יהודה אייזנברג](tg://user?id=291563178)!
			אם ברצונכם להוסיף אפשרות חיפוש כל שהיא שלחו ל-[יהודה אייזנברג](tg://user?id=291563178) את החיפוש וקישור אליו.",
			"thumb_url" => 'https://t.me/i/userpic/320/Search_ILBOT.jpg',  
			"parse_mode" => "Markdown");
        $count = array(
			"type" => "article",
			"id" => "2",
			"title" => "כמות חיפושים",
			"description" => "כמות חיפושים בבוט הנהדר הזה",
			"message_text" => "כמות החיפושים בבוט היא: ".f_count(false),
			"thumb_url" => 'https://t.me/i/userpic/320/Search_ILBOT.jpg',
			"reply_markup" => $rm_refresh,
			"parse_mode" => "Markdown");
		$block = array(
	    		"type" => "article",
		    	"id" => "1",
		    	"title" => "הבוט חסום",
			    "message_text" => "הבוט חסום כרגע לשימושך אנא נסה שנית מאוחר יותר.
        לכניסה לגרסת הבטא פנה ל-[יהודה אייזנברג](tg://user?id=291563178)",
        "thumb_url" => 'https://t.me/i/userpic/320/Search_ILBOT.jpg',  
	    		"parse_mode" => "Markdown");
		if(f_block($InlineQId))
	       	$mResult = array($credit,$count);
		else
			$mResult = array($block);
        answerInline($InlineQId,json_encode($mResult));
    }
    elseif($inlineQ == "סרטון")
    {
        $markup = array('inline_keyboard' =>  array(array(array('text' => 'שיתוף', 'switch_inline_query' => "סרטון"))));
        $mResult = array(array(
			"type" => "video",
			"id" => "1",
			"video_file_id" => "BAADBAADxQQAAqTMUVH7vwe5r_FsmQI",
			"title" => "סרטון הסבר לשימוש בבוט",
			"description" => "לחץ כאן בשביל לשלוח את הסרטון",
			"caption" => "אם הסרטון עדיין לא ברור לך אתה מוזמן לפנות ל @YehudaEisenberg",
			"reply_markup" => $markup
			));
        answerInline($InlineQId,json_encode($mResult));
    }
    else
    {
		$block = array(
	    		"type" => "article",
		    	"id" => "1",
		    	"title" => "הבוט חסום",
			    "message_text" => "הבוט חסום כרגע לשימושך אנא נסה שנית מאוחר יותר.
        לכניסה לגרסת הבטא פנה ל-[יהודה אייזנברג](tg://user?id=291563178)",
        "thumb_url" => 'https://t.me/i/userpic/320/Search_ILBOT.jpg',  
	    		"parse_mode" => "Markdown");
		if(true)//is block
			$mResult = array($google/*,$google_a*/,$duckduckgo/*,$duckduckgo_a*/,$bing/*,$bing_a*/,$Yahoo/*,$Yahoo_a*/,$aol/*,$aol_a*/,$clearch,$StartPage,$youtube,$wikipdia,$ebay,$facebook);
		else
			$mResult = array($block);
        answerInline($InlineQId,json_encode($mResult));
        f_count(true);
    }
}
}
?>