<?php
// src/Repository/OrderRepository.php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }


    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.user = :user')
            ->setParameter('user', $user)
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Statistiques pour l'admin
     */
  public function getOrderStats(): array
{
    $totalOrders = $this->createQueryBuilder('o')
        ->select('COUNT(o.id)')
        ->getQuery()
        ->getSingleScalarResult();

    $totalRevenue = $this->createQueryBuilder('o')
        ->select('SUM(o.totalPrice)')
        ->getQuery()
        ->getSingleScalarResult();

    $todayOrders = $this->createQueryBuilder('o')
        ->select('COUNT(o.id)')
        ->andWhere('o.createdAt >= :todayStart')
        ->andWhere('o.createdAt < :tomorrowStart')
        ->setParameter('todayStart', new \DateTime('today'))
        ->setParameter('tomorrowStart', new \DateTime('tomorrow'))
        ->getQuery()
        ->getSingleScalarResult();

    return [
        'totalOrders' => $totalOrders,
        'totalRevenue' => $totalRevenue ?? 0,
        'todayOrders' => $todayOrders
    ];
}


    /**
     * Commandes récentes pour l'admin
     */
    public function findRecentOrders(int $limit = 10): array
    {
        return $this->createQueryBuilder('o')
            ->join('o.user', 'u')
            ->addSelect('u')
            ->orderBy('o.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}