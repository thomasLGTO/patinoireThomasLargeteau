<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function show(Category $category, CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $category,
            'tips'=>$category->getTips(),
            'categories' => $categoryRepository->findBy(
                [],
                ['nameCategory' => 'ASC']
            ),
        ]);
    }
}
