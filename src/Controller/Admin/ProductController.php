<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/products')]
#[IsGranted('ROLE_ADMIN')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'admin_product_index')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->render('admin/product/index.html.twig', [
            'products' => $products,
        ]);
    }
    
#[Route('/new', name: 'admin_product_new')]
public function new(
    Request $request, 
    EntityManagerInterface $entityManager,
    SluggerInterface $slugger
): Response {
    $product = new Product();
    $form = $this->createForm(ProductType::class, $product);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form->get('imageFile')->getData();

        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

            try {
                $uploadsDirectory = $this->getParameter('images_directory');
                if (!is_dir($uploadsDirectory)) {
                    mkdir($uploadsDirectory, 0755, true);
                }

                $imageFile->move($uploadsDirectory, $newFilename);
                $product->setImage($newFilename);

                $this->addFlash('info', 'Image uploadée: ' . $newFilename);
            } catch (FileException $e) {
                $this->addFlash('error', 'Erreur lors du téléchargement de l\'image: ' . $e->getMessage());
            }
        }

        $product->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->persist($product);
        $entityManager->flush();

        $this->addFlash('success', 'Produit créé avec succès!');
        return $this->redirectToRoute('admin_product_index');
    }

    // Form submitted but not valid
    if ($form->isSubmitted() && !$form->isValid()) {
        $this->addFlash('error', 'Le formulaire contient des erreurs. Veuillez les corriger.');
    }

        return $this->render('admin/product/new.html.twig', [
        'product' => $product,
        'form' => $form->createView(),
    ]);
}


    #[Route('/{id}', name: 'admin_product_show', requirements: ['id' => '\d+'])]
    public function show(Product $product): Response
    {
        return $this->render('admin/product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_product_edit', requirements: ['id' => '\d+'])]
    public function edit(
        Request $request, 
        Product $product, 
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image file upload
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $uploadsDirectory = $this->getParameter('images_directory');
                    if (!is_dir($uploadsDirectory)) {
                        mkdir($uploadsDirectory, 0755, true);
                    }

                    $imageFile->move($uploadsDirectory, $newFilename);
                    $product->setImage($newFilename);
                    
                    // Debug: Add flash message to confirm image processing
                    $this->addFlash('info', 'Image uploadée: ' . $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image: ' . $e->getMessage());
                    // Don't return here, continue with saving the product
                }
            }

            // Update timestamp
            $product->setUpdatedAt(new \DateTimeImmutable());

            $entityManager->flush();

            $this->addFlash('success', 'Produit modifié avec succès!');
            return $this->redirectToRoute('admin_product_index');
        }

        return $this->render('admin/product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_product_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $submittedToken = $request->request->get('_token');
        $tokenId = 'delete' . $product->getId();  // This should match your Twig: 'delete' ~ product.id
        
        if ($this->isCsrfTokenValid($tokenId, $submittedToken)) {
            try {
                $entityManager->remove($product);
                $entityManager->flush();
                
                $this->addFlash('success', 'Produit supprimé avec succès!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur: ' . $e->getMessage());
            }
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('admin_product_index');
    }

    #[Route('/{id}/toggle-status', name: 'admin_product_toggle_status', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function toggleStatus(Product $product, EntityManagerInterface $entityManager): Response
    {
        $product->setIsActive(!$product->isActive());
        $product->setUpdatedAt(new \DateTimeImmutable());
        $entityManager->flush();

        $status = $product->isActive() ? 'activé' : 'désactivé';
        $this->addFlash('success', "Produit {$status} avec succès!");

        return $this->redirectToRoute('admin_product_index');
    }
}