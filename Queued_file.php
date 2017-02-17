<?php 

class Queued_File extends Google_Service_Drive_DriveFile{
	private $remoteId = null;
	private $checked = false; 
	private $localPath = '';
	private $fileData = null;
	private $localChecksum;


	public function setLocalChecksum($hash){$this->localChecksum = $hash;}
	public function getLocalChecksum(){return $this->localChecksum;}

	public function setData($d){ $this->fileData = $d;}
	public function getData(){ return $this->fileData;}

	public function setRemoteId($id){ $this->remoteId = $id; }
	public function isUploaded(){ return $this->remoteId;  }

	public function setChecked(){ $this->checked = true; }
	public function isChecked(){ return $this->checked; }

	public function setLocalPath($path){
		$this->localPath = $path;
	}

	public function getLocalpath(){
		return $this->localPath;
	}
}


 ?>