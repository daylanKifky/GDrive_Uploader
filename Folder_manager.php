<?php 

require_once("../i-db.php");
require_once("../session_manager.php");
require_once __DIR__ . '/GD_uploader.php';
define("LOCKED" , true);

class Folder_Manager extends i_DB{
	private $baseFolder;
	private $mimeType = 'application/vnd.google-apps.folder';
	private $db;
	public $params;
	private $gDriveFile;
	private $cachedEditionFolder = null;

	public function __construct($client, $path = "../config.ini"){
		$args = parse_ini_file($path);
		if(!$args)
			throw new SM_Exception(
				"Can't load configuration", 
				"Unknown error", "Folder_manager", 3);

		parent::__construct($args);
		parent::DB_connect($args);
		$this->params = $args;
		$this->baseFolder = $args['IF_CURRENT_EDICION'];

		$driveService = new Google_Service_Drive($client);
		$this->gDriveFile = $driveService->files;

	}

	public function getFolder($firstOrder, $secOrder ){
		$parentID = sprintf("SELECT `folder_id` FROM %s WHERE `DEF_ID` = %s",
			$this->params['IFDB_EXT_FOLDERS'],
			$firstOrder
			);
		$q = sprintf("SELECT `folder_id` FROM %s WHERE `DEF_ID` = %s AND parent_id = (%s)",
			$this->params['IFDB_EXT_FOLDERS'],
			$secOrder,
			$parentID
			);

		$result = $this->query($q);

		if (!$result)
			die($this->error);

		return $result->fetch_assoc()['folder_id'];
	}

	public function getEditionFolder(){
		if ($this->cachedEditionFolder)
			return $this->cachedEditionFolder;

		$result = $this->query(sprintf("SELECT `folder_id` FROM %s WHERE `folder_name` = '%s'",
			$this->params['IFDB_EXT_FOLDERS'],
			$this->baseFolder));

		$id = false;
		if (!$result || $result->num_rows < 1)
			$id = $this->createFolder($this->baseFolder, null, 'root');

		if (!$result)
			die("cant write folder to DB");

		$this->cachedEditionFolder = ($id)? $id : $result->fetch_assoc()['folder_id']; 
		return $this->cachedEditionFolder;
	}

	public function fetchFirstOrderFolders(){
		$q = sprintf(
			"SELECT * FROM %s WHERE `parent_id` = (SELECT `folder_id` from `%s` WHERE `folder_name` = '%s')",
			$this->params['IFDB_EXT_FOLDERS'],
			$this->params['IFDB_EXT_FOLDERS'],
			$this->baseFolder
			);

		// die($q);

		$folders = $this->query($q);

		if (!$folders)
			var_dump($this->error);

		$orders = $this->getFirstOrders();
		if ($folders->num_rows < $orders->num_rows){
			while ($order = $orders-> fetch_assoc()){
				$thisOneMatch = false;
				while($folder = $folders-> fetch_assoc()){
					if ($order['nombre_esp'] == $folder["folder_name"]){
						$thisOneMatch = true;
						break;
					}
				}

				if (!$thisOneMatch){
						$this->createFolder($order['nombre_esp'],$order['DEF_ID'], $this->getEditionFolder());
					}
			}
			$folders = $this->fetchFirstOrderFolders();
		}
		return $folders;
	}

	public function fetchSecondOrderFolders($parent){
		$q = sprintf(
			"SELECT * FROM %s WHERE `parent_id` = '%s'",
			$this->params['IFDB_EXT_FOLDERS'],
			$parent
			);
		$folders = $this->query($q);

		if (!$folders)
			var_dump($this->error);

		$orders = $this->getSecondOrders();
		if ($folders->num_rows < $orders->num_rows){
			while ($order = $orders-> fetch_assoc()){
				$thisOneMatch = false;
				while($folder = $folders-> fetch_assoc()){
					if ($order['nombre_esp'] == $folder["folder_name"]){
						$thisOneMatch = true;
						break;
					}
				}

				if (!$thisOneMatch){
						$this->createFolder($order['nombre_esp'],$order['DEF_ID'], $parent);
					}
			}
			$folders = $this->fetchSecondOrderFolders($parent);
		}
		return $folders;
	}
	

	private function getFirstOrders(){
		$result = $this->query("select `nombre_esp`,`DEF_ID` from ".$this->params['IFDB_DEFINICIONES']." where `tipo` = '".$this->params['IF_FIRST_ORDER']."'");

		if (!$result)
			var_dump($this->error);


		return $result;
	}

	private function getSecondOrders(){
		$result = $this->query("select `nombre_esp`,`DEF_ID` from ".$this->params['IFDB_DEFINICIONES']." where `tipo` = '".$this->params['IF_SECOND_ORDER']."'");

		if (!$result)
			var_dump($this->error);


		return $result;
	}

	private function createFolder($name, $defID, $parent){


		$fileMetadata = new Google_Service_Drive_DriveFile(array(
			  'name' => $name,
			  'mimeType' => 'application/vnd.google-apps.folder',
			  'parents' => array($parent)));
		
		$file = $this->gDriveFile->create($fileMetadata, //throws Google_Service_Exception
			array('fields' => 'id'));

		$q = sprintf("INSERT INTO %s VALUES ('%s', '%s', '%s', %s)",
					$this->params['IFDB_EXT_FOLDERS'],
					$name,
					$file['id'],
					$parent,
					($defID)? "'".$defID."'" : "NULL");

		$result = $this->query($q);

		if (!$result){
			echo $q;
			var_dump($this->error);
			die();
		}

		return ($result)? $file['id'] : false;
	}




}

function createHierarchy (){
	if (LOCKED) die("cannot perform operation");

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

	$fm = new Folder_manager($client);

	// echo $fm->getFolder(4,10);

	$fm->getEditionFolder();
	// var_dump($fm->fetchFirstOrderFolders());

	$barrios = $fm->fetchFirstOrderFolders();

	var_dump($barrios);

	while ($barrio = $barrios->fetch_assoc()){;

		echo $barrio['folder_name'];
		echo "\n";
		echo $barrio['folder_id'];
		echo "\n";

		$cats = $fm->fetchSecondOrderFolders($barrio['folder_id']);

		// var_dump($cats);

	}
}

 ?>