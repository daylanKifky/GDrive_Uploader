<?php 
require_once __DIR__ . '/GD_uploader.php';

class GDrive_Selective_Uploader extends GDrive_Uploader{
	private $allowedTypes;
	//TODO: max allowed filesize

	public function __construct($allowed = array() ,$max = 8){
		parent::__construct($max);
		$this->allowedTypes = array();
		$this->setAllowed($allowed);

	}



	private function setAllowed($allowedExtensions){
		foreach ($allowedExtensions as $a) {
			$type = $this -> extensionToMIME($a);
			if (!in_array($type, $this->allowedTypes))
				$this->allowedTypes[] = $type;
		}
	}

	



	// private function getExtensionFromPath($path){

	// 	return $extension;
	// }



}

 ?>