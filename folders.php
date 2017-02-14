<?php
require_once __DIR__ . '/GD_selective_uploader.php'; 
/**
* GDrive_Uploader application
*/
// var_dump( ini_get('max_execution_time') );
//Can I pass a timeout to guzzle?
//http://docs.guzzlephp.org/en/latest/request-options.html#timeout


define("FOLDER_MIME", 'application/vnd.google-apps.folder');

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


$driveService = new Google_Service_Drive($client);


//Create folder and retrive ID
$fileMetadata = new Google_Service_Drive_DriveFile(array(
  'name' => 'Edicion 2017_01',
  'mimeType' => 'application/vnd.google-apps.folder'));

$file = $driveService->files->create($fileMetadata, array(
  'fields' => 'id'));

printf("Folder ID: %s\n", $file->id);


//Search for all the folders
$pageToken = null;
do {
  $response = $driveService->files->listFiles(array(
    // 'q' => "mimeType='".FOLDER_MIME."'",
    'q' => "mimeType='".FOLDER_MIME."' and trashed = false",
    // 'q' => "'root' in parents",
    'spaces' => 'drive',
    'pageToken' => $pageToken,
    // 'fields' => 'nextPageToken, files(id, name)',
    // 
    
  ));

  // var_dump($response);
  foreach ($response->files as $file) {
      printf("Found file: %s (%s)\n", $file->name, $file->id);
  }
} while ($pageToken != null);




?>

