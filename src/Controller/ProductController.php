<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/product/all', name: 'app_product_all')]
    public function showAll(EntityManagerInterface $em): Response
    {
        $products = $em->getRepository(Product::class)->findAll();

        return $this->render('product/product_all.html.twig', [
            'products' => $products
        ]);
    }

    #[Route('/product/{id}', name: 'app_product_show', requirements: ['id' => '\d+'])]
    public function show(/* EntityManagerInterface $em, */ Product $product): Response
    {
//        $product = $em->getRepository(Product::class)->find($id);
//
//        if ($product === null)
//        {
//            throw new NotFoundHttpException("Ce produit n'existe pas !");
//        }

        return $this->render('product/product.html.twig', [
            'product' => $product
        ]);
    }

    #[Route('/product/search', name: 'app_product_search', methods: ['POST'])]
    public function search(Request $request, ProductRepository $repository): Response
    {
        $keywords = $request->request->get('searchProduct');

        $products = $repository->search($keywords);
        $numberOfResults = count($products);

        return $this->render('product/product_all.html.twig', [
            "products" => $products,
            "number_of_results" => $numberOfResults
        ]);
    }
}