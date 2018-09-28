<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use League\Csv\Reader;
use Carbon\Carbon;
use Psr\Log\LoggerInterface;
use AppBundle\Entity\Images;
use AppBundle\Entity\ImagesRepository;
use AppBundle\Utils\Csv;
use AppBundle\Utils\ImageClean;

class AdminController extends Controller
{
    private $csvFile;
    /**
     * @Route("/admin", name="show")
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
                    case '500':
                        $messages[]=["text"=>"Las Imágenes no han sido eliminadas."];
                        break;
                    case '501':
                        $messages[]=["text"=>"El archivo no se ha podido descargar"];
                        break;
                    case '502':
                        $messages[]=["text"=>"El archivo no se ha Importado"];
                        break;
                    case '503':
                        $messages[]=["text"=>"Imagen no encontrada"];
                        break;
                    case '100':
                        $messages[]=["text"=>"Operación no ejecutada"];
                        break;
                }
            }
            $ok=$request->query->get('ok');
            switch ($ok) {
                case '100':
                    $messages[]=["text"=>"Operación ejecutada con éxito"];
                    break;
                case '101':
                    $messages[]=["text"=>"Archivo Descargado con exito"];
                    $messages[]=["text"=>"Archivo Importado con exito"];
                    break;
                default:
                    # code...
                    break;
            }
            return $this->render('admin/list.html.twig', array("images" => ImagesRepository::sortByName(), "messages"=>$messages, "msg_type" => $msg_type));
        }
        
    }
    /**
     * @Route("/admin/import", name="import")
     * @Method("POST")
     */
    public function importAction(Request $request){
        $messages=array();

        if($url = $request->request->get('url')){
            $url=$this->getParameter('csv_url');
        }

        Csv::init();
        if($csvFile = Csv::download($url)){
            
            if(Csv::import($csvFile)){
                
                return $this->redirectToRoute('admin', array("ok"=> '101'));
            }else{
                
                return $this->redirectToRoute('admin', array("error"=> '502'));
            }
        }else{
            
            return $this->redirectToRoute('admin', array("error"=> '501'));
        }
    }

    /**
     * @Route("/admin/export", name="export")
     */
    public function exportAction(){
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('login');
        }else{
            $fname=Csv::export(ImagesRepository::sortByName());
            $response = new BinaryFileResponse($fname);
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
            return $response;
        }
    }
    /**
     * @Route("/admin/clean", name="clean")
     */
    public function cleanAction(){
        
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('login');
        }else{
            ImageClean::cleanAll(ImagesRepository::getFilesName());
            if(ImagesRepository::cleanImages()){

                return $this->redirectToRoute('admin', array("ok"=> '100'));
            }else{
                
                return $this->redirectToRoute('admin', array("error"=> '500'));
            }
        }
    }
    /**
     * @Route("/aprove/{id}", name="aprove", requirements={"id"="\d+"})
     */
    public function aproveAction($id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('login');
        }else{
            if(ImagesRepository::aprove($id)){
                return $this->redirectToRoute('admin', array("ok"=> '100'));
            }else{
                return $this->redirectToRoute('admin', array("error"=> '503'));   
            }
        }
    }
}