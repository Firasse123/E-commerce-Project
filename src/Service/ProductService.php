<?php
// src/Service/ProductService.php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProductService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository $productRepository,
        private string $uploadDirectory
    ) {}

    /**
     * Sauvegarde un produit avec upload d'image
     */
    public function saveProduct(Product $product, ?UploadedFile $imageFile = null): void
    {
        if ($imageFile) {
            $imageName = $this->uploadImage($imageFile);
            $product->setImage($imageName);
        }

        $product->setUpdatedAt(new \DateTimeImmutable());
        
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    /**
     * Upload une image de produit
     */
    private function uploadImage(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        $file->move($this->uploadDirectory, $fileName);

        return $fileName;
    }

    /**
     * Recherche de produits avec filtres
     */
    public function searchProducts(array $filters = []): array
    {
        $queryBuilder = $this->productRepository->findActiveProducts();

        if (!empty($filters['search'])) {
            $queryBuilder = $this->productRepository->searchProducts($filters['search']);
        }

        if (!empty($filters['category'])) {
            $queryBuilder->andWhere('p.category = :category')
                         ->setParameter('category', $filters['category']);
        }

        if (!empty($filters['minPrice'])) {
            $queryBuilder->andWhere('p.price >= :minPrice')
                         ->setParameter('minPrice', $filters['minPrice']);
        }

        if (!empty($filters['maxPrice'])) {
            $queryBuilder->andWhere('p.price <= :maxPrice')
                         ->setParameter('maxPrice', $filters['maxPrice']);
        }

        if (!empty($filters['inStock'])) {
            $queryBuilder->andWhere('p.stock > 0');
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Met à jour le stock d'un produit
     */
    public function updateStock(Product $product, int $newStock): void
    {
        $product->setStock($newStock);
        $product->setUpdatedAt(new \DateTimeImmutable());
        
        $this->entityManager->flush();
    }
}