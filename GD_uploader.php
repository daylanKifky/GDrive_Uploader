<?php 
/**
 * A wrapper class for a Google Drive client, allowing to upload files
 * Using Google API php client VER 2.1.1
 */

require_once __DIR__ . '/google-api-php-client-2.1.1/vendor/autoload.php';

define('CREDENTIALS_DIR', 'credentials/');

define('APPLICATION_NAME', 'GoogleDrive Client');
define('CREDENTIALS_PATH', CREDENTIALS_DIR.'credentials.json');
define('CLIENT_SECRET_PATH', CREDENTIALS_DIR.'client_secret.json');
// define('SCOPES', implode(' ', array(
//     ::DRIVE_FILE)
// ));

class Queued_File extends Google_Service_Drive_DriveFile{
	private $uploaded = false;
	private $checked = false; 
	private $baseFolder = '';

	public setUploaded(){ $this->uploaded = true; }
	public setCheked(){ $this->checked = true; }

	public isUploaded(){ return $this->uploaded;  }
	public isCheked(){ return $this->checked; }

	public setBaseFolder($path){
		$this -> baseFolder = $path;
	}
}


class GDrive_Uploader extends Google_Client {

	private $service;
	private $uploadQueue;
	private $maxQueue;
	/**
	 * Constructor
	 */
	public function __construct($max = 8){
		parent::__construct();
		$this->service = new Google_Service_Drive($this);
		$this->uploadQueue = array();
		$this->maxQueue = $max;
	}

	/**
	 * Uploader initial config, and obtaining the authorization token from Google Server
	 */
	
	public function init(){
		$this->setApplicationName(APPLICATION_NAME);
	   	$this->setScopes( Google_Service_Drive::DRIVE_FILE );
	   	$this->setAuthConfig(CLIENT_SECRET_PATH);
	   	$this->setAccessType('offline');

	   	// Load previously authorized credentials from a file.
	   	$credentialsPath = expandHomeDirectory(CREDENTIALS_PATH);
	   	if (file_exists($credentialsPath)) {
	   	  $accessToken = json_decode(file_get_contents($credentialsPath), true);
	   	} else {
	   		  // Request authorization from the user.
	   		  $authUrl = $this->createAuthUrl();
	   		  printf("Open the following link in your browser:\n%s\n", $authUrl);
	   		  print 'Enter verification code: ';
	   		  $authCode = trim(fgets(STDIN));

	   		  // Exchange authorization code for an access token.
	   		  $accessToken = $this->fetchAccessTokenWithAuthCode($authCode);

	   		  // Store the credentials to disk.
	   		  if(!file_exists(dirname($credentialsPath))) {
	   		    mkdir(dirname($credentialsPath), 0700, true);
	   		  }
	   		  file_put_contents($credentialsPath, json_encode($accessToken));
	   		  printf("Credentials saved to %s\n", $credentialsPath);
	   		}
	   	$this->setAccessToken($accessToken);

	   // Refresh the token if it's expired.
	   if ($this->isAccessTokenExpired()) {
	     $this->fetchAccessTokenWithRefreshToken($this->getRefreshToken());
	     file_put_contents($credentialsPath, 
	     					json_encode($this->getAccessToken()));
	   }

	}

	/**
	 * Upload a file
	 * TODOC
	 */
	
	protected function uploadFile($path, $description){
		//Insert a file
		$file = new Google_Service_Drive_DriveFile();
		$file->setName($name.'.jpg');
		$file->setDescription($description);
		$file->setMimeType('image/jpeg');

		$data = file_get_contents($path);

		 try {
			 $createdFile = $this->service->files->create($file, array(
			       'data' => $data,
			       'mimeType' => 'image/jpeg',
			       'uploadType' => 'multipart'
			     ));

			 print_r($createdFile);
		 	
		} catch (Google_Service_Exception $e) {
			print_r("Error while uploading file\n");
			foreach ($e->getErrors() as $error) {
				print_r($error["message"]);
			}

			exit();
		}
	}

	public function PUblicuploadFile($name, $description, $path){
		$this->uploadFile($name, $description, $path);
	}

class GDrive_Selective_Uploader extends GDrive_Uploader{
	private $allowedTypes;

	public function __construct($allowed = array() ,$max = 8){
		parent::__construct($max);
		$this -> allowedTypes = $allowed;

	}

	private function getExtensionFromPath($path){

		return $extension;
	}

	private function getFilenameFromPath($path, $trimExtension){
		$fileNameParts = explode(basename($path), ".");
		
		if ()

		return basename
	}

}

}


 ?>

<?php 
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

$client->PUblicuploadFile('testUploadMemberFN'.'.jpg',
							'Another test document',
							'a.jpg');

?>

 <?php
 /**
  * OLD APPLICATION TOERASE!!
  */
 // Get the API client and construct the service object.
 // $client = getClient();
//  $service = new Google_Service_Drive($client);

//  //Insert a file
//  $file = new Google_Service_Drive_DriveFile();
//  $file->setName('testUploadCLASS'.'.jpg');
//  $file->setDescription('Another test document');
//  $file->setMimeType('image/jpeg');

//  $data = file_get_contents('a.jpg');


//  try {
// 	 $createdFile = $service->files->create($file, array(
// 	       'data' => $data,
// 	       'mimeType' => 'image/jpeg',
// 	       'uploadType' => 'multipart'
// 	     ));

// 	 print_r($createdFile);
 	
// } catch (Google_Service_Exception $e) {
// 	print_r("Error while uploading file\n");
// 	foreach ($e->getErrors() as $error) {
// 		print_r($error["message"]);
// 	}

// 	exit();
// }

  ?>
  <?php 
  /**
   * Function definition
   */
  
  function getClient() {
   $client = new GDrive_Uploader();
   $client->setApplicationName(APPLICATION_NAME);
   $client->setScopes( Google_Service_Drive::DRIVE_FILE );
   $client->setAuthConfig(CLIENT_SECRET_PATH);
   $client->setAccessType('offline');

   // Load previously authorized credentials from a file.
   $credentialsPath = expandHomeDirectory(CREDENTIALS_PATH);
   if (file_exists($credentialsPath)) {
     $accessToken = json_decode(file_get_contents($credentialsPath), true);
   } else {
     // Request authorization from the user.
     $authUrl = $client->createAuthUrl();
     printf("Open the following link in your browser:\n%s\n", $authUrl);
     print 'Enter verification code: ';
     $authCode = trim(fgets(STDIN));

     // Exchange authorization code for an access token.
     $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

     // Store the credentials to disk.
     if(!file_exists(dirname($credentialsPath))) {
       mkdir(dirname($credentialsPath), 0700, true);
     }
     file_put_contents($credentialsPath, json_encode($accessToken));
     printf("Credentials saved to %s\n", $credentialsPath);
   }
   $client->setAccessToken($accessToken);

   // Refresh the token if it's expired.
   if ($client->isAccessTokenExpired()) {
     $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
     file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
   }
   return $client;
 }

 /**
  * Expands the home directory alias '~' to the full path.
  * @param string $path the path to expand.
  * @return string the expanded path.
  */
 function expandHomeDirectory($path) {
   $homeDirectory = getenv('HOME');
   if (empty($homeDirectory)) {
     $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
   }
   return str_replace('~', realpath($homeDirectory), $path);
 }

   ?>