<?php

namespace AppBundle\Utils;

use Intervention\Image\ImageManagerStatic as Image;

Class ImageProcesor{
	
	public function thumbnail($url, $fname){

		Image::configure(array('driver' => 'imagick'));
		
			$image = Image::make($url)
					->resizeCanvas(300, 200)
					->save($this->get('kernel')->getRootDir().'/../web/images/gallery/thumbnail/'.$fname, 60);
			if($image){
				return true;
			}
		
		return false;
	}

    public function validateReg($title, $description){
        
        if(strlen($title)>50){
            return false;
        }elseif($title==null){
        	return false;
        }elseif(strlen($description)>255){
            return false;
        }
        
        return true;
    }
    public function validateUrl($url, $newName){
    	if($url == null || strlen($url)>600){
    	    return false;
    	}else{  

	    	$downloaded=Download::downloadFileCurl($url, $this->get('kernel')->getRootDir().'/../web/images/gallery/', $newName);
	    	if(!$downloaded){
	    	    return false;
	    	}else{
	    	    $mimetype = mime_content_type($this->get('kernel')->getRootDir().'/../web/images/gallery/'. $newName);
	    	    
	    	    if($mimetype=="text/html"){
	    	        ImageClean::del($this->get('kernel')->getRootDir().'/../web/images/gallery/'.$newName);
	    	        return false;
	    	    }else{
	    	        if(!self::thumbnail($url, $newName)){
	    	            return false;
	    	        }
	    	    }
	    	}
    	}
    	return true;
    }
}