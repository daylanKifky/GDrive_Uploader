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
			echo "FILE TOO BIG";
			return null;
			//TODO: alert!!
			}

		if (!in_array($this->getMIMEFromPath($args['path']), 
					$this->allowedTypes)){
			print_r("NOT ALLOWED TYPE \n");
			return null;		
			}
		return parent::createQueuedFile($args);
	}	



	// private function getExtensionFromPath($path){

	// 	return $extension;
	// }



}

 ?>