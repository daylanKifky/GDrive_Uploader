<?php 
require_once __DIR__ . '/GD_uploader.php';



class GDrive_Selective_Uploader extends GDrive_Uploader{
	private $allowedTypes;
	private $allowedSize;

	public function __construct($allowed = array(),
								$size=20*1024*1024,
								$max = 8){
		parent::__construct($max);
		$this->allowedTypes = array();
		$this->setAllowed($allowed);
		$this->allowedSize = $size;
	}

	private function setAllowed($allowedExtensions){
		foreach ($allowedExtensions as $a) {
			$type = $this -> extensionToMIME($a);
			if (!in_array($type, $this->allowedTypes))
				$this->allowedTypes[] = $type;
		}

		if( count($this->allowedTypes) == 0)
			;//TODO alert!!
	}

	public function createQueuedFile($args){

		if (filesize($args['path']) >= $this->allowedSize){
			throw new GD_Uploader_Exception("FILE_TOO_BIG", "file: ".basename($args['path']) . " size: " .filesize($args['path']).", MAX ALLOWED: ".$this->allowedSize);

			}

		if (!in_array($this->getMIMEFromPath($args['path']), 
					$this->allowedTypes)){
			throw new GD_Uploader_Exception("BAD_FILETYPE", "file: ".basename($args['path']) ." is of type: " . $this->getMIMEFromPath($args['path']));		
			}
		return parent::createQueuedFile($args);
	}	

	public function getAllowedTypes(){
		return $this->allowedTypes;
	}


	// private function getExtensionFromPath($path){

	// 	return $extension;
	// }



}

 ?>