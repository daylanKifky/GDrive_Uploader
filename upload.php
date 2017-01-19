<?php
require_once __DIR__ . '/GD_selective_uploader.php'; 
/**
* GDrive_Uploader application
*/
try {
	$client = new GDrive_Uploader();
	$client -> init();
	
} catch (Google_Service_Exception $e) {
	print_r("Error while requesting autorization\n");
	foreach ($e->getErrors() as $error) {
		print_r($error["message"]);
	}
	exit();
}

$fileArgs = array(
			"path" => 'a.jpg',
			"description" => "a file upladed from the QUEUE");


$thefile = $client->createQueuedFile($fileArgs);

$client->addToQueue($thefile);

$fileArgs['path'] = "maggot-eyeofscience.jpg";

$thefile = $client->createQueuedFile($fileArgs);

$client->addToQueue($thefile);

$fileArgs['path'] = "testUpload2.jpg";

$thefile = $client->createQueuedFile($fileArgs);

$client->addToQueue($thefile);

$client->processQueue();


?>

