<?php
require_once __DIR__ . '/GD_selective_uploader.php'; 
/**
* GDrive_Uploader application
*/

$types = array("jpg", "png", "tif");
try {
	$client = new GDrive_Selective_Uploader($types, 3*1024*1024, 5);
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
			"description" => "Selective upload");


$thefile = $client->createQueuedFile($fileArgs);


for ($i=0; $i < 10; $i++) { 
	# code...
	$client->addToQueue($thefile);
}


$client->processQueue();


?>

