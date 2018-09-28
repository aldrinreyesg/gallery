<?php

namespace AppBundle\Utils;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Carbon\Carbon;
use League\Csv\Reader;
use League\Csv\Writer;
use AppBundle\Entity\Images;
use AppBundle\Utils\Download;
use AppBundle\Utils\ImageProcesor;
use AppBundle\Utils\ImageClean;
use AppBundle\Entity\ImagesRepository;

Class Csv {
	
    public function init(){
        self::dirCreate($this->get('kernel')->getRootDir().'/../web/csv');
        self::dirCreate($this->get('kernel')->getRootDir().'/../web/images');
        self::dirCreate($this->get('kernel')->getRootDir().'/../web/images/gallery');
        self::dirCreate($this->get('kernel')->getRootDir().'/../web/images/gallery/thumbnail');
    }

    private function dirCreate($path){
        $fileSystem = new Filesystem();
        
        if(!$fileSystem->exists($path)){
            try {
                $fileSystem->mkdir($path);
            } catch (IOExceptionInterface $exception) {
                echo "An error occurred while creating csv directory at ".$exception->getPath();
                return false;
            }
        }
    }


	public function download($url){
    	$path=$this->get('kernel')->getRootDir().'/../web/csv';
        $fileName="/images_".Carbon::now()->format("Ymd").'.csv';
        if(Download::downloadFile($url, $path, $fileName)){
            return $path.$fileName;
        }else{
            return false;
        }
    }

	public function import($path){
        
        $csv = Reader::createFromPath($path, 'r');
        $csv->setOffset(1); //because we don't want to insert the header
        $em = $this->getDoctrine()->getManager();
        foreach($csv as $index=>$row) {
            $newName=mt_rand().'.jpg';
            $valid1=ImageProcesor::validateReg($row[0],$row[1]);

            $valid2=ImageProcesor::validateUrl(
                    $row[2],
                    $newName);
            if(!$valid2){
                $newName="image-not-found.png";
            }

            ImagesRepository::add(
                    $row[0],
                    $row[1],
                    $row[2],
                    ($valid1==false || $valid2 == false) ? false : true,
                    $newName);
        }
        return true;
    }

    public function export($rows){
        $fname=$this->get('kernel')->getRootDir().'/../web/csv/export_'.Carbon::now()->format("Ymd").'.csv';
        $writer = Writer::createFromPath($fname, 'w+');
        $writer->insertOne(["id", "title", "description", "url", "name", "created", "valid"]);
        foreach ($rows as $key => $value) {
            $writer->insertOne(
                [$value->getId(), 
                $value->getTitle(), 
                $value->getDescription(), 
                $value->getImage_url(),
                $value->getImage_name(),
                new Carbon($value->getCreated()->format('r')),
                $value->getValid() ? 'true' : 'false']);
        }
        return $fname;
    }
}