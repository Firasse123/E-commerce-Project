<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        Request $request
    ): Response {
        $search = $request->query->get('search');
        $categoryId = $request->query->get('category');
        
        $products = $productRepository->findBySearchAndCategory($search, $categoryId);
        $categories = $categoryRepository->findAll();
        
        return $this->render('product/index.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'current_search' => $search,
            'current_category' => $categoryId,
        ]);
    }
}