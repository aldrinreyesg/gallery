<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\ImagesRepository;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function homeAction()
    {
        
        $images=ImagesRepository::sortByNameValid();
        $response = $this->render('default/index.html.twig', array("images" => $images));
        $response->setSharedMaxAge(3600);
        $response->headers->addCacheControlDirective('must-revalidate', true);
        return $response;
    }
    /**
     * @Route("/image/{id}", name="image", requirements={"id"="\d+"})
     */
    public function imageAction($id)
    {
        
        $images=ImagesRepository::get($id);
        $response = $this->render('default/image_view.html.twig', array("image" => $images));
        $response->setSharedMaxAge(3600);
        $response->headers->addCacheControlDirective('must-revalidate', true);
        return $response;
    }
}
