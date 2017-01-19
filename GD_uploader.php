<?php 
/**
 * A wrapper class for a Google Drive client, allowing to upload files
 * Using Google API php client VER 2.1.1
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Queued_file.php';

define('CREDENTIALS_DIR', 'credentials/');

define('APPLICATION_NAME', 'GoogleDrive Client');
define('CREDENTIALS_PATH', CREDENTIALS_DIR.'credentials.json');
define('CLIENT_SECRET_PATH', CREDENTIALS_DIR.'client_secret.json');




class GDrive_Uploader extends Google_Client {

	private $service;
	private $uploadQueue;
	private $maxQueue;
	private static $mimeChecker;
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

	public function extensionToMIME($ext){
		return GDrive_Selective_Uploader::getMIMEChecker() -> getMimeType($ext);
	}

	public function MIMEToExtensions($type){
		return GDrive_Selective_Uploader::getMIMEChecker() -> getAllExtensions($type);		
	}

	private static function getMIMEChecker() {
	    if (!isset(self::$mimeChecker))
	        self::$mimeChecker = new \Mimey\MimeTypes;
	    return self::$mimeChecker;
	}

	private function getFilenameAndExtension($path){
		$fileNameParts = explode("." , basename($path));
		
		if (count($fileNameParts) != 2){
			 throw new GD_Uploader_Exception("BAD_FILE_NAME", "Dots not allowed in filename: ". basename($path));}

		return $fileNameParts;
	}

	private function getExtensionFromPath($path){
		return $this->getFilenameAndExtension($path)[1];
	}

	private function getNameFromPath($path){
		return $this->getFilenameAndExtension($path)[0];
	}

	public function createQueuedFile($args){
		$file = new Queued_File();
		$file->setName($this->getNameFromPath( $args['path']) );
		$file->setDescription( $args['description'] );
		$file->setMimeType( $this->extensionToMIME($this->getExtensionFromPath($args['path'])));

		if ( !($data = file_get_contents($args['path']) ) )
			throw new GD_Uploader_Exception("ERROR_READING_FILE","\nThe file: \n".$args['path']."\ndoesn't exists or is corrupted");
		
		$file->setData($data);
		return $file;
	}

	public function addToQueue($file){
		if (!is_a($file, "Queued_File"))
			return null;
		$this->uploadQueue[] = $file;
	}	

	public function processQueue(){
		foreach ($this->uploadQueue as $task) {
			$this->uploadFile($task);
		}
	}

	/**
	 * Upload a file
	 * TODOC
	 */
	
	protected function uploadFile($file){
		try {
			$createdFile = $this->service->files->create($file, array(
		       'data' => $file->getData(),
		       'mimeType' => $file->getMimeType(),
		       'uploadType' => 'multipart'
			    ));

			print_r($createdFile->id);
 	
		} catch (Google_Service_Exception $e) {
			print_r("Error while uploading file\n");
			foreach ($e->getErrors() as $error) {
				print_r($error["message"]);
			}

			exit();
		}
	}

	//TOERASE
	public function PUblicuploadFile($name, $description, $path){
		$this->uploadFile($name, $description, $path);
	}
}




class GD_Uploader_Exception extends Exception{
	public function __construct($t, $msg){
		$this -> type = $t;
		$this -> message = $msg;
	}

	protected $type;
	protected $message;
}



 ?>



  <?php 
  /**
   * Function definition
   */
  
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