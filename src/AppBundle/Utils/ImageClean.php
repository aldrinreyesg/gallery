<?php

namespace AppBundle\Utils;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Psr\Log\LoggerInterface;
use AppBundle\Entity\ImagesRepository;

Class ImageClean{

	private $gallery;
	private $thumbnail;
	
	private function init(){
		
        $this->gallery = $this->get('kernel')->getRootDir().'/../web/images/gallery';
        $this->thumbnail = $this->get('kernel')->getRootDir().'/../web/images/gallery/thumbnail';
    }

	public function del($fname){
		self::init();
		$fs = new Filesystem();
		if($fs->exists($fname)){
			$fs->remove($fname);
			return true;
		}
		return false;
	}

	public function cleanAll($files){
		self::init();
		$result=array();
		foreach ($files as $key => $value) {
			
			$result[]=["file"=>$value, "result"=> (self::del($this->gallery.'/'.$value["image_name"])) ? "Eliminado" : "Error al eliminar"];
			$result[]=["file"=>$value, "result"=> (self::del($this->thumbnail.'/'.$value["image_name"])) ? "Eliminado" : "Error al eliminar"];
		}
		return $result;
	}

	public function delImgFromGall($id){
		self::init();
		$file = ImagesRepository::getImgFileName($id);
		self::del($this->gallery.'/'.$file[0]["image_name"]);
		self::del($this->thumbnail.'/'.$file[0]["image_name"]);
	}
}
