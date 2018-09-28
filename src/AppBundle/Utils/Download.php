<?php

namespace AppBundle\Utils;

Class Download {

	public function downloadFileCurl($url, $path, $fileName=null)
    {
        if(!$fileName){
            $fileName=basename($url);
        }
        $newfname = $path . $fileName;
        if(filter_var($url, FILTER_VALIDATE_URL)){

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $data=curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if($httpCode == '404'){
                return false;
            }else{
                if($data){
                    $file = fopen($newfname, "w+");
                    fputs($file, $data);
                    fclose($file);
                    file_put_contents($newfname, $data);    
                }else{
                    return false;
                }
            }
        }else{
            return false;
        }
        return true;
    }	
	public function downloadFile($url, $path, $fileName=null)
	{
	    if(!$fileName){
	        $fileName=basename($url);
	    }
	    $newfname = $path . $fileName;
	    if(filter_var($url, FILTER_VALIDATE_URL)){
	        try {
	            $exists = ($file = fopen ($url, 'rb')) !== false;
	            if ($exists) {
	                $newf = fopen ($newfname, 'wb');
	                if ($newf) {
	                    while(!feof($file)) {
	                        fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
	                    }
	                }else{
	                    return false;    
	                }
	                
	            }else{
	                return false;
	            }
	            if ($file) {
	                fclose($file);
	            }
	            if ($newf) {
	                fclose($newf);
	            }    
	        } catch (Exception $e) {
	            return false;
	        }
	        
	    }else{
	        return false;
	    }
	    return true;
	}
}