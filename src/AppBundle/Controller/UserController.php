<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Intervention\Image\ImageManagerStatic as Image;
use AppBundle\Entity\ImagesRepository;
use AppBundle\Entity\Images;
use AppBundle\Utils\ImageProcesor;
use AppBundle\Utils\ImageClean;

class UserController extends Controller{
	/**
     * @Route("/user", name="show")
     */
	public function showAction(Request $request)
	{
        $messages=array();
        $msg_type="success";
		if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('login');
        }else{
            $error=$request->query->get('error');
            if($error){
                $msg_type="warning";
                switch($error){
                    case '504':
                        $messages[]=["text"=>"Parámetros invalidos"];
                        break;
                    case '505':
                        $messages[]=["text"=>"El título ya existe en la galería"];
                        break;
                    case '100':
                        $messages[]=["text"=>"Operación no ejecutada"];
                        break;
                }
            }
            $ok=$request->query->get('ok');
            if($ok==100){
                $messages[]=["text"=>"Operación ejecutada con éxito"];
            }
        	return $this->render('user/list.html.twig', array("images" => ImagesRepository::sortByName(), "messages" => $messages, "msg_type" => $msg_type));
        }
	}
    /**
     * @Route("/upload", name="upload")
     * @method ("POST")
     */
    public function uploadAction(Request $request)
    {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('login');
        }else{

            $source = $request->request->get("source");
            $newName=mt_rand().'.jpg';
            $valid=ImageProcesor::validateReg(
                    $request->request->get("title"),
                    $request->request->get("desc")
                );
            if(!$valid){
                return $this->redirectToRoute('user', array("error"=> '504'));   
            }
            if($source=="url"){
                $valid=ImageProcesor::validateUrl(
                    $request->request->get("url"),
                    $newName);
                if(!$valid){
                    $newName="image-not-found.png";
                }

            }else{
                $img = Image::make($_FILES['fileToUpload']['tmp_name']);
                $img->save($this->get('kernel')->getRootDir().'/../web/images/gallery/'. $newName);
                ImageProcesor::thumbnail($this->get('kernel')->getRootDir().'/../web/images/gallery/'. $newName, $newName);
            }
            if(ImagesRepository::add(
                    $request->request->get("title"),
                    $request->request->get("desc"),
                    $request->request->get("url"),
                    $valid,
                    $newName)){
                return $this->redirectToRoute('user', array("ok"=> '100'));    
            }else{
                return $this->redirectToRoute('user', array("error"=> '505'));
            }

            
        }
    }
    /**
     * @Route("/delete/{id}", name="delete", requirements={"id"="\d+"})
     */
    public function deleteAction($id) 
    {
        
        
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('login');
        }else{

            ImageClean::delImgFromGall($id);

            if(ImagesRepository::delete($id)){
                return $this->redirectToRoute('user', array("ok"=> '100'));
            }else{
                return $this->redirectToRoute('user', array("error"=> '404'));
            }
        }
    }
    /**
     * @Route("/edit/{id}", name="edit", requirements={"id"="\d+"})
     */
    public function editAction($id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('login');
        }else{
            if($image=ImagesRepository::get($id)){
                return $this->render('user/edit.html.twig', array("images" => $image));
            }else{
                return $this->redirectToRoute('user', array("error"=> '404'));
            }
        }
    }
    /**
     * @Route("/update/{id}", name="update", requirements={"id"="\d+"})
     */
    public function updateAction(Request $request, $id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('login');
        }else{
            $em = $this->getDoctrine()->getManager();
            $image = $em->getRepository(Images::class)->find($id);
            if(!$image){
                return $this->redirectToRoute('user', array("error"=> '404'));
            }
            $image->setTitle($request->query->get("title"));
            $image->setDescription($request->query->get("desc"));
            $image->setImage_url($request->query->get("url"));
            $em->flush();
            
            return $this->redirectToRoute('user', array("ok" => '100'));
            
        }
    }

}
