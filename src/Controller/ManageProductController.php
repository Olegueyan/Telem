<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\Type\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use function Symfony\Component\Clock\now;

class ManageProductController extends AbstractController
{
    /**
     * @throws \DateMalformedStringException
     */
    #[Route('/manage/product/new', 'manage_product_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        $form->add('addProduct', SubmitType::class, [
            'label' => "Ajouter",
            'attr' => ['class' => 'Button -no-danger -reverse']
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $product->setCreatedAt(now());

            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Le produit a été ajouté au catalogue');

            return $this->redirectToRoute('app_product_all');
        }

        return $this->render('product/product_new.html.twig', [
            'formulaire_ajout' => $form->createView()
        ]);
    }

    #[Route('/manage/product/edit/{id}', 'manage_product_edit', requirements: ['id' => '\d+'])]
    public function edit(Product $product, Request $request, EntityManagerInterface $em): Response
    {
//        $product = $em->getRepository(Product::class)->find($id);
//
//        if (!$product)
//        {
//            throw $this->createNotFoundException("Aucun produit avce l'id $id");
//        }

        $form = $this->createForm(ProductType::class, $product);

        $form->add('updateProduct', SubmitType::class, [
            'label' => "Modifier",
            'attr' => ['class' => 'Button -no-danger -reverse']
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em->flush();

            $this->addFlash('success', 'Le produit a été mis à jour');

            return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
        }

        return $this->render('product/product_new.html.twig', [
            'product' => $product,
            'formulaire_ajout' => $form->createView()
        ]);
    }

    #[Route('/manage/product/delete/{id}', 'manage_product_delete', requirements: ['id' => '\d+'])]
    public function delete(Product $product, EntityManagerInterface $em, Request $request): Response
    {
        $submittedToken = $request->request->get('token');

        if (!$this->isCsrfTokenValid('delete-product', $submittedToken))
        {
            $em->remove($product);
            $em->flush();

            $this->addFlash('success', 'Le produit '.$product->getId().' a été supprimé');
        }
        else
        {
            $this->addFlash('error', 'Le token pour la suppression du produit est invalide');
            $this->redirectToRoute('manage_product_edit', ['id' => $product->getId()]);
        }

        return $this->redirectToRoute('app_product_all');
    }

    #[Route('/manage/product/delete-confirm/{id}', 'manage_product_delete_confirm', requirements: ['id' => '\d+'])]
    public function deleteConfirm(Product $product): Response
    {
        return $this->render('product/product_delete_confirm.html.twig', [
            'product' => $product
        ]);
    }
}