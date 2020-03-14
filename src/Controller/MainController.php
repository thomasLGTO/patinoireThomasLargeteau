<?php

namespace App\Controller;

use App\Form\SearchBarType;
use App\Repository\TipsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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


    /**
     * @Route("/recherche", name="recherche")
     */
    public function newsearch(Request $request,TipsRepository $tipsRepository): Response
    {
        $tips = $tipsRepository->findAll();
        // dump($tips[0]->getKeywords());
        // dump($request->request->get('Recherche'));
        $wordsSearched = $request->request->get('Recherche');
        $sortTips =[];
        for ($i=0;$i<count($tips);$i++){
            if(stristr($tips[$i]->getKeywords(), $wordsSearched)) {
                $sortTips[]=$tips[$i];
            }
        }
        return $this->render('searchbar.html.twig', [
            'name' => 'Accueil',
            'picture'=>'pictureHome',
            'sortTips'=>$sortTips
        ]);
    }
}
