<?php
header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set('Asia/Jerusalem');

$update = file_get_contents('php://input');   
$update = json_decode($update, TRUE);

define('YOUR_ID' "");
define('TOKEN' "");

function curlPost($method,$datas=[]==NULL){    
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
        return json_decode($res,true);
    }
}

function compressFolder($path,$filename){

	$rootPath = realpath($path);

	// Initialize archive object
	$zip = new ZipArchive();
	$zip->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);

	// Create recursive directory iterator
	/** @var SplFileInfo[] $files */
	$files = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator($rootPath),
		RecursiveIteratorIterator::LEAVES_ONLY
	);

	foreach ($files as $name => $file)
	{
		// Skip directories (they would be added automatically)
		if (!$file->isDir())
		{
			// Get real and relative path for current file
			$filePath = $file->getRealPath();
			$relativePath = substr($filePath, strlen($rootPath) + 1);

			// Add current file to archive
			$zip->addFile($filePath, $relativePath);
		}
	}

	// Zip archive will be created only after closing object
	$zip->close();
}

function sendMessage($id, $message){
	$PostData = array(
		'chat_id' => $id,
		'text' => $message,
		'parse_mode' => "Markdown", 
		'disable_web_page_preview' => true
	);
	return curlPost('sendMessage',$PostData);
}

$message = $update["message"]["text"];
$id = $update["message"]["chat"]["id"];

if($id == YOUR_ID){
    if($message == "/start")
        sendMessage($id, "Send me folder name");
    $date = date("d-m-y");
    $time = date("h:i:s");
    if($message == "Folder1")
        $path = "Folder1";
    elseif($message == "Folder2")
        $path = "Folder2";
    elseif($message != "/start")
        sendMessage($id, "Invalid folder name!"."\n\nThe valids name: Folder1, Folder2");
    if($path){
        $filename = $message." , ".$date." , ".$time.".zip";
        compressFolder($path,$filename);
        $postData = array(
            'chat_id' => $id,
            'document' =>  new CURLFile(realpath($filename)),
            'caption' => "The backup is send in date: ".date(DATE_RFC850),
            );
        if(curlPost("sendDocument",$postData));
        unlink($filename);
    }
    //fastcgi_finish_request();
}
else
    sendMessage($id, "The Bot not active");
?>
