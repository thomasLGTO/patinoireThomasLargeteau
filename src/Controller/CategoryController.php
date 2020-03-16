<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Repository\TipsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/categories")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/", name="category_index", methods={"GET"})
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findBy(
                [],
                ['nameCategory' => 'ASC']
            ),
            'name'=>'Categories',
            'picture'=>'category'
        ]);
    }

    
    public function listCategory(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/boxCategory.html.twig', [
            'categories' => $categoryRepository->findBy(
                [],
                ['nameCategory' => 'ASC']
            ),
        ]);
    }

    /**
     * @Route("/{id}", name="category_show", methods={"GET"})
     */
    public function show(Category $category, CategoryRepository $categoryRepository, TipsRepository $tipsRepository, PaginatorInterface $paginator,Request $request): Response
    {
        $pagination = $paginator->paginate(
            $tipsRepository->findBy(
                ['category'=> $category]
            ), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            2 /*limit per page*/
        );
        // $test=$pagination->getitems()[0];
        $descendingOrderUsers = $paginator->paginate(
            $tipsRepository->findBy(
                ['category'=> $category],
                ['numberUsers'=>'desc']
            ), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            2 /*limit per page*/
        );
        $descendingOrderDate = $paginator->paginate(
            $tipsRepository->findBy(
                ['category'=> $category],
                ['createdAt'=>'desc']
            ), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            2 /*limit per page*/
        );
        $user=$this->getUser();
        return $this->render('category/show.html.twig', [
            'category' => $category,
            'categories' => $categoryRepository->findBy(
                [],
                ['nameCategory' => 'ASC']
            ),
            'pagination' => $pagination,
            'descendingOrderUsers'=>$descendingOrderUsers,
            'descendingOrderDate'=>$descendingOrderDate,
            'user'=>$user
        ]);
    }

    
}
