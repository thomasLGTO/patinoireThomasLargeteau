<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index()
    {
        return $this->render('main/index.html.twig', [
            'name' => 'Accueil',
            'picture'=>'pictureHome'
        ]);
    }


    /**
     * @Route("/concept", name="concept")
     */
    public function concept()
    {
        return $this->render('main/concept.html.twig', [
            'name' => 'Le concept',
            'picture'=>'concept'
        ]);
    }
}
