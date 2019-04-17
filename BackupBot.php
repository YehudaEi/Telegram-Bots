<?php
header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set('Asia/Jerusalem');

$update = file_get_contents('php://input');   
$update = json_decode($update, TRUE);

define('CHANNEL_ID' "");
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

$date = date("d-m-y");
$time = date("h:i:s");
$folders = ["../public_html"];
for($i = 0; $i < count($folders); $i++){
    $filename = "Domain name , ".$date." , ".$time.".zip";
    compressFolder($folders[$i],$filename);
    $postData = array(
        'chat_id' => CHANNEL_ID,
        'document' =>  new CURLFile(realpath($filename)),
        'caption' => "The backup is send in date: ".date(DATE_RFC850),
        );
    echo curlPost("sendDocument",$postData);
    unlink($filename);
}
?>
