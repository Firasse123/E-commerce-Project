<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;


class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Trouve les produits actifs avec pagination
     */
    public function findActiveProducts(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('p.createdAt', 'DESC');
    }


    public function findBySearchAndCategory(?string $searchTerm, ?Category $category): array
{
    $qb = $this->createQueryBuilder('p');

    if ($searchTerm) {
        $qb->andWhere('p.name LIKE :searchTerm')
           ->setParameter('searchTerm', '%' . $searchTerm . '%');
    }

    if ($category) {
        $qb->andWhere('p.category = :category')
           ->setParameter('category', $category);
    }

    return $qb->getQuery()->getResult();
}


    /**
     * Recherche de produits par nom ou description
     */
    public function searchProducts(string $query): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isActive = :active')
            ->andWhere('p.name LIKE :query OR p.description LIKE :query')
            ->setParameter('active', true)
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('p.name', 'ASC');
    }

    /**
     * Filtre par catégorie
     */
    public function findByCategory(Category $category): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.category = :category')
            ->andWhere('p.isActive = :active')
            ->setParameter('category', $category)
            ->setParameter('active', true)
            ->orderBy('p.name', 'ASC');
    }

    /**
     * Filtre par prix
     */
    public function findByPriceRange(float $minPrice, float $maxPrice): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isActive = :active')
            ->andWhere('p.price BETWEEN :minPrice AND :maxPrice')
            ->setParameter('active', true)
            ->setParameter('minPrice', $minPrice)
            ->setParameter('maxPrice', $maxPrice)
            ->orderBy('p.price', 'ASC');
    }

    /**
     * Produits en stock
     */
    public function findInStock(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isActive = :active')
            ->andWhere('p.stock > 0')
            ->setParameter('active', true)
            ->orderBy('p.stock', 'DESC');
    }

    /**
     * Produits similaires (même catégorie)
     */
    public function findSimilarProducts(Product $product, int $limit = 4): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.category = :category')
            ->andWhere('p.isActive = :active')
            ->andWhere('p.id != :currentProduct')
            ->setParameter('category', $product->getCategory())
            ->setParameter('active', true)
            ->setParameter('currentProduct', $product->getId())
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Produits les plus récents
     */
    public function findLatestProducts(int $limit = 8): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findLowStock(int $threshold = 10): array
{
    return $this->createQueryBuilder('p')
        ->where('p.stock < :threshold')
        ->setParameter('threshold', $threshold)
        ->getQuery()
        ->getResult();
}
}
