<?php

namespace App\Controller\Admin;


use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/admin/stats', name: 'admin_stats_')]
class AdminstatsController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(
        OrderRepository $orderRepository,
        ProductRepository $productRepository,
        UserRepository $userRepository
    ): Response {
        // Statistiques des commandes
        $orderStats = $orderRepository->getOrderStats();
        
        // Statistiques par mois (12 derniers mois)
        $monthlyStats = $this->getMonthlyStats($orderRepository);
        
        // Top produits les plus vendus
        $topProducts = $productRepository->getTopSellingProducts(10);
        
        // Évolution des inscriptions utilisateurs
        
        // Statistiques du chiffre d'affaires par jour (30 derniers jours)
        $dailyRevenue = $this->getDailyRevenueStats($orderRepository);

        return $this->render('admin/stats/index.html.twig', [
            'orderStats' => $orderStats,
            'monthlyStats' => $monthlyStats,
            'topProducts' => $topProducts,
            'dailyRevenue' => $dailyRevenue,
        ]);
    }
private function getMonthlyStats(OrderRepository $orderRepository): array
{
    $connection = $orderRepository->getEntityManager()->getConnection();

    $sql = "
        SELECT 
            YEAR(created_at) AS year, 
            MONTH(created_at) AS month, 
            COUNT(id) AS orders, 
            SUM(total_price) AS revenue
        FROM `order`
        WHERE created_at >= :date
        GROUP BY year, month
        ORDER BY year ASC, month ASC
    ";

    $stmt = $connection->prepare($sql);
    $result = $stmt->executeQuery([
        'date' => (new \DateTime('-12 months'))->format('Y-m-d H:i:s'),
    ])->fetchAllAssociative();

    return $result;
}





 private function getDailyRevenueStats(OrderRepository $orderRepository): array
{
    $connection = $orderRepository->getEntityManager()->getConnection();

    $sql = "
        SELECT DATE(created_at) AS date, SUM(total_price) AS revenue, COUNT(id) AS orders
        FROM `order`
        WHERE created_at >= :date
        GROUP BY date
        ORDER BY date ASC
    ";

    $stmt = $connection->prepare($sql);
    $result = $stmt->executeQuery([
        'date' => (new \DateTime('-30 days'))->format('Y-m-d H:i:s'),
    ])->fetchAllAssociative();

    return $result;
}

}
