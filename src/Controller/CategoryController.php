<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
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

    /**
     * @Route("/{id}", name="category_show", methods={"GET"})
     */
    public function show(Category $category, CategoryRepository $categoryRepository,PaginatorInterface $paginator,Request $request): Response
    {
        $pagination = $paginator->paginate(
            $category->getTips(), /* query NOT result */
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
            'user'=>$user
        ]);
    }
}
