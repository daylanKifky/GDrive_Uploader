<?php
require_once __DIR__ . '/GD_selective_uploader.php'; 
/**
* GDrive_Uploader application
*/
var_dump( ini_get('max_execution_time') );
//Can I pass a timeout to guzzle?
//http://docs.guzzlephp.org/en/latest/request-options.html#timeout


$types = array("jpg", "png", "tif");
try {
	$client = new GDrive_Selective_Uploader($types);
	$client -> init();
	
} catch (Google_Service_Exception $e) {
	print_r("Error while requesting autorization\n");
	foreach ($e->getErrors() as $error) {
		print_r($error["message"]);
	}
	exit();
}

$fileArgs = array(
			"path" => 'testUpload2.jpg',
			"description" => "Selective upload");


$thefile = $client->createQueuedFile($fileArgs);



$client->addToQueue($thefile);



// $client->processQueue();
	



?>

