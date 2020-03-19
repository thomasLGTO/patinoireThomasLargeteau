<?php

namespace App\Controller;

use App\Form\SearchBarType;
use App\Repository\TipsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index(Request $request, TipsRepository $tipsRepository, PaginatorInterface $paginator)
    {
        $pagination = $paginator->paginate(
            $tipsRepository->findBy(
                [],
                ['createdAt'=>'desc'],
                5 /* the first hundred*/ 
            ), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        $user=$this->getUser();
        return $this->render('main/index.html.twig', [
            'name' => 'Accueil',
            'picture'=>'seul',
            'pagination'=>$pagination,
            'user'=>$user
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
     * @Route("/recherche", name="search")
     */
    public function newsearch(Request $request,TipsRepository $tipsRepository , PaginatorInterface $paginator): Response
    {
        $tips = $tipsRepository->findBy(
            [],
            ['numberUsers'=>'desc']
        );
        // dump($tips[0]->getKeywords());
        // dump($request->request->get('Recherche'));
        $wordsSearchedChecked = $request->request->get('Recherche');
        $wordsSearched=htmlspecialchars($wordsSearchedChecked);
        $sortTips =[];
        if (empty($wordsSearchedChecked) == false ){
            for ($i=0;$i<count($tips);$i++){
                if(stristr($tips[$i]->getKeywords(), $wordsSearched)) {
                    $sortTips[]=$tips[$i];
                }
            }
        }
        $pagination = $paginator->paginate(
            $sortTips, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            100 /*limit per page*/
        );
        $user=$this->getUser();
        return $this->render('main/search.html.twig', [
            'name' => 'Résultat de la recherche',
            'picture'=>'search',
            'pagination'=>$pagination,
            'user'=>$user
        ]);
    }

    /**
     * @Route("/top100", name="top100")
     */
    public function top100(Request $request,TipsRepository $tipsRepository , PaginatorInterface $paginator): Response
    {
        
        
        $pagination = $paginator->paginate(
            $tipsRepository->findBy(
                [],
                ['numberUsers'=>'desc'],
                100 /* the first hundred*/ 
            ), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        $user=$this->getUser();
        return $this->render('main/search.html.twig', [
            'name' => 'Top 100',
            'picture'=>'top100',
            'pagination'=>$pagination,
            'user'=>$user
        ]);
    }
}
