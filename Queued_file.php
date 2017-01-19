<?php 
class Queued_File extends Google_Service_Drive_DriveFile{
	private $uploaded = false;
	private $checked = false; 
	private $baseFolder = '';
	private $fileData = null;

	public function setData($d){ $this->fileData = $d;}
	public function getData(){ return $this->fileData;}


	public function setUploaded(){ $this->uploaded = true; }
	public function setCheked(){ $this->checked = true; }

	public function isUploaded(){ return $this->uploaded;  }
	public function isCheked(){ return $this->checked; }

	public function setBaseFolder($path){
		$this -> baseFolder = $path;
	}
}
 ?>